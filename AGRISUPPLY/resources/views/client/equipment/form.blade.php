{{-- 
    Usage: 
    @include('client.equipment.form', ['equipment' => $equipment, 'action' => route('equipment.update', $equipment), 'method' => 'PUT'])
    @include('client.equipment.form', ['action' => route('equipment.store')])
--}}

@php
    $isEdit = isset($equipment) && $equipment->exists;
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
            <!-- Property Number -->
            <div class="form-group">
                <label for="property_number" class="form-label required">Property Number</label>
                <div class="input-group">
                    <i class="fas fa-barcode"></i>
                    <input type="text" id="property_number" name="property_number" class="form-input" 
                           value="{{ old('property_number', $isEdit ? $equipment->property_number : '') }}" 
                           required placeholder="Enter property number">
                </div>
                @error('property_number')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>
            
            <!-- Article -->
            <div class="form-group">
                <label for="article" class="form-label required">Article</label>
                <div class="input-group">
                    <i class="fas fa-tag"></i>
                    <input type="text" id="article" name="article" class="form-input" 
                           value="{{ old('article', $isEdit ? $equipment->article : '') }}" 
                           required placeholder="Enter article name">
                </div>
                @error('article')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>
            
            <!-- Classification with Autocomplete -->
            <div class="form-group">
                <label for="classification" class="form-label">Classification</label>
                <div class="input-group" style="position: relative;">
                    <i class="fas fa-layer-group"></i>
                    <input type="text" id="classification" name="classification" class="form-input" 
                           value="{{ old('classification', $isEdit ? $equipment->classification : '') }}" 
                           placeholder="Enter Classification"
                           autocomplete="off">
                    <div id="classification-dropdown" class="autocomplete-dropdown" style="display: none;">
                        <!-- Dropdown items will be populated dynamically -->
                    </div>
                </div>
                @error('classification')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>
            
            <!-- Unit of Measurement -->
            <div class="form-group">
                <label for="unit_of_measurement" class="form-label required">Unit of Measurement</label>
                <div class="input-group">
                    <i class="fas fa-ruler"></i>
                    <input type="text" id="unit_of_measurement" name="unit_of_measurement" class="form-input" 
                           value="{{ old('unit_of_measurement', $isEdit ? $equipment->unit_of_measurement : '') }}" 
                           required placeholder="Enter unit of measurement">
                </div>
                @error('unit_of_measurement')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>
            
            <!-- Unit Value -->
            <div class="form-group">
                <label for="unit_value" class="form-label required">Unit Value</label>
                <div class="input-group">
                    <i class="fas fa-peso-sign"></i>
                    <input type="number" id="unit_value" name="unit_value" class="form-input" 
                           value="{{ old('unit_value', $isEdit ? number_format($equipment->unit_value, 2, '.', '') : '0.00') }}" 
                           step="0.01" min="0" required placeholder="0.00">
                </div>
                @error('unit_value')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>
            
            <!-- Remarks (previously Condition) -->
            <div class="form-group">
                <label for="condition" class="form-label required">Remarks</label>
                <select id="condition" name="condition" class="form-select" required>
                    <option value="">Select Remarks</option>
                    @php
                        $currentCondition = old('condition', $isEdit ? $equipment->condition : 'Serviceable');
                    @endphp
                    <option value="Serviceable" {{ $currentCondition == 'Serviceable' ? 'selected' : '' }}>
                        Serviceable
                    </option>
                    <option value="Unserviceable" {{ $currentCondition == 'Unserviceable' ? 'selected' : '' }}>
                        Unserviceable
                    </option>
                </select>
                @error('condition')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>
            
            <!-- Acquisition Date -->
            <div class="form-group">
                <label for="acquisition_date" class="form-label">Acquisition Date</label>
                <div class="input-group">
                    <i class="fas fa-calendar"></i>
                    <input type="date" id="acquisition_date" name="acquisition_date" class="form-input" 
                           value="{{ old('acquisition_date', $isEdit && $equipment->acquisition_date ? $equipment->acquisition_date->format('Y-m-d') : date('Y-m-d')) }}">
                </div>
                @error('acquisition_date')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>
            
            <!-- Responsibility Center (Location) -->
            <div class="form-group">
                <label for="location" class="form-label">Responsibility Center</label>
                <div class="input-group">
                    <i class="fas fa-building"></i>
                    <input type="text" id="location" name="location" class="form-input" 
                           value="{{ old('location', $isEdit ? $equipment->location : '') }}" 
                           placeholder="Department, office, or responsible unit">
                </div>
                @error('location')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>
            
            <!-- Responsible Person -->
            <div class="form-group">
                <label for="responsible_person" class="form-label">Responsible Person</label>
                <div class="input-group">
                    <i class="fas fa-user"></i>
                    <input type="text" id="responsible_person" name="responsible_person" class="form-input" 
                           value="{{ old('responsible_person', $isEdit ? $equipment->responsible_person : '') }}" 
                           placeholder="Name of person responsible for this equipment">
                </div>
                @error('responsible_person')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>
            
            <!-- Description -->
            <div class="form-group full-width">
                <label for="description" class="form-label">Description</label>
                <textarea id="description" name="description" class="form-input form-textarea" 
                          placeholder="Enter detailed description, specifications, or features">{{ old('description', $isEdit ? $equipment->description : '') }}</textarea>
                @error('description')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>
        </div>
        
        <div class="form-actions">
            <a href="{{ route('client.equipment.index') }}" class="btn btn-secondary">
                <i class="fas fa-times"></i>
                Cancel
            </a>
            <button type="submit" class="btn btn-success">
                <i class="fas fa-save"></i>
                {{ $isEdit ? 'Update' : 'Save' }} Equipment
            </button>
        </div>
    </form>
</div>

<style>
.autocomplete-dropdown {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: white;
    border: 1px solid #ddd;
    border-radius: 4px;
    max-height: 200px;
    overflow-y: auto;
    z-index: 1000;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    margin-top: 2px;
}

.autocomplete-item {
    padding: 10px 15px;
    cursor: pointer;
    border-bottom: 1px solid #f0f0f0;
    display: flex;
    align-items: center;
    gap: 8px;
}

.autocomplete-item:hover {
    background: #f8f9fa;
}

.autocomplete-item:last-child {
    border-bottom: none;
}

.autocomplete-item i {
    color: #6c757d;
    font-size: 12px;
}

.autocomplete-empty {
    padding: 10px 15px;
    color: #6c757d;
    font-size: 14px;
    text-align: center;
}
</style>

<script>
    // Set today's date as default for acquisition date (only for create form)
    document.addEventListener('DOMContentLoaded', function() {
        const acquisitionDateInput = document.getElementById('acquisition_date');
        @if(!$isEdit)
        if (!acquisitionDateInput.value) {
            acquisitionDateInput.value = new Date().toISOString().split('T')[0];
        }
        @endif

        // Classification autocomplete functionality
        const classificationInput = document.getElementById('classification');
        const dropdown = document.getElementById('classification-dropdown');
        let classifications = [];

        // Fetch existing classifications
        fetch('/client/equipment/api/classifications')
            .then(response => response.json())
            .then(data => {
                classifications = data;
            })
            .catch(error => console.error('Error fetching classifications:', error));

        // Show dropdown on focus
        classificationInput.addEventListener('focus', function() {
            if (classifications.length > 0) {
                showDropdown(classifications);
            }
        });

        // Filter on input
        classificationInput.addEventListener('input', function() {
            const value = this.value.toLowerCase().trim();
            
            if (value === '') {
                showDropdown(classifications);
            } else {
                const filtered = classifications.filter(item => 
                    item.toLowerCase().includes(value)
                );
                showDropdown(filtered);
            }
        });

        // Hide dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!classificationInput.contains(e.target) && !dropdown.contains(e.target)) {
                dropdown.style.display = 'none';
            }
        });

        function showDropdown(items) {
            if (items.length === 0) {
                dropdown.innerHTML = '<div class="autocomplete-empty">No classifications found. Type to create a new one.</div>';
                dropdown.style.display = 'block';
                return;
            }

            dropdown.innerHTML = items.map(item => `
                <div class="autocomplete-item" data-value="${item}">
                    <i class="fas fa-layer-group"></i>
                    ${item}
                </div>
            `).join('');

            dropdown.style.display = 'block';

            // Add click handlers to items
            dropdown.querySelectorAll('.autocomplete-item').forEach(item => {
                item.addEventListener('click', function() {
                    classificationInput.value = this.dataset.value;
                    dropdown.style.display = 'none';
                });
            });
        }
    });
</script>