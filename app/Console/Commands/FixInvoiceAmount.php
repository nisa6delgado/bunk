<?php

namespace App\Console\Commands;

use App\Models\Invoice;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('fix-invoice-amount')]
#[Description('Fix invoice amount')]
class FixInvoiceAmount extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $invoices = Invoice::get();

        foreach ($invoices as $invoice) {
            $total = 0;

            foreach ($invoice->products as $product) {
                $total = $total + ($product['price'] * $product['quantity']);
            }

            if ($invoice->amount == $invoice->paid) {
                $invoice->update(['paid' => $total]);    
            }
            
            $invoice->update(['amount' => $total]);
        }

        $this->info('Facturas actualizadas exitosamente');
    }
}
