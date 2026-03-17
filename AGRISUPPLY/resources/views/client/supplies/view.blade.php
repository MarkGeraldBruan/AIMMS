<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock Card</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/supplies.css') }}">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body {
            background: #f5f6fa;
            font-family: 'Poppins', sans-serif;
        }
        .container {
            display: flex;
            min-height: 100vh;
        }
        .details {
            position: relative;
            width: calc(100% - 280px) !important;
            left: 280px !important;
            min-height: 100vh;
            background: #f5f5f5;
            transition: 0.5s;
            padding: 20px;
            padding-bottom: 60px;
            font-family: 'Inter', sans-serif;
            box-sizing: border-box;
        }
        .card {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            padding: 25px;
        }
        .card h2 {
            font-size: 1.5rem;
            color: #333;
            margin-bottom: 15px;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .table th, .table td {
            padding: 10px 15px;
            border-bottom: 1px solid #eee;
        }
        .table th {
            width: 200px;
            background: #fafafa;
            color: #333;
            text-align: left;
        }
        .btn-group {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
            padding: 8px 14px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 500;
            color: #fff;
        }
        .btn-back {
            background: #6c757d;
        }
        .btn-edit {
            background: #007bff;
        }
        .btn:hover {
            opacity: 0.9;
        }
    </style>
</head>
<body>
    <div class="container">
        @include('layouts.core.sidebar')
        <div class="details">
            @include('layouts.core.header')

            <div class="card mt-4">
                <h2><i class="fa-solid fa-box"></i> Stock Card Details</h2>

                @if(isset($supply))
                <table class="table">
                    <tr><th>ID</th><td>{{ $supply->id }}</td></tr>
                    <tr><th>Name</th><td>{{ $supply->name }}</td></tr>
                    <tr><th>Description</th><td>{{ $supply->description ?? 'N/A' }}</td></tr>
                    <tr><th>Quantity</th><td>{{ $supply->quantity }}</td></tr>
                    <tr><th>Unit Price</th><td>₱{{ number_format($supply->unit_price, 2) }}</td></tr>
                    <tr><th>Total Value</th><td>₱{{ number_format($supply->quantity * $supply->unit_price, 2) }}</td></tr>
                    <tr><th>Category</th><td>{{ $supply->category ?? 'N/A' }}</td></tr>
                    <tr><th>Supplier</th><td>{{ $supply->supplier ?? 'N/A' }}</td></tr>
                    <tr><th>Purchase Date</th>
                        <td>{{ $supply->purchase_date ? $supply->purchase_date->format('F d, Y') : 'N/A' }}</td></tr>
                    <tr><th>Minimum Stock</th><td>{{ $supply->minimum_stock }}</td></tr>
                    <tr><th>Notes</th><td>{{ $supply->notes ?? 'None' }}</td></tr>
                </table>

                <div class="btn-group">
                    <a href="{{ route('supplies.index') }}" class="btn btn-back">
                        <ion-icon name="arrow-back-outline"></ion-icon> Back
                    </a>
                    <a href="{{ route('supplies.edit', $supply->id) }}" class="btn btn-edit">
                        <ion-icon name="create-outline"></ion-icon> Edit
                    </a>
                </div>
                @else
                    <p>No supply data found.</p>
                @endif
            </div>
        </div>
    </div>
</body>
</html>
