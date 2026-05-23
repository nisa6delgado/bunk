<?php

namespace App\Filament\Resources\Products\Pages;

use App\Filament\Resources\Products\ProductResource;
use App\Models\Product;
use App\Models\Invoice;
use Filament\Actions\CreateAction;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListProducts extends ListRecords
{
    protected static string $resource = ProductResource::class;

    public $credit = null;
    public $customer = null;
    public $paid = null;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('delete')
                ->label('Borrar factura')
                ->icon('heroicon-o-trash')
                ->visible(session()->get('products') ? true : false)
                ->action(function () {
                    session()->forget('products');
                    return redirect('/products');
                }),

            Action::make('invoice')
                ->label('Ver factura')
                ->icon('heroicon-o-document-currency-dollar')
                ->modalContent(function () {
                    return view('invoices.preview');
                })
                ->modalSubmitAction(false)
                ->modalCancelAction(false)
                ->visible(session()->get('products') ? true : false),

            CreateAction::make()
                ->icon('heroicon-o-plus'),
        ];
    }

    public function delete($index)
    {
        $products = session()->get('products');
        array_splice($products, $index, 1);
        session()->put('products', $products);
    }

    public function save()
    {
        if ($this->credit && ! $this->customer) {
            return Notification::make()
                ->title('Debe indicar el nombre del cliente')
                ->danger()
                ->send();
        }

        $amount = 0;

        foreach (session()->get('products') as $item) {
            $product = Product::find($item['product']);

            $quantity = $product->quantity - $item['quantity'];
            $product->update(['quantity' => $quantity]);

            $amount += $product->selling_price;
        }
        
        $paid = $this->paid ?? $amount;

        Invoice::create([
            'amount' => $amount,
            'paid' => $paid,
            'credit' => $this->credit,
            'customer' => $this->customer,
            'products' => session()->get('products'),
        ]);

        session()->forget('products');

        Notification::make()
            ->title('Factura registrada')
            ->success()
            ->send();

        return redirect('/products');
    }
}
