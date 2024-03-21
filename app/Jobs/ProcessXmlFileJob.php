<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\ProductsImportedEmail;
use App\Models\Product;
use XMLReader;

class ProcessXmlFileJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $file;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(string $file)
    {
        $this->file = $file;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(
        Product $product,
        XMLReader $xmlReader,
        DB $db,
        Log $log, 
        Mail $mail,
        ProductsImportedEmail $productsImportedEmail
    ) {
        $file = $this->file;

        if (! file_exists($file)) {
            $error = 'File not found.';
            Log::error($error);
            throw new \Exception($error);
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

        $reader = new XMLReader;
        $success = $reader->open($file);

        if (!$success) {
            $error = 'Error loading XML file.';
            Log::error($error);
            throw new \Exception($error);
        }

        DB::beginTransaction();

        try {
            $productsWithNoStock = [];
            while ($reader->read()) {
                if ($reader->nodeType == XMLReader::ELEMENT && $reader->name == 'ExportData') {
                    $xml = new \SimpleXMLElement($reader->readOuterXML());
                    $product = Product::create([
                        'code' => (string) $xml->code,
                        'cat' => (string) $xml->cat,
                        'name' => (string) $xml->name,
                        'price_ex_vat' => (float) $xml->price_ex_vat,
                        'price_inc_vat' => (float) $xml->price_inc_vat,
                        'stock' => (int) $xml->stock,
                        'short_desc' => (string) $xml->short_desc,
                    ]);
                    if ($product->stock === 0) {
                        $productsWithNoStock[] = $product;
                    }
                }
            }
            DB::commit();
            if(count($productsWithNoStock) > 0) {
                $productsImportedEmail = new ProductsImportedEmail();
                $productsImportedEmail->products = $productsWithNoStock;
                Mail::to(env('PRODUCT_MAIL_TO'))->send($productsImportedEmail);
            }
        } catch (\Exception $e) {
            Log::error('Error importing products: ' . print_r($e, true));
            throw new \Exception('Error importing products: ' . $e->getMessage());
        }
    }
}
