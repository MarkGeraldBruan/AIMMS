<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Supply Item</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/supplies.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    <div class="container">
        @include('layouts.core.sidebar')
        <div class="details">
            @include('layouts.core.header')
            
            <div class="form-container">
                <div class="form-header">
                    <a href="{{ route('supplies.index') }}" class="back-button">
                        <i class="fas fa-arrow-left"></i>
                        Back to Supplies
                    </a>
                    <h1 class="form-title">
                        <i class="fas fa-edit"></i>
                        Edit Supply Item
                    </h1>
                </div>
                
                @include('client.supplies.form', [
                    'supply' => $supply,
                    'action' => route('supplies.update', $supply->id),
                    'method' => 'PUT',
                    'categories' => $categories ?? [],
                    'suppliers' => $suppliers ?? []
                ])
            </div>
        </div>
    </div>
</body>
</html>