<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\ProcessXmlFileJob;

class ImportXmlData extends Command
{
    protected $signature = 'xml:import {file}';
    protected $description = 'Import data from XML file to the database';

    public function handle(): void
    {
        try {
            $file = $this->argument('file');
            ProcessXmlFileJob::dispatch($file);
            $this->info('Products import enqueued successfully!');
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
    }
}