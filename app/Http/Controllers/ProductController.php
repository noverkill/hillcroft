<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Jobs\ProcessXmlFileJob;

class ProductController extends Controller
{
    public function index() : View 
    {
        $products = Product::all();
        return view('products.index', compact('products'));
    }

    /**
     * Handle the upload of XML file and import products to database.
     */
    public function upload(Request $request) : RedirectResponse
    {
        try {
            if ($request->hasFile('xml_file')) {
                $file = $request->file('xml_file')->store('xml_file');
                ProcessXmlFileJob::dispatch(storage_path('app/' . $file));
                return redirect()
                ->route('products.index')
                ->with('success', "File uploaded and processing started. " .
                                  "Processing may take a few minutes.\n" .
                                  "Please keep refreshing the page to see " .
                                  "the updated list of products.");
            } else {
                return redirect()->route('products.index')->with('error', 'No file uploaded.');
            }
        } catch (\Exception $e) {
            return redirect()->route('products.index')->with('error', $e->getMessage());
        }
    }
}
