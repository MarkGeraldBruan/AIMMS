<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock In</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/supplies.css') }}">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    <div class="container">
        @include('layouts.core.sidebar')
        <div class="details">
            @include('layouts.core.header')
            
            <div class="form-container">
                <div class="form-header">
                    <a href="{{ route('client.stockcard.index') }}" class="back-button">
                        <i class="fas fa-arrow-left"></i>
                        Back to Stock Card
                    </a>
                    <h1 class="form-title">
                        <i class="fas fa-edit"></i>
                        Stock In
                    </h1>
                    <div id="stock-display" class="stock-info-card">    
                        <div class="stock-info-row">
                            <div class="stock-item">
                                <label>Current Stock:</label>
                                <span id="current-stock-display" class="stock-value current disabled">-- --</span>
                            </div>
                            <div class="stock-item">
                                <label>Adding:</label>
                                <span id="adding-quantity" class="stock-value adding disabled">-- --</span>
                            </div>
                            <div class="stock-item">
                                <label>New Total:</label>
                                <span id="new-total" class="stock-value new-total disabled">-- --</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-content">
                    <form action="{{ route('client.stockcard.stock-in.process') }}" method="POST">
                        @csrf
                        
                        <div class="form-group">
                            <label for="supply_id" class="form-label required">Select Supply Item</label>
                            <select name="supply_id" id="supply_id" class="form-input form-select" required>
                                <option value="">Choose a supply item...</option>
                                @foreach($supplies as $supply)
                                    <option value="{{ $supply->id }}" 
                                            data-current-stock="{{ $supply->quantity }}"
                                            data-unit="{{ $supply->unit }}"
                                            {{ old('supply_id') == $supply->id ? 'selected' : '' }}>
                                        #{{ str_pad($supply->id, 4, '0', STR_PAD_LEFT) }} - {{ $supply->name }} 
                                        (Current: {{ $supply->quantity }} {{ $supply->unit }})
                                    </option>
                                @endforeach
                            </select>
                            @error('supply_id')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="quantity" class="form-label required">Quantity to Add</label>
                            <input type="number" name="quantity" id="quantity" class="form-input" 
                                   value="{{ old('quantity') }}" min="1" placeholder="Enter quantity" required>
                            @error('quantity')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="notes" class="form-label">Description</label>
                            <textarea name="notes" id="notes" class="form-input form-textarea" 
                                      rows="3" placeholder="Enter description or notes (optional)">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-actions">
                            <a href="{{ route('client.stockcard.index') }}" class="btn btn-secondary">
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-plus"></i>
                                Add Stock
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>   
    </div>

    <script>
        $(document).ready(function() {
            let currentStock = 0;
            let unit = '';

            // Initialize display
            updateStockDisplay();

            // Show current stock when supply is selected
            $('#supply_id').on('change', function() {
                const selectedOption = $(this).find('option:selected');
                currentStock = parseInt(selectedOption.data('current-stock')) || 0;
                unit = selectedOption.data('unit') || '';
                
                if (selectedOption.val()) {
                    $('#no-item-selected').hide();
                    $('.stock-value').removeClass('disabled');
                    updateStockDisplay();
                } else {
                    $('#no-item-selected').show();
                    $('.stock-value').addClass('disabled');
                    resetStockDisplay();
                }
            });

            // Update calculations when quantity changes
            $('#quantity').on('input', function() {
                if ($('#supply_id').val()) {
                    updateStockDisplay();
                }
            });

            function updateStockDisplay() {
                const addingQuantity = parseInt($('#quantity').val()) || 0;
                const newTotal = currentStock + addingQuantity;

                if ($('#supply_id').val()) {
                    $('#current-stock-display').text(currentStock + ' ' + unit);
                    $('#adding-quantity').text('+' + addingQuantity + ' ' + unit);
                    $('#new-total').text(newTotal + ' ' + unit);

                    // Add visual feedback for positive change
                    if (addingQuantity > 0) {
                        $('#adding-quantity').addClass('positive');
                        $('#new-total').addClass('positive');
                    } else {
                        $('#adding-quantity').removeClass('positive');
                        $('#new-total').removeClass('positive');
                    }
                }
            }

            function resetStockDisplay() {
                $('#current-stock-display').text('-- --');
                $('#adding-quantity').text('-- --');
                $('#new-total').text('-- --');
                $('#adding-quantity').removeClass('positive');
                $('#new-total').removeClass('positive');
            }

            // Initialize display if form has old values
            if ($('#supply_id').val()) {
                $('#supply_id').trigger('change');
            }
        });
    </script>

    <style>
        .stock-info-card {
            background: linear-gradient(135deg, #5a8a4e 0%, #6b9d5e 100%);
            border-radius: 12px;
            padding: 25px;
            margin: 15px 0;
            box-shadow: 0 4px 12px rgba(90, 138, 78, 0.2);
        }

        .stock-info-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
        }

        .stock-item {
            flex: 1;
            text-align: center;
        }

        .stock-item label {
            display: block;
            font-size: 12px;
            color: rgba(255, 255, 255, 0.9);
            margin-bottom: 10px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .stock-value {
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            font-weight: 700;
            padding: 15px;
            border-radius: 8px;
            background: white;
            border: 2px solid #dee2e6;
            transition: all 0.3s ease;
            min-height: 60px;
        }

        .stock-value.disabled {
            background: #f8f9fa;
            color: #adb5bd;
            border-color: #e9ecef;
        }

        .stock-value.current {
            color: #495057;
            border-color: #6c757d;
        }

        .stock-value.adding {
            color: #28a745;
            border-color: #28a745;
            background: #f0f9f3;
        }

        .stock-value.new-total {
            color: #007bff;
            border-color: #007bff;
            background: #f0f7ff;
        }

        .form-content {
            background: white !important;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        }

        .form-input,
        .form-select {
            background: #fafafa !important;
            border: 2px solid #e9ecef !important;
            transition: all 0.3s ease;
        }

        .form-input:focus,
        .form-select:focus {
            background: white !important;
            border-color: #296218 !important;
            box-shadow: 0 0 0 3px rgba(41, 98, 24, 0.1) !important;
        }

        .form-label {
            color: #495057;
        }

        .form-label.required::after {
            content: " *";
            color: #dc3545;
        }

        @media (max-width: 768px) {
            .stock-info-row {
                flex-direction: column;
                gap: 15px;
            }
            
            .stock-item {
                width: 100%;
            }

            .form-content {
                padding: 20px;
            }
        }
    </style>
</body>
</html>