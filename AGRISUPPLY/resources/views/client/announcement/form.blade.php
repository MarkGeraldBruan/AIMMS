<form action="{{ $route }}" method="POST" id="announcementForm">
    @csrf
    @if(isset($announcement))
        @method('PUT')
    @endif
    
    <div class="form-grid">
        <!-- Title -->
        <div class="form-group full-width">
            <label for="title" class="form-label required">Title</label>
            <div class="input-group">
                <i class="fas fa-heading"></i>
                <input type="text" 
                       id="title" 
                       name="title" 
                       class="form-input" 
                       value="{{ old('title', $announcement->title ?? '') }}"
                       placeholder="Enter announcement title"
                       required>
            </div>
            @error('title')
                <span class="error-message">{{ $message }}</span>
            @enderror
        </div>

        <!-- Status -->
        <div class="form-group">
            <label for="status" class="form-label required">Status</label>
            <select id="status" 
                    name="status" 
                    class="form-select" 
                    required>
                <option value="draft" {{ old('status', $announcement->status ?? 'draft') === 'draft' ? 'selected' : '' }}>Draft</option>
                <option value="published" {{ old('status', $announcement->status ?? '') === 'published' ? 'selected' : '' }}>Published</option>
            </select>
            @error('status')
                <span class="error-message">{{ $message }}</span>
            @enderror
        </div>

        <!-- Event Date -->
        <div class="form-group">
            <label for="event_date" class="form-label">Event Date (Optional)</label>
            <input type="date" 
                   id="event_date" 
                   name="event_date" 
                   class="form-input" 
                   value="{{ old('event_date', isset($announcement) && $announcement->event_date ? $announcement->event_date->format('Y-m-d') : '') }}">
            @error('event_date')
                <span class="error-message">{{ $message }}</span>
            @enderror
        </div>

        <!-- Content -->
        <div class="form-group full-width">
            <label for="content" class="form-label required">Content</label>
            <textarea id="content" 
                      name="content" 
                      class="form-input form-textarea" 
                      rows="8"
                      placeholder="Enter announcement content..."
                      required>{{ old('content', $announcement->content ?? '') }}</textarea>
            @error('content')
                <span class="error-message">{{ $message }}</span>
            @enderror
        </div>

        <!-- Supplies Section -->
        <div class="form-group full-width">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                <label class="form-label" style="margin: 0;">
                    <i class="fas fa-boxes"></i>
                    Supplies Needed for Event
                </label>
                <button type="button" class="btn btn-primary btn-sm" onclick="addSupplyRow()">
                    <i class="fas fa-plus"></i>
                    Add Supply
                </button>
            </div>
            
            <div id="suppliesContainer" class="supplies-container">
                @if(isset($announcement) && $announcement->supplies->count() > 0)
                    @foreach($announcement->supplies as $index => $supply)
                        <div class="supply-row" data-index="{{ $index }}">
                            <div class="supply-select-wrapper">
                                <select name="supplies[{{ $index }}][supply_id]" class="form-select supply-select" required>
                                    <option value="">Select a supply...</option>
                                    @foreach($supplies as $s)
                                        <option value="{{ $s->id }}" 
                                                data-stock="{{ $s->quantity }}" 
                                                data-unit="{{ $s->unit }}"
                                                {{ $supply->id == $s->id ? 'selected' : '' }}>
                                            {{ $s->name }} (Available: {{ $s->quantity }} {{ $s->unit }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="supply-quantity-wrapper">
                                <input type="number" 
                                       name="supplies[{{ $index }}][quantity]" 
                                       class="form-input supply-quantity" 
                                       placeholder="Qty"
                                       value="{{ $supply->pivot->quantity_needed }}"
                                       min="1" 
                                       required>
                            </div>
                            <button type="button" class="btn btn-danger btn-sm" onclick="removeSupplyRow(this)">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    @endforeach
                @else
                    <div class="empty-supplies-message">
                        <i class="fas fa-info-circle"></i>
                        <p>No supplies added yet. Click "Add Supply" to include supplies needed for this event.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Form Actions -->
    <div class="form-actions">
        <a href="{{ route('client.announcement.index') }}" class="btn btn-secondary">
            <i class="fas fa-times"></i>
            Cancel
        </a>
        <button type="submit" class="btn btn-success">
            <i class="fas fa-save"></i>
            {{ isset($announcement) ? 'Update Announcement' : 'Create Announcement' }}
        </button>
    </div>
</form>

<style>
.supplies-container {
    border: 2px dashed #dee2e6;
    border-radius: 8px;
    padding: 20px;
    background: #f8f9fa;
    min-height: 100px;
}

.empty-supplies-message {
    text-align: center;
    color: #6c757d;
    padding: 20px;
}

.empty-supplies-message i {
    font-size: 32px;
    margin-bottom: 10px;
    color: #adb5bd;
}

.supply-row {
    display: grid;
    grid-template-columns: 1fr 150px 50px;
    gap: 10px;
    margin-bottom: 10px;
    padding: 15px;
    background: white;
    border-radius: 8px;
    border: 1px solid #dee2e6;
    align-items: center;
}

.supply-select-wrapper,
.supply-quantity-wrapper {
    display: flex;
    flex-direction: column;
}

.supply-select,
.supply-quantity {
    width: 100%;
}

.supply-quantity {
    text-align: center;
}

@media (max-width: 768px) {
    .supply-row {
        grid-template-columns: 1fr;
        gap: 10px;
    }
    
    .supply-quantity-wrapper {
        grid-column: 1;
    }
}
</style>

<script>
let supplyIndex = {{ isset($announcement) ? $announcement->supplies->count() : 0 }};
const supplies = @json($supplies);

function addSupplyRow() {
    const container = document.getElementById('suppliesContainer');
    const emptyMessage = container.querySelector('.empty-supplies-message');
    
    if (emptyMessage) {
        emptyMessage.remove();
    }
    
    const row = document.createElement('div');
    row.className = 'supply-row';
    row.dataset.index = supplyIndex;
    
    let optionsHtml = '<option value="">Select a supply...</option>';
    supplies.forEach(supply => {
        optionsHtml += `<option value="${supply.id}" data-stock="${supply.quantity}" data-unit="${supply.unit}">
            ${supply.name} (Available: ${supply.quantity} ${supply.unit})
        </option>`;
    });
    
    row.innerHTML = `
        <div class="supply-select-wrapper">
            <select name="supplies[${supplyIndex}][supply_id]" class="form-select supply-select" required>
                ${optionsHtml}
            </select>
        </div>
        <div class="supply-quantity-wrapper">
            <input type="number" 
                   name="supplies[${supplyIndex}][quantity]" 
                   class="form-input supply-quantity" 
                   placeholder="Qty"
                   min="1" 
                   required>
        </div>
        <button type="button" class="btn btn-danger btn-sm" onclick="removeSupplyRow(this)">
            <i class="fas fa-trash"></i>
        </button>
    `;
    
    container.appendChild(row);
    supplyIndex++;
    
    // Add validation for quantity vs stock
    const quantityInput = row.querySelector('.supply-quantity');
    const selectInput = row.querySelector('.supply-select');
    
    quantityInput.addEventListener('input', function() {
        validateSupplyQuantity(selectInput, quantityInput);
    });
    
    selectInput.addEventListener('change', function() {
        validateSupplyQuantity(selectInput, quantityInput);
    });
}

function removeSupplyRow(button) {
    const row = button.closest('.supply-row');
    row.remove();
    
    const container = document.getElementById('suppliesContainer');
    if (container.querySelectorAll('.supply-row').length === 0) {
        container.innerHTML = `
            <div class="empty-supplies-message">
                <i class="fas fa-info-circle"></i>
                <p>No supplies added yet. Click "Add Supply" to include supplies needed for this event.</p>
            </div>
        `;
    }
}

function validateSupplyQuantity(selectElement, quantityInput) {
    const selectedOption = selectElement.options[selectElement.selectedIndex];
    const availableStock = parseInt(selectedOption.dataset.stock || 0);
    const requestedQuantity = parseInt(quantityInput.value || 0);
    
    if (requestedQuantity > availableStock) {
        quantityInput.setCustomValidity(`Only ${availableStock} units available`);
        quantityInput.classList.add('error');
    } else {
        quantityInput.setCustomValidity('');
        quantityInput.classList.remove('error');
    }
}

// Initialize validation for existing rows
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.supply-row').forEach(row => {
        const selectInput = row.querySelector('.supply-select');
        const quantityInput = row.querySelector('.supply-quantity');
        
        quantityInput.addEventListener('input', function() {
            validateSupplyQuantity(selectInput, quantityInput);
        });
        
        selectInput.addEventListener('change', function() {
            validateSupplyQuantity(selectInput, quantityInput);
        });
    });
});
</script>