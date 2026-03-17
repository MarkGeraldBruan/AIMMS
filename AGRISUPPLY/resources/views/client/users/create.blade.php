<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New User</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/users.css') }}">
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
                    <a href="{{ url('client/users') }}" class="back-button">
                        <i class="fas fa-arrow-left"></i>
                        Back to Users
                    </a>
                    <h1 class="form-title">
                        <i class="fas fa-user-plus"></i>
                        Add New User
                    </h1>
                </div>
                
                <div class="form-content">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <strong>Please fix the following errors:</strong>
                            <ul style="margin: 10px 0 0 0; padding-left: 20px;">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    
                    <form method="POST" action="{{ url('client/users') }}">
                        @csrf
                        @include('client.users.form')
                    </form>
                </div>
            </div>
        </div>
    </div>

    @include('layouts.core.footer')
</body>
</html>
