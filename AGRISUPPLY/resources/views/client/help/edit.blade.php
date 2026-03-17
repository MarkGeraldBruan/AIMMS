<link rel="stylesheet" href="{{ asset('css/style.css') }}">
<link rel="stylesheet" href="{{ asset('css/help.css') }}">
<script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="container">
    @include('layouts.core.sidebar')
    <div class="details">
        @include('layouts.core.header')
        
        @include('client.help.form', [
            'isEdit' => true,
            'formAction' => route('client.help.update', $helpRequest->id),
            'backUrl' => route('client.help.show', $helpRequest->id),
            'helpRequest' => $helpRequest
        ])
    </div>
</div>