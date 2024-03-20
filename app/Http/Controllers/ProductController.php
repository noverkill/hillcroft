<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use \Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Mail\ProductsImported;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

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
            $xml = $request->file('xml_file');
            if (! $xml) {
                return redirect()->route('products.index')->with('error', 'No file was uploaded.');
            }
            $result = $this->importProductsFromXml($xml->getRealPath());
            return redirect()->route('products.index')->with('success', 'Products imported successfully!');
        } catch (\Exception $e) {
            return redirect()->route('products.index')->with('error', $e->getMessage());
        }
    }

    /**
     * Imports products from an XML file into the database.
     *
     * @param  string  $file  The path to the XML file.
     *
     * @throws \Exception   If the file does not exist, 
     *                      if there was an error loading the XML file, 
     *                      or if there was an error importing products.
     */
    public function importProductsFromXml(string $file) : void
    {
        if (!file_exists($file)) {
            Log::error('File not found.');
            throw new \Exception('File not found.');
        }
    
        // Validate the XML file against XSD schema.
        $dom = new \DOMDocument;
        $dom->load($file);
        libxml_use_internal_errors(true);
        if (!$dom->schemaValidate(resource_path('schemas/products.xsd'))) {
            $errors = libxml_get_errors();
            libxml_clear_errors();
            Log::error('Invalid XML format: ' . print_r($errors, true));
            throw new \Exception(
                'Invalid XML format: ' . $errors[0]->message . 
                ", line: " . $errors[0]->line . 
                ", column: " . $errors[0]->column . 
                ".  Please check the XML file and try again."
            );
        }

        $xml = simplexml_load_file($file);

        if (!$xml) {
            Log::error('Error loading XML file.');
            throw new \Exception('Error loading XML file.');
        }

        try {
            $productsWithNoStock = [];
            foreach ($xml->ExportData as $data) {
                $product = Product::create([
                    'code' => (string) $data->code,
                    'cat' => (string) $data->cat,
                    'name' => (string) $data->name,
                    'price_ex_vat' => (float) $data->price_ex_vat,
                    'price_inc_vat' => (float) $data->price_inc_vat,
                    'stock' => (int) $data->stock,
                    'short_desc' => (string) $data->short_desc,
                ]);
                if ($product->stock === 0) {
                    $productsWithNoStock[] = $product;
                }
            }
            if(count($productsWithNoStock) > 0) {
                Mail::to(env('PRODUCT_MAIL_TO'))->send(new ProductsImported($productsWithNoStock));
            }
        } catch (\Exception $e) {
            Log::error('Error importing products: ' . print_r($e, true));
            throw new \Exception('Error importing products: ' . $e->getMessage());
        }
    }
}
