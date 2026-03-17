<div class="form-container">
    <div class="form-header">
        <h2>
            <ion-icon name="{{ $isEdit ? 'create-outline' : 'add-outline' }}"></ion-icon> 
            {{ $isEdit ? 'Edit Help Request' : 'Submit Help Request' }}
        </h2>
        <a href="{{ $backUrl }}" class="btn btn-secondary">
            <ion-icon name="arrow-back-outline"></ion-icon> Back
        </a>
    </div>

    @if($isEdit && auth()->user()->isAdmin())
        {{-- Admin Edit Form --}}
        <form action="{{ route('client.help.update', $helpRequest->id) }}" method="POST" class="help-form">
            @csrf
            @method('PUT')
            
            <div class="form-row">
                <div class="form-group">
                    <label>Subject</label>
                    <input type="text" value="{{ $helpRequest->subject }}" readonly class="readonly">
                </div>
                <div class="form-group">
                    <label>Priority</label>
                    <input type="text" value="{{ ucfirst($helpRequest->priority) }}" readonly class="readonly">
                </div>
            </div>

            <div class="form-group">
                <label>Description</label>
                <textarea readonly class="readonly" rows="4">{{ $helpRequest->description }}</textarea>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="status">Status *</label>
                    <select id="status" name="status" required>
                        <option value="pending" {{ $helpRequest->status === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="in_progress" {{ $helpRequest->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="resolved" {{ $helpRequest->status === 'resolved' ? 'selected' : '' }}>Resolved</option>
                        <option value="closed" {{ $helpRequest->status === 'closed' ? 'selected' : '' }}>Closed</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="assigned_to">Assign To</label>
                    <select id="assigned_to" name="assigned_to">
                        <option value="">Select Admin</option>
                        @foreach(\App\Models\User::where('role', 'admin')->get() as $admin)
                            <option value="{{ $admin->id }}" {{ $helpRequest->assigned_to === $admin->id ? 'selected' : '' }}>
                                {{ $admin->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="admin_response">Admin Response</label>
                <textarea id="admin_response" name="admin_response" rows="6" placeholder="Provide response to the user...">{{ $helpRequest->admin_response }}</textarea>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Update Request</button>
                <a href="{{ $backUrl }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>

    @else
        {{-- User Create/Edit Form --}}
        <form action="{{ $formAction }}" method="POST" class="help-form">
            @csrf
            @if($isEdit)
                @method('PUT')
            @endif
            
            <div class="form-group">
                <label for="subject">Subject *</label>
                <input type="text" id="subject" name="subject" 
                       value="{{ old('subject', $isEdit ? $helpRequest->subject : '') }}" required>
                @error('subject')<span class="error">{{ $message }}</span>@enderror
            </div>

            <div class="form-group">
                <label for="priority">Priority *</label>
                <select id="priority" name="priority" required>
                    @if(!$isEdit)
                        <option value="">Select Priority</option>
                    @endif
                    <option value="low" {{ old('priority', $isEdit ? $helpRequest->priority : '') === 'low' ? 'selected' : '' }}>Low</option>
                    <option value="medium" {{ old('priority', $isEdit ? $helpRequest->priority : '') === 'medium' ? 'selected' : '' }}>Medium</option>
                    <option value="high" {{ old('priority', $isEdit ? $helpRequest->priority : '') === 'high' ? 'selected' : '' }}>High</option>
                </select>
                @error('priority')<span class="error">{{ $message }}</span>@enderror
            </div>

            <div class="form-group">
                <label for="description">Description *</label>
                <textarea id="description" name="description" rows="6" required>{{ old('description', $isEdit ? $helpRequest->description : '') }}</textarea>
                @error('description')<span class="error">{{ $message }}</span>@enderror
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    {{ $isEdit ? 'Update Request' : 'Submit Request' }}
                </button>
                <a href="{{ $backUrl }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    @endif
</div>