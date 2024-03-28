@extends('layouts.dashboard')
@section('title', 'Dashboard - Invoice')
@section('content')
    <div class="card">
        <div class="card-header">
            Invoice
        </div>
        <div class="card-body">
            <a href="/dashboard/sale/pdf" class="btn btn-primary" target="_blank">Export PDF</a>
            <div>Customer Name: {{ $data['name'] }}</div>
            <div>Customer Address: {{ $data['address'] }}</div>
            <div>Customer Phone: {{ $data['phone'] }}</div>
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">Product Name</th>
                        <th scope="col">Quantity</th>
                        <th scope="col">Price</th>
                        <th scope="col">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($detailSale as $item)
                        <tr>
                            <th>{{ $item['name'] }}</th>
                            <td>{{ $item['quantity'] }}</td>
                            <td>Rp{{ number_format($item['price'], 0, ',' . '.') }}</td>
                            <td>Rp{{ number_format($item['subtotal'], 0, ',' . '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div>Total Price: Rp{{ number_format($totalPrice, 0, ',' . '.') }}</div>
            <a href="#" class="btn btn-primary">Cancel</a>
            <form action="{{ route('sale.store') }}" method="post">
                @csrf
                <button href="#" class="btn btn-primary" type="submit">Confirm</button>
            </form>
        </div>
    </div>
@endsection
