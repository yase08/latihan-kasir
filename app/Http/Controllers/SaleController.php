<?php

namespace App\Http\Controllers;

use App\Exports\SaleExport;
use App\Models\Customer;
use App\Models\DetailSale;
use App\Models\Product;
use App\Models\Sale;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class SaleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sales = Sale::all();
        $detailSales = DetailSale::with('product', 'sale')->get();
        return view('pages.sale.index', compact('sales', 'detailSales'));
    }

    public function export()
    {
        return Excel::download(new SaleExport, 'sales.xlsx');
    }

    public function show($id)
    {
    }

    public function invoice(Request $request)
    {
        session(['data' => $request->all()]);
        return redirect('/dashboard/sale/invoice');
    }

    public function invoiceView()
    {
        $data = session('data');
        $totalPrice = 0;
        $detailSale = [];

        foreach ($data['products'] as $productId) {
            $product = Product::find($productId);
            $index = array_search($productId, $data['products']);
            $totalPrice += $product->price * $data['quantities'][$index];
        }

        foreach ($data['products'] as $productId) {
            $product = Product::find($productId);
            $index = array_search($productId, $data['products']);
            $detailSale[] = [
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => $data['quantities'][$index],
                'subtotal' => $product->price * $data['quantities'][$index],
            ];
        }
        return view('pages.sale.invoice', compact('totalPrice', 'data', 'detailSale'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $products = Product::where('stock', '>', 0)->get();
        return view('pages.sale.create', compact('products'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store()
    {
        $data = session('data');
        $totalPrice = 0;

        foreach ($data['products'] as $productId) {
            $product = Product::find($productId);
            $index = array_search($productId, $data['products']);
            $totalPrice += $product->price * $data['quantities'][$index];
        }

        $newCustomer = Customer::create([
            'name' => $data['name'],
            'phone' => $data['phone'],
            'address' => $data['address'],
        ]);

        $newSale = Sale::create([
            'customer_id' => $newCustomer->id,
            'sale_date' => date('Y-m-d'),
            'total_price' => $totalPrice,
            'user_id' => Auth::user()->id
        ]);

        foreach ($data['products'] as $productId) {
            $product = Product::findOrFail($productId);
            $product->update([
                'stock' => $product->stock - $data['quantities'][array_search($productId, $data['products'])],
            ]);
        }

        foreach ($data['products'] as $productId) {
            $product = Product::findOrFail($productId);
            DetailSale::create([
                'sale_id' => $newSale->id,
                'quantity' => $data['quantities'][$index],
                'product_id' => $product->id,
                'subtotal' => $product->price * $data['quantities'][array_search($productId, $data["products"])]
            ]);
        }

        return redirect('/dashboard/sale')->with('success', 'Sale created successfully');
    }

    public function download($saleId)
    {
        $sale = Sale::with('detailSales.product')->find($saleId);
        $totalPrice = 0;

        $totalPrice = $sale->detailSales->reduce(function ($carry, $detail) {
            return $carry + ($detail->product->price * $detail->quantity);
        });

        $detailSale = $sale->detailSales->map(function ($detail) {
            return [
                'name' => $detail->product ? $detail->product->name : 'N/A',
                'price' => $detail->product->price,
                'quantity' => $detail->quantity,
                'subtotal' => $detail->product->price * $detail->quantity,
            ];
        })->toArray();

        $pdf = Pdf::loadView('pages.sale.pdf', compact('totalPrice', 'sale', 'detailSale', 'totalPrice'));

        return $pdf->download("invoice-{$saleId}.pdf");
    }

    public function pdf()
    {
        $data = session('data');
        $totalPrice = 0;
        $detailSale = [];

        foreach ($data['products'] as $productId) {
            $product = Product::find($productId);
            $index = array_search($productId, $data['products']);
            $totalPrice += $product->price * $data['quantities'][$index];
        }

        foreach ($data['products'] as $productId) {
            $product = Product::find($productId);
            $index = array_search($productId, $data['products']);
            $detailSale[] = [
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => $data['quantities'][$index],
                'subtotal' => $product->price * $data['quantities'][$index],
            ];
        }

        $pdf = Pdf::loadView('pages.sale.pdf', compact('totalPrice', 'data', 'detailSale'));

        return $pdf->download("invoice.pdf", compact('totalPrice', 'data', 'detailSale'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $sale = Sale::find($id);
        return view('pages.sale.edit', compact('sale'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $sale = Sale::find($id);

        $sale->update([
            'name' => $request->name,
            'price' => $request->price,
        ]);

        return redirect('/dashboard/sale')->with('success', 'Sale updated successfully');
    }

    public function updateStock(Request $request, $id)
    {
        $sale = Sale::find($id);

        $sale->update([
            'stock' => $request->stock,
        ]);

        return redirect('/dashboard/sale')->with('success', 'Sale updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $sale = Sale::find($id);
        $sale->delete();
        return redirect('/dashboard/sale')->with('success', 'Sale deleted successfully');
    }
}
