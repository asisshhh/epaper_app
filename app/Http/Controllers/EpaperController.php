<?php

namespace App\Http\Controllers;

use App\Models\Epaper;
use App\Models\EpaperPage;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Carbon\Carbon;

class EpaperController extends Controller
{
    /**
     * Show the latest or selected epaper.
     */
    public function index(Request $request, $edition = null, $date = null, $page = null): View
    {
        // Get parameters from route or request
        $date = $date ?? $request->get('date', now()->format('Y-m-d'));
        $edition = $edition ?? $request->get('edition', 'Bhubaneswar');
        $page = (int) ($page ?? $request->get('page', 1));
        
        // Ensure page is at least 1
        $page = max(1, $page);

        $epaper = Epaper::byCity($edition)
            ->byDate($date)
            ->active()
            ->with(['pages' => function($query) {
                $query->orderBy('page_number');
            }])
            ->first();

        if (!$epaper) {
            $epaper = Epaper::byCity($edition)
                ->active()
                ->with(['pages' => function($query) {
                    $query->orderBy('page_number');
                }])
                ->latest('publication_date')
                ->first();

            if ($epaper) {
                $date = $epaper->publication_date->format('Y-m-d');
            }
        }

        $currentPage = null;
        if ($epaper && $epaper->pages->count() > 0) {
            // Ensure page doesn't exceed total pages
            $maxPage = $epaper->pages->count();
            $page = min($page, $maxPage);
            
            // Get the specific page (pages are 1-indexed)
            $currentPage = $epaper->pages->where('page_number', $page)->first();
            
            // If current page doesn't exist, get first page
            if (!$currentPage) {
                $page = 1;
                $currentPage = $epaper->pages->first();
            }
        }

        $cities = ['Bhubaneswar'];

        // Generate PDF URL
        $pdfUrl = $epaper && $epaper->pdf_path
            ? asset('storage/' . $epaper->pdf_path)
            : null;

        return view('epaper.index', compact(
            'epaper', 'currentPage', 'cities', 'date', 'edition', 'page', 'pdfUrl'
        ));
    }

    /**
     * Return a specific page of an epaper via AJAX.
     */
    public function getPage(Request $request): JsonResponse
    {
        $epaperId = (int) $request->get('epaper_id');
        $pageNumber = (int) $request->get('page_number', 1);

        $page = EpaperPage::where('epaper_id', $epaperId)
            ->where('page_number', $pageNumber)
            ->first();

        if (!$page) {
            return response()->json([
                'success' => false,
                'message' => 'Page not found',
                'page' => null,
            ]);
        }

        return response()->json([
            'success' => true,
            'page' => [
                'image_url' => $page->image_url,
                'thumbnail_url' => $page->thumbnail_url,
                'page_number' => $page->page_number,
            ],
        ]);
    }

    /**
     * Download the PDF of an epaper.
     */
    public function downloadPdf(Request $request): RedirectResponse|\Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $epaperId = (int) $request->get('epaper_id');
        $epaper = Epaper::findOrFail($epaperId);

        $pdfFullPath = storage_path('app/public/' . $epaper->pdf_path);

        if ($epaper->pdf_path && file_exists($pdfFullPath)) {
            return response()->download(
                $pdfFullPath,
                $epaper->title . '_' . $epaper->formatted_date . '.pdf'
            );
        }

        return back()->with('error', 'PDF file not found.');
    }

    /**
     * Show archive of epapers filtered by city/month.
     */
    public function archive(Request $request, $edition = null, $date = null, $page = null): View
    {
        // Handle both route parameters and query parameters
        $edition = $edition ?? $request->get('edition', 'Bhubaneswar');
        $month = $request->get('month', now()->format('Y-m'));

        // If we have a specific date and page from route, redirect to main view
        if ($date && $page) {
            return redirect()->route('epaper.index', [
                'edition' => $edition,
                'date' => $date,
                'page' => $page
            ]);
        }

        $year = substr($month, 0, 4);
        $monthNum = substr($month, 5, 2);

        $epapers = Epaper::byCity($edition)
            ->whereYear('publication_date', $year)
            ->whereMonth('publication_date', $monthNum)
            ->active()
            ->orderByDesc('publication_date')
            ->paginate(15);

        $cities = ['Bhubaneswar'];

        return view('epaper.archive', compact('epapers', 'cities', 'edition', 'month'));
    }

    /**
     * Get epaper data for AJAX requests (useful for dynamic loading)
     */
    public function getEpaperData(Request $request): JsonResponse
    {
        $date = $request->get('date', now()->format('Y-m-d'));
        $edition = $request->get('edition', 'Bhubaneswar');

        $epaper = Epaper::byCity($edition)
            ->byDate($date)
            ->active()
            ->with(['pages' => function($query) {
                $query->orderBy('page_number');
            }])
            ->first();
        

        if (!$epaper) {
            return response()->json([
                'success' => false,
                'message' => "No e-paper available for {$edition} on " . \Carbon\Carbon::parse($date)->format('Y-m-d'),
        ], 404);
        }

        $epaper->date = $epaper->date ?? \Carbon\Carbon::parse($date);

        return response()->json([
            'success' => true,
            'epaper' => [
                'id' => $epaper->id,
                'title' => $epaper->title,
                'formatted_date' => $epaper->formatted_date,
                'total_pages' => $epaper->pages->count(),
                'pages' => $epaper->pages->map(function($page) {
                    return [
                        'page_number' => $page->page_number,
                        'image_url' => $page->image_url,
                        'thumbnail_url' => $page->thumbnail_url,
                    ];
                })
            ]
        ]);
    }
}