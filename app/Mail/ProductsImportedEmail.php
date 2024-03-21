<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ProductsImportedEmail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $products;

    public function build() : ProductsImportedEmail
    {
        return $this->subject('Products Imported')
                    ->view('emails.products_imported');
    }
}
