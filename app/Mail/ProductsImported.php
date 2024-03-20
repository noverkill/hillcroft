<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ProductsImported extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $products;

    public function __construct($products)
    {
        $this->products = $products;
    }

    public function build() : ProductsImported
    {
        return $this->subject('Products Imported')
                    ->view('emails.products_imported');
    }
}
