{{-- 
    Usage: 
    @include('supplies.form', ['supply' => $supply, 'action' => route('supplies.update', $supply), 'method' => 'PUT'])
    @include('supplies.form', ['action' => route('supplies.store')])
--}}

@php
    $isEdit = isset($supply) && $supply->exists;
    $formMethod = $method ?? 'POST';
@endphp

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
    
    <form method="POST" action="{{ $action }}">
        @csrf
        @if($formMethod !== 'POST')
            @method($formMethod)
        @endif
        
        <div class="form-grid">
            <!-- Item Name -->
            <div class="form-group">
                <label for="name" class="form-label required">Item Name</label>
                <div class="input-group">
                    <i class="fas fa-box"></i>
                    <input type="text" id="name" name="name" class="form-input" 
                           value="{{ old('name', $isEdit ? $supply->name : '') }}" 
                           required placeholder="Enter item name">
                </div>
                @error('name')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>
            
            <!-- Category -->
            <div class="form-group">
                <label for="category" class="form-label">Category</label>
                <div class="input-group">
                    <i class="fas fa-tags"></i>
                    <input type="text" id="category" name="category" class="form-input" 
                           value="{{ old('category', $isEdit ? $supply->category : '') }}" 
                           placeholder="e.g., Office Supplies, Equipment" 
                           list="categories">
                    <datalist id="categories">
                        @foreach($categories ?? [] as $cat)
                            <option value="{{ $cat }}">
                        @endforeach
                    </datalist>
                </div>
                @error('category')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>
            
            <!-- Quantity -->
            <!-- <div class="form-group">
                <label for="quantity" class="form-label required">Quantity</label>
                <div class="input-group">
                    <i class="fas fa-calculator"></i>
                    <input type="number" id="quantity" name="quantity" class="form-input" 
                           value="{{ old('quantity', $isEdit ? $supply->quantity : 0) }}" 
                           min="0" required placeholder="0">
                </div>
                @error('quantity')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div> -->
            
            <!-- Unit -->
            <div class="form-group">
                <label for="unit" class="form-label required">Unit</label>
                <select id="unit" name="unit" class="form-select" required>
                    <option value="">Select Unit</option>
                    @php
                        $currentUnit = old('unit', $isEdit ? $supply->unit : '');
                        $units = [
                            'pcs' => 'Pieces (pcs)',
                            'kg' => 'Kilograms (kg)',
                            'g' => 'Grams (g)',
                            'liters' => 'Liters',
                            'ml' => 'Milliliters (ml)',
                            'boxes' => 'Boxes',
                            'packs' => 'Packs',
                            'meters' => 'Meters',
                            'rolls' => 'Rolls'
                        ];
                    @endphp
                    @foreach($units as $value => $label)
                        <option value="{{ $value }}" {{ $currentUnit == $value ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
                @error('unit')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>
            
            <!-- Unit Price -->
            <div class="form-group">
                <label for="unit_price" class="form-label required">Unit Price</label>
                <div class="input-group">
                    <i class="fas fa-peso-sign"></i>
                    <input type="number" id="unit_price" name="unit_price" class="form-input" 
                           value="{{ old('unit_price', $isEdit ? number_format($supply->unit_price, 2, '.', '') : '0.00') }}" 
                           step="0.01" min="0" required placeholder="0.00">
                </div>
                @error('unit_price')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>
            
            <!-- Minimum Stock -->
            <div class="form-group">
                <label for="minimum_stock" class="form-label required">Minimum Stock Level</label>
                <div class="input-group">
                    <i class="fas fa-exclamation-triangle"></i>
                    <input type="number" id="minimum_stock" name="minimum_stock" class="form-input" 
                           value="{{ old('minimum_stock', $isEdit ? $supply->minimum_stock : 5) }}" 
                           min="0" required placeholder="5">
                </div>
                <small style="color: #6c757d; font-size: 12px;">Alert when stock falls below this level</small>
                @error('minimum_stock')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>
            
            <!-- Supplier -->
            <div class="form-group">
                <label for="supplier" class="form-label">Supplier</label>
                <div class="input-group">
                    <i class="fas fa-truck"></i>
                    <input type="text" id="supplier" name="supplier" class="form-input" 
                           value="{{ old('supplier', $isEdit ? $supply->supplier : '') }}" 
                           placeholder="Supplier name" 
                           list="suppliers">
                    <datalist id="suppliers">
                        @foreach($suppliers ?? [] as $supplier)
                            <option value="{{ $supplier }}">
                        @endforeach
                    </datalist>
                </div>
                @error('supplier')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>
            
            <!-- Purchase Date -->
            <div class="form-group">
                <label for="purchase_date" class="form-label">Purchase Date</label>
                <div class="input-group">
                    <i class="fas fa-calendar"></i>
                    <input type="date" id="purchase_date" name="purchase_date" class="form-input" 
                           value="{{ old('purchase_date', $isEdit && $supply->purchase_date ? $supply->purchase_date->format('Y-m-d') : date('Y-m-d')) }}">
                </div>
                @error('purchase_date')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>
            
            <!-- Description -->
            <div class="form-group full-width">
                <label for="description" class="form-label">Description</label>
                <textarea id="description" name="description" class="form-input form-textarea" 
                          placeholder="Enter item description, specifications, or other details">{{ old('description', $isEdit ? $supply->description : '') }}</textarea>
                @error('description')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>
            
            <!-- Notes -->
            <div class="form-group full-width">
                <label for="notes" class="form-label">Notes</label>
                <textarea id="notes" name="notes" class="form-input form-textarea" 
                          placeholder="Additional notes or comments">{{ old('notes', $isEdit ? $supply->notes : '') }}</textarea>
                @error('notes')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>
        </div>
        
        <div class="form-actions">
            <a href="{{ route('supplies.index') }}" class="btn btn-secondary">
                <i class="fas fa-times"></i>
                Cancel
            </a>
            <button type="submit" class="btn btn-success">
                <i class="fas fa-save"></i>
                {{ $isEdit ? 'Update' : 'Save' }} Supply Item
            </button>
        </div>
    </form>
</div>

<script>
    // Auto-calculate total value
    function calculateTotal() {
        const quantity = parseFloat(document.getElementById('quantity').value) || 0;
        const unitPrice = parseFloat(document.getElementById('unit_price').value) || 0;
        const total = quantity * unitPrice;
        
        // You can display this somewhere if needed
        console.log('Total Value:', total.toFixed(2));
    }
    
    document.getElementById('quantity').addEventListener('input', calculateTotal);
    document.getElementById('unit_price').addEventListener('input', calculateTotal);
    
    // Set today's date as default for purchase date (only for create form)
    document.addEventListener('DOMContentLoaded', function() {
        calculateTotal(); // Calculate initial total
        
        const purchaseDateInput = document.getElementById('purchase_date');
        @if(!$isEdit)
        if (!purchaseDateInput.value) {
            purchaseDateInput.value = new Date().toISOString().split('T')[0];
        }
        @endif
    });
</script>