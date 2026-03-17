<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Equipment</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/supplies.css') }}">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    <div class="container">
        @include('layouts.core.sidebar')
        <div class="details">
            @include('layouts.core.header')
            
            <div class="form-container">
                <div class="form-header">
                    <a href="{{ route('client.equipment.index') }}" class="back-button">
                        <i class="fas fa-arrow-left"></i>
                        Back to Equipment List
                    </a>
                    <h1 class="form-title">
                        <i class="fas fa-edit"></i>
                        Edit Equipment
                    </h1>
                </div>
                
                @include('client.equipment.form', [
                    'equipment' => $equipment,
                    'action' => route('equipment.update', $equipment),
                    'method' => 'PUT'
                ])
            </div>
        </div>
    </div>
</body>
</html>