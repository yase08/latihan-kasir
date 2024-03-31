@extends('layouts.dashboard')
@section('title', 'Dashboard - Sale')
@section('content')
    <section class="section">
        <section class="section-header">
            <h1>Sale</h1>
        </section>
        @if (session('success'))
            <div class="alert alert-success alert-dismissible show fade">
                <div class="alert-body">
                    <button class="close" data-dismiss="alert">
                        <span>&times;</span>
                    </button>
                    <b>Success:</b>
                    {{ session('success') }}
                </div>
            </div>
        @endif
        @if (session('fail'))
            <div class="alert alert-danger alert-dismissible show fade">
                <div class="alert-body">
                    <button class="close" data-dismiss="alert">
                        <span>&times;</span>
                    </button>
                    <b>Fail:</b>
                    Produk dengan kode
                    @foreach (session('fail') as $code)
                        <b>{{ $code }}</b>,
                    @endforeach
                    tidak tersedia
                </div>
            </div>
        @endif
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <h3>Sale List</h3>
                        <div class="card-header-form">
                            <div class="input-group">
                                <div class="input-group-btn">
                                    <a href="/dashboard/sale/export" class="btn btn-success my-3" target="_blank">Export to
                                        Excel</a>
                                    @if (Auth::user()->role == 'staff')
                                        <a href="{{ route('sale.create') }}" class="btn btn-primary"><i
                                                class="fas fa-plus mr-2"></i>New
                                            Sale</a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped table-md">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Name Customer</th>
                                        <th>Sale Date</th>
                                        <th>Total Price</th>
                                        <th>Created By</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($sales as $sale)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $sale->customer->name }}</td>
                                            <td>{{ $sale->sale_date }}</td>
                                            <td>Rp{{ number_format($sale->total_price, 0, ',' . '.') }}</td>
                                            <td>{{ $sale->user->name }}</td>
                                            <td>
                                                <button class="btn btn-primary" data-toggle="modal"
                                                    data-target="#exampleModal{{ $sale->id }}">Show Detail</button>
                                                <a href="{{ route('sale.download', $sale->id) }}"
                                                    class="btn btn-warning">Download Payment</a>
                                                <form action="{{ route('sale.destroy', $sale->id) }}" method="POST"
                                                    class="d-inline">
                                                    @method('DELETE')
                                                    @csrf
                                                    <button type="submit" class="btn btn-danger"><i
                                                            class="fas fa-trash"></i>Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @foreach ($sales as $sale)
        <div class="modal fade" tabindex="-1" role="dialog" id="exampleModal{{ $sale->id }}">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Detail Sale {{ $sale->id }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div>Customer Name: {{ $sale->detailSales->first()->sale->customer->name }}</div>
                        <div>Customer Address: {{ $sale->detailSales->first()->sale->customer->address }}</div>
                        <div>Customer Phone: {{ $sale->detailSales->first()->sale->customer->phone }}</div>
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
                                @foreach ($sale->detailSales as $item)
                                    <tr>
                                        <th>{{ $item->product->name }}</th>
                                        <td>{{ $item->quantity }}</td>
                                        <td>Rp{{ number_format($item->product->price, 0, ',' . '.') }}</td>
                                        <td>Rp{{ number_format($item->subtotal, 0, ',' . '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div>Total Price: Rp{{ number_format($sale->total_price, 0, ',' . '.') }}</div>
                    </div>
                    <div class="modal-footer bg-whitesmoke br">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
@endsection
