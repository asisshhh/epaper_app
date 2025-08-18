<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth; 
use App\Models\Epaper;
use App\Models\EpaperPage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class AdminController extends Controller
{
public function __construct()
{
    $this->middleware(['admin.auth', 'admin.role:super_admin,admin,editor']);
}
    public function index()
    {
        $epapers = Epaper::with('pages')->latest()->paginate(10);
        return view('admin.index', compact('epapers'));
    }

    public function create()
    {
        return view('admin.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'publication_date' => ['required', 'date'],
            'city' => ['required', 'string', 'max:100'],
            'pdf_file' => ['nullable', 'file', 'mimes:pdf', 'max:102400'],
            'page_images' => ['required', 'array'],
            'page_images.*' => ['image', 'mimes:jpeg,png,jpg', 'max:102400'],
        ]);

        DB::beginTransaction();

        try {
            $epaper = Epaper::create([
                'title' => $validated['title'],
                'publication_date' => $validated['publication_date'],
                'city' => $validated['city'],
                'total_pages' => count($validated['page_images']),
                'pdf_path' => null,
            ]);

            // Upload PDF
            if ($request->hasFile('pdf_file')) {
                $pdfPath = $request->file('pdf_file')->store('pdfs', 'public');
                $epaper->update(['pdf_path' => $pdfPath]);
            }

            // Initialize Image Manager
            $manager = new ImageManager(new Driver());

            // Upload page images and create thumbnails
            foreach ($validated['page_images'] as $index => $image) {
                $pageNumber = $index + 1;

                // Store original image
                $imagePath = $image->store("epapers/{$epaper->id}", 'public');

                // Thumbnail path setup
                $thumbnailPath = "thumbnails/{$epaper->id}/page_{$pageNumber}.jpg";
                $thumbnailFullPath = storage_path("app/public/{$thumbnailPath}");

                // Ensure directory exists
                if (!file_exists(dirname($thumbnailFullPath))) {
                    mkdir(dirname($thumbnailFullPath), 0755, true);
                }

                // Create thumbnail
                $imageData = file_get_contents($image->getRealPath());
                $imageObject = $manager->read($imageData)
                    ->resize(150, 200)
                    ->toJpeg(80);

                file_put_contents($thumbnailFullPath, $imageObject);

                // Save page record
                EpaperPage::create([
                    'epaper_id' => $epaper->id,
                    'page_number' => $pageNumber,
                    'image_path' => $imagePath,
                    'thumbnail_path' => $thumbnailPath,
                ]);
            }

            DB::commit();
            return redirect()->route('admin.index')->with('success', 'E-paper uploaded successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to upload E-paper. ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Show the form for editing the specified epaper.
     */
    public function edit(Epaper $epaper)
    {
        return view('admin.edit', compact('epaper'));
    }

    /**
     * Update the specified epaper in storage.
     */
    public function update(Request $request, Epaper $epaper)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'publication_date' => ['required', 'date'],
            'city' => ['required', 'string', 'max:100'],
            'is_active' => ['boolean'],
            'pdf_file' => ['nullable', 'file', 'mimes:pdf', 'max:102400'],
            'page_images' => ['nullable', 'array'],
            'page_images.*' => ['image', 'mimes:jpeg,png,jpg', 'max:102400'],
            'page_order' => ['nullable', 'string'],
            'deleted_pages' => ['nullable', 'string'],
        ]);

        DB::beginTransaction();

        try {
            // Update basic epaper information
            $updateData = [
                'title' => $validated['title'],
                'publication_date' => $validated['publication_date'],
                'city' => $validated['city'],
                'is_active' => $request->has('is_active') ? (bool)$validated['is_active'] : $epaper->is_active,
            ];

            // Handle PDF file upload if provided
            if ($request->hasFile('pdf_file')) {
                // Delete old PDF if exists
                if ($epaper->pdf_path && Storage::disk('public')->exists($epaper->pdf_path)) {
                    Storage::disk('public')->delete($epaper->pdf_path);
                }

                // Store new PDF
                $pdfPath = $request->file('pdf_file')->store('pdfs', 'public');
                $updateData['pdf_path'] = $pdfPath;
            }

            // Handle deleted pages first
            if ($request->filled('deleted_pages')) {
                $deletedPageIds = explode(',', $request->deleted_pages);
                $deletedPageIds = array_filter($deletedPageIds); // Remove empty values
                
                if (!empty($deletedPageIds)) {
                    $pagesToDelete = $epaper->pages()->whereIn('id', $deletedPageIds)->get();
                    
                    // Delete files for deleted pages
                    foreach ($pagesToDelete as $page) {
                        if (Storage::disk('public')->exists($page->image_path)) {
                            Storage::disk('public')->delete($page->image_path);
                        }
                        if (Storage::disk('public')->exists($page->thumbnail_path)) {
                            Storage::disk('public')->delete($page->thumbnail_path);
                        }
                    }
                    
                    // Delete page records
                    $epaper->pages()->whereIn('id', $deletedPageIds)->delete();
                }
            }

            // Handle page reordering
            if ($request->filled('page_order')) {
                $pageOrder = explode(',', $request->page_order);
                $pageOrder = array_filter($pageOrder); // Remove empty values
                
                if (!empty($pageOrder)) {
                    foreach ($pageOrder as $index => $pageId) {
                        $page = $epaper->pages()->find($pageId);
                        if ($page) {
                            $page->update(['page_number' => $index + 1]);
                        }
                    }
                }
            }

            // Handle new page images if provided (this replaces ALL existing pages)
            if ($request->hasFile('page_images')) {
                // Delete ALL existing page images and thumbnails
                foreach ($epaper->pages as $page) {
                    if (Storage::disk('public')->exists($page->image_path)) {
                        Storage::disk('public')->delete($page->image_path);
                    }
                    if (Storage::disk('public')->exists($page->thumbnail_path)) {
                        Storage::disk('public')->delete($page->thumbnail_path);
                    }
                }

                // Delete ALL existing page records
                $epaper->pages()->delete();

                // Initialize Image Manager
                $manager = new ImageManager(new Driver());

                // Upload new page images and create thumbnails
                foreach ($validated['page_images'] as $index => $image) {
                    $pageNumber = $index + 1;

                    // Store original image
                    $imagePath = $image->store("epapers/{$epaper->id}", 'public');

                    // Thumbnail path setup
                    $thumbnailPath = "thumbnails/{$epaper->id}/page_{$pageNumber}.jpg";
                    $thumbnailFullPath = storage_path("app/public/{$thumbnailPath}");

                    // Ensure directory exists
                    if (!file_exists(dirname($thumbnailFullPath))) {
                        mkdir(dirname($thumbnailFullPath), 0755, true);
                    }

                    // Create thumbnail
                    $imageData = file_get_contents($image->getRealPath());
                    $imageObject = $manager->read($imageData)
                        ->resize(150, 200)
                        ->toJpeg(80);

                    file_put_contents($thumbnailFullPath, $imageObject);

                    // Save page record
                    EpaperPage::create([
                        'epaper_id' => $epaper->id,
                        'page_number' => $pageNumber,
                        'image_path' => $imagePath,
                        'thumbnail_path' => $thumbnailPath,
                    ]);
                }

                $updateData['total_pages'] = count($validated['page_images']);
            } else {
                // If no new images uploaded, update total_pages based on remaining pages
                $remainingPagesCount = $epaper->pages()->count();
                $updateData['total_pages'] = $remainingPagesCount;
            }

            // Update the epaper
            $epaper->update($updateData);

            DB::commit();
            return redirect()->route('admin.index')->with('success', 'E-paper updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error updating e-paper: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(Epaper $epaper)
    {
        DB::beginTransaction();
        
        try {
            // Delete PDF file
            if ($epaper->pdf_path && Storage::disk('public')->exists($epaper->pdf_path)) {
                Storage::disk('public')->delete($epaper->pdf_path);
            }

            // Delete page images and thumbnails
            foreach ($epaper->pages as $page) {
                if (Storage::disk('public')->exists($page->image_path)) {
                    Storage::disk('public')->delete($page->image_path);
                }
                if (Storage::disk('public')->exists($page->thumbnail_path)) {
                    Storage::disk('public')->delete($page->thumbnail_path);
                }
            }

            // Delete page records
            $epaper->pages()->delete();
            
            // Delete epaper record
            $epaper->delete();

            DB::commit();
            return redirect()->route('admin.index')->with('success', 'E-paper deleted successfully!');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error deleting e-paper: ' . $e->getMessage());
        }
    }
}