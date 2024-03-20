<?php
namespace App\Console\Commands;

use App\Http\Controllers\ProductController;
use Illuminate\Console\Command;
use App\Models\Product;
use SimpleXMLElement;

class ImportXmlData extends Command
{
    protected $signature = 'xml:import {file}';
    protected $description = 'Import data from XML file to the database';

    public function handle(): void
    {
        try {
            $file = $this->argument('file');
            $controller = new ProductController;            
            $controller->importProductsFromXml($file);
            $this->info('Products imported successfully!');
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
    }
}