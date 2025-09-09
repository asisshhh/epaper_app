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
public function index(Request $request): View
{
    $date = $request->get('date', now()->format('Y-m-d'));
    $edition = $request->get('edition', 'Bhubaneswar');
    $page = (int) $request->get('page', 1);

    $epaper = Epaper::byCity($edition)
        ->byDate($date)
        ->active()
        ->with('pages')
        ->first();

    if (!$epaper) {
        $epaper = Epaper::byCity($edition)
            ->active()
            ->with('pages')
            ->latest('publication_date')
            ->first();

        if ($epaper) {
            $date = $epaper->publication_date->format('Y-m-d');
        }
    }

    $currentPage = null;
    if ($epaper && $epaper->pages->count() > 0) {
        $pages = $epaper->pages->sortBy('page_number')->values();
        $currentPage = $pages->get($page - 1); // 0-indexed
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

        return response()->json([
            'success' => (bool) $page,
            'page' => $page ? [
                'image_url' => $page->image_url,
                'page_number' => $page->page_number,
            ] : null,
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
    public function archive(Request $request): View
    {
        $edition = $request->get('edition', 'Bhubaneswar');
        $month = $request->get('month', now()->format('Y-m'));

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
}
