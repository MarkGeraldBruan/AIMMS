<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Equipment Details</title>
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
            padding: 20px 20px 100px 20px;
            font-family: 'Inter', sans-serif;
            box-sizing: border-box;
            max-width: none;
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

        @media (max-width: 768px) {
            .details {
                width: 100% !important;
                left: 0 !important;
                padding: 20px 10px 100px 10px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        @include('layouts.core.sidebar')
        <div class="details">
            @include('layouts.core.header')

            <div class="card mt-4">
                <h2><i class="fa-solid fa-box"></i> Equipment Details</h2>

                @if(isset($equipment))
                <table class="table">
                    <tr><th>Property Number</th><td>{{ $equipment->property_number }}</td></tr>
                    <tr><th>Article</th><td>{{ $equipment->article }}</td></tr>
                    <tr><th>Classification</th><td>{{ $equipment->classification ?? 'N/A' }}</td></tr>
                    <tr><th>Description</th><td>{{ $equipment->description ?? 'N/A' }}</td></tr>
                    <tr><th>Unit of Measurement</th><td>{{ $equipment->unit_of_measurement }}</td></tr>
                    <tr><th>Unit Value</th><td>₱{{ number_format($equipment->unit_value, 2) }}</td></tr>
                    <tr><th>Condition</th><td>{{ $equipment->condition }}</td></tr>
                    <tr><th>Acquisition Date</th>
                        <td>{{ $equipment->acquisition_date ? \Carbon\Carbon::parse($equipment->acquisition_date)->format('F d, Y') : 'N/A' }}</td></tr>
                    <tr><th>Location</th><td>{{ $equipment->location ?? 'N/A' }}</td></tr>
                    <tr><th>Responsible Person</th><td>{{ $equipment->responsible_person ?? 'N/A' }}</td></tr>
                    <tr><th>Remarks</th><td>{{ $equipment->remarks ?? 'None' }}</td></tr>
                </table>

                <div class="btn-group">
                    <a href="{{ route('client.equipment.index') }}" class="btn btn-back">
                        <ion-icon name="arrow-back-outline"></ion-icon> Back
                    </a>
                    <a href="{{ route('equipment.edit', $equipment->id) }}" class="btn btn-edit">
                        <ion-icon name="create-outline"></ion-icon> Edit
                    </a>
                </div>
                @else
                    <p>No equipment data found.</p>
                
                @endif
            </div>
        </div>
    </div>

    @include('layouts.core.footer')
</body>
</html>
