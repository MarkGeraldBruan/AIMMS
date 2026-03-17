{{-- filepath: resources/views/client/profile/index.blade.php --}}

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Settings</title>
    <link rel="stylesheet" href="{{ asset('css/profile.css') }}">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        .profile-settings-container {
            background-color: #296218;
            border-radius: 20px;
            padding: 30px;
            margin: 20px 0;
            color: white;
            box-shadow: 0 10px 30px rgba(76, 175, 80, 0.3);
        }
        
        .profile-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        
        .profile-title {
            font-size: 24px;
            font-weight: 700;
            margin: 0;
        }
        
        .profile-info {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        
        .profile-avatar {
            position: relative;
            cursor: pointer;
        }
        
        .avatar-img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid rgba(255, 255, 255, 0.3);
            transition: all 0.3s ease;
        }
        
        .avatar-img:hover {
            border-color: rgba(255, 255, 255, 0.6);
        }
        
        .avatar-badge {
            position: absolute;
            bottom: 0;
            right: 0;
            background: #2196F3;
            color: white;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            border: 2px solid white;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .avatar-badge:hover {
            background: #1976D2;
            transform: scale(1.1);
        }
        
        .avatar-upload-section {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            padding: 20px;
            margin: 20px 0;
            display: none;
        }
        
        .avatar-upload-section.active {
            display: block;
        }
        
        .avatar-preview {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 15px;
        }
        
        .avatar-preview img {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid rgba(255, 255, 255, 0.3);
        }
        
        .upload-controls {
            display: flex;
            gap: 10px;
            align-items: center;
            flex-wrap: wrap;
        }
        
        .file-input-wrapper {
            position: relative;
            overflow: hidden;
            display: inline-block;
        }
        
        .file-input-wrapper input[type=file] {
            position: absolute;
            left: -9999px;
        }
        
        .file-input-label {
            background: #2196F3;
            color: white;
            padding: 8px 16px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .file-input-label:hover {
            background: #1976D2;
        }
        
        .remove-avatar-btn {
            background: transparent;
            color: rgba(255, 255, 255, 0.8);
            border: 1px solid rgba(255, 255, 255, 0.3);
            padding: 8px 16px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .remove-avatar-btn:hover {
            background: rgba(244, 67, 54, 0.2);
            border-color: #f44336;
            color: #f44336;
        }
        
        .upload-info {
            font-size: 12px;
            opacity: 0.7;
            margin-top: 10px;
        }
        
        .profile-details h3 {
            margin: 0;
            font-size: 18px;
            font-weight: 600;
        }
        
        .profile-details p {
            margin: 5px 0 0 0;
            opacity: 0.8;
            font-size: 14px;
        }
        
        .edit-btn {
            background: #2196F3;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 10px;
            cursor: pointer;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.3s ease;
        }
        
        .edit-btn:hover {
            background: #1976D2;
            transform: translateY(-2px);
        }
        
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .form-group {
            display: flex;
            flex-direction: column;
        }
        
        .form-label {
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 8px;
            opacity: 0.9;
        }
        
        .form-input {
            background: rgba(255, 255, 255, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 10px;
            padding: 12px 15px;
            color: white;
            font-size: 14px;
            transition: all 0.3s ease;
        }
        
        .form-input::placeholder {
            color: rgba(255, 255, 255, 0.7);
        }
        
        .form-input:focus {
            outline: none;
            background: rgba(255, 255, 255, 0.25);
            border-color: rgba(255, 255, 255, 0.5);
            transform: translateY(-2px);
        }
        
        .form-input.error {
            border-color: #f44336;
            background: rgba(244, 67, 54, 0.1);
        }
        
        .email-section {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            padding: 20px;
            margin: 20px 0;
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .email-icon {
            background: #2196F3;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
        }
        
        .email-details h4 {
            margin: 0;
            font-size: 16px;
            font-weight: 600;
        }
        
        .email-details p {
            margin: 5px 0 0 0;
            opacity: 0.7;
            font-size: 12px;
        }
        
        .action-buttons {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 30px;
        }
        
        .delete-btn {
            background: transparent;
            color: rgba(255, 255, 255, 0.8);
            border: 1px solid rgba(255, 255, 255, 0.3);
            padding: 12px 24px;
            border-radius: 10px;
            cursor: pointer;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.3s ease;
        }
        
        .delete-btn:hover {
            background: rgba(244, 67, 54, 0.2);
            border-color: #f44336;
            color: #f44336;
        }
        
        .save-btn {
            background: #2196F3;
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 10px;
            cursor: pointer;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.3s ease;
        }
        
        .save-btn:hover {
            background: #1976D2;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(33, 150, 243, 0.4);
        }
        
        .save-btn:disabled {
            background: rgba(255, 255, 255, 0.2);
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }
        
        .delete-warning {
            font-size: 12px;
            opacity: 0.7;
            margin-top: 5px;
        }
        
        .success-message {
            background: rgba(76, 175, 80, 0.2);
            border: 1px solid rgba(76, 175, 80, 0.5);
            color: #4CAF50;
            padding: 12px 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .error-message {
            color: #ffcccb;
            font-size: 12px;
            margin-top: 5px;
        }
        
        @media (max-width: 768px) {
            .form-grid {
                grid-template-columns: 1fr;
                gap: 15px;
            }
            
            .profile-info {
                flex-direction: column;
                text-align: center;
            }
            
            .action-buttons {
                flex-direction: column;
                gap: 15px;
            }
            
            .upload-controls {
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        @include('layouts.core.sidebar')
        
        <div class="details">
            @include('layouts.core.header')
            
            <div class="profile-settings-container">
                <div class="profile-header">
                    <h1 class="profile-title">Profile Settings</h1>
                    <button class="edit-btn" onclick="toggleEditMode()">
                        <ion-icon name="create-outline"></ion-icon>
                        Edit
                    </button>
                </div>
                
                <div class="profile-info">
                    <div class="profile-avatar" onclick="toggleAvatarUpload()">
                        <img src="{{ $user->avatar_url }}" 
                             alt="Profile" class="avatar-img" id="avatarDisplay">
                        <div class="avatar-badge">
                            <ion-icon name="camera-outline"></ion-icon> 
                        </div>
                    </div>
                    <div class="profile-details">
                        <h3>{{ $user->name }}</h3>
                        <p>{{ $user->email }}</p>
                    </div>
                </div>

                <div class="avatar-upload-section" id="avatarUploadSection">
                    <h4 style="margin: 0 0 15px 0; font-size: 16px; font-weight: 600;">Change Profile Picture</h4>
                    <div class="avatar-preview">
                        <img src="{{ $user->avatar_url }}" alt="Current Avatar" id="avatarPreview">
                        <div>
                            <p style="margin: 0; font-size: 14px; font-weight: 600;">Current Photo</p>
                            <p style="margin: 5px 0 0 0; opacity: 0.7; font-size: 12px;">Last updated {{ $user->updated_at ? $user->updated_at->diffForHumans() : 'Never' }}</p>
                        </div>
                    </div>
                    <div class="upload-controls">
                        <div class="file-input-wrapper">
                            <input type="file" id="avatarInput" name="avatar" accept="image/*">
                            <label for="avatarInput" class="file-input-label">
                                <ion-icon name="cloud-upload-outline"></ion-icon>
                                Choose Photo
                            </label>
                        </div>
                        @if($user->avatar)
                        <button type="button" class="remove-avatar-btn" onclick="removeAvatar()">
                            <ion-icon name="trash-outline"></ion-icon>
                            Remove
                        </button>
                        @endif
                    </div>
                    <div class="upload-info">
                        Recommended: Square image, at least 200x200px. Max file size: 2MB. Supported formats: JPG, PNG, GIF.
                    </div>
                </div>

                <!-- Logo Upload Section (Admin Only) -->
                @if($user->isAdmin())
                <div class="logo-upload-section" id="logoUploadSection" style="background: rgba(255, 255, 255, 0.1); border-radius: 15px; padding: 20px; margin: 20px 0;">
                    <h4 style="margin: 0 0 15px 0; font-size: 16px; font-weight: 600;">
                        <ion-icon name="images-outline"></ion-icon>
                        ATI-RTC 1 Logo (System Logo)
                    </h4>
                    <div class="avatar-preview">
                        <img src="{{ $user->logo_url }}" alt="Current Logo" id="logoPreview" style="width: 80px; height: 80px; object-fit: contain; background: white; padding: 5px; border-radius: 8px;">
                        <div>
                            <p style="margin: 0; font-size: 14px; font-weight: 600;">Current Logo</p>
                            <p style="margin: 5px 0 0 0; opacity: 0.7; font-size: 12px;">This logo will be displayed in the sidebar</p>
                        </div>
                    </div>
                    <div class="upload-controls">
                        <div class="file-input-wrapper">
                            <input type="file" id="logoInput" name="logo" accept="image/*">
                            <label for="logoInput" class="file-input-label">
                                <ion-icon name="cloud-upload-outline"></ion-icon>
                                Choose Logo
                            </label>
                        </div>
                        @if($user->logo)
                        <button type="button" class="remove-avatar-btn" onclick="removeLogo()">
                            <ion-icon name="trash-outline"></ion-icon>
                            Remove
                        </button>
                        @endif
                    </div>
                    <div class="upload-info">
                        Recommended: Square or rectangular image with transparent background. Max file size: 2MB. Supported formats: JPG, PNG, GIF.
                    </div>
                </div>
                @endif

                @if(session('success'))
                    <div class="success-message" id="success-alert">
                        <ion-icon name="checkmark-circle-outline"></ion-icon>
                        {{ session('success') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('client.profile.update') }}" id="profileForm" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <!-- Hidden file input for form submission -->
                    <input type="file" name="avatar" id="hiddenAvatarInput" style="display: none;">
                    <input type="file" name="logo" id="hiddenLogoInput" style="display: none;">
                    
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">First Name</label>
                            <input type="text" 
                                   class="form-input @error('name') error @enderror" 
                                   name="first_name" 
                                   value="{{ old('first_name', explode(' ', $user->name)[0] ?? '') }}" 
                                   placeholder="Your First Name"
                                   disabled>
                            @error('first_name')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Last Name</label>
                            <input type="text" 
                                   class="form-input @error('last_name') error @enderror" 
                                   name="last_name" 
                                   value="{{ old('last_name', implode(' ', array_slice(explode(' ', $user->name), 1))) }}" 
                                   placeholder="Your Last Name"
                                   disabled>
                            @error('last_name')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div></div> <!-- Empty div for grid spacing -->
                    </div>
                    
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">Old Password</label>
                            <input type="password" 
                                   class="form-input @error('current_password') error @enderror" 
                                   name="current_password" 
                                   placeholder="••••••••••••"
                                   disabled>
                            @error('current_password')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">New Password</label>
                            <input type="password" 
                                   class="form-input @error('password') error @enderror" 
                                   name="password" 
                                   placeholder="••••••••••••"
                                   disabled>
                            @error('password')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Confirm New Password</label>
                            <input type="password" 
                                   class="form-input @error('password_confirmation') error @enderror" 
                                   name="password_confirmation" 
                                   placeholder="••••••••••••"
                                   disabled>
                            @error('password_confirmation')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <!-- Hidden input to combine first and last name -->
                    <input type="hidden" name="name" id="fullName">
                </form>

                <div class="email-section">
                    <div class="email-icon">
                        <ion-icon name="mail-outline"></ion-icon>
                    </div>
                    <div class="email-details">
                        <h4>My email Address</h4>
                        <p>{{ $user->email }}</p>
                        <p>{{ $user->updated_at ? $user->updated_at->diffForHumans() : 'Unknown' }}</p>
                    </div>
                </div>

                <div class="action-buttons">
                    <div>
                        <button type="button" class="delete-btn" onclick="confirmDelete()">
                            Delete Account
                        </button>
                        <div class="delete-warning">
                            Contact the admin to process the deletion of your account.
                        </div>
                    </div>
                    
                    <button type="submit" class="save-btn" form="profileForm" disabled id="saveBtn">
                        Save Info
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let editMode = false;
        
        function toggleEditMode() {
            editMode = !editMode;
            const inputs = document.querySelectorAll('.form-input');
            const saveBtn = document.getElementById('saveBtn');
            const editBtn = document.querySelector('.edit-btn');
            
            inputs.forEach(input => {
                input.disabled = !editMode;
            });
            
            saveBtn.disabled = !editMode;
            editBtn.innerHTML = editMode ? 
                '<ion-icon name="close-outline"></ion-icon> Cancel' : 
                '<ion-icon name="create-outline"></ion-icon> Edit';
                
            // Clear password fields when entering edit mode
            if (editMode) {
                document.querySelector('input[name="current_password"]').value = '';
                document.querySelector('input[name="password"]').value = '';
                document.querySelector('input[name="password_confirmation"]').value = '';
            }
            
            // Hide avatar upload section when exiting edit mode
            if (!editMode) {
                document.getElementById('avatarUploadSection').classList.remove('active');
            }
        }
        
        function toggleAvatarUpload() {
            const section = document.getElementById('avatarUploadSection');
            section.classList.toggle('active');
        }
        
        function confirmDelete() {
            if (confirm('Are you sure you want to delete your account? This action cannot be undone.')) {
                alert('Please contact the administrator to process account deletion.');
            }
        }
        
        // Handle logo file selection
        document.getElementById('logoInput').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                // Validate file size (2MB)
                if (file.size > 2 * 1024 * 1024) {
                    alert('File size must be less than 2MB');
                    return;
                }
                
                // Validate file type
                if (!file.type.startsWith('image/')) {
                    alert('Please select an image file');
                    return;
                }
                
                // Preview the image
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('logoPreview').src = e.target.result;
                };
                reader.readAsDataURL(file);
                
                // Copy file to hidden input for form submission
                const hiddenInput = document.getElementById('hiddenLogoInput');
                hiddenInput.files = e.target.files;
                
                // Enable save button
                document.getElementById('saveBtn').disabled = false;
            }
        });
        
        // Remove logo function
        function removeLogo() {
            if (confirm('Are you sure you want to remove the system logo?')) {
                fetch('{{ route("client.profile.remove-logo") }}', {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                    },
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update logo display to default
                        document.getElementById('logoPreview').src = '{{ asset("assets/img/atirtc1logo.jpg") }}';
                        
                        // Hide remove button
                        const removeBtn = document.querySelector('#logoUploadSection .remove-avatar-btn');
                        if (removeBtn) {
                            removeBtn.style.display = 'none';
                        }
                        
                        // Show success message
                        alert('System logo removed successfully');
                        location.reload();
                    } else {
                        alert(data.message || 'Error removing logo');
                    }
                })
                .catch(error => {
                    alert('Error removing system logo');
                    console.error('Error:', error);
                });
            }
        }
        
        // Handle avatar file selection
        document.getElementById('avatarInput').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                // Validate file size (2MB)
                if (file.size > 2 * 1024 * 1024) {
                    alert('File size must be less than 2MB');
                    return;
                }
                
                // Validate file type
                if (!file.type.startsWith('image/')) {
                    alert('Please select an image file');
                    return;
                }
                
                // Preview the image
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('avatarPreview').src = e.target.result;
                    document.getElementById('avatarDisplay').src = e.target.result;
                };
                reader.readAsDataURL(file);
                
                // Copy file to hidden input for form submission
                const hiddenInput = document.getElementById('hiddenAvatarInput');
                hiddenInput.files = e.target.files;
                
                // Enable save button
                document.getElementById('saveBtn').disabled = false;
            }
        });
        
        // Remove avatar function
        function removeAvatar() {
            if (confirm('Are you sure you want to remove your profile picture?')) {
                fetch('{{ route("client.profile.remove-avatar") }}', {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                    },
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update avatar display to default
                        const defaultAvatar = '{{ asset("images/noprofile.jpg") }}';
                        document.getElementById('avatarDisplay').src = defaultAvatar;
                        document.getElementById('avatarPreview').src = defaultAvatar;
                        
                        // Hide remove button
                        document.querySelector('.remove-avatar-btn').style.display = 'none';
                        
                        // Show success message
                        alert('Profile picture removed successfully');
                        location.reload();
                    }
                })
                .catch(error => {
                    alert('Error removing profile picture');
                    console.error('Error:', error);
                });
            }
        }
        
        // Combine first and last name before form submission
        document.getElementById('profileForm').addEventListener('submit', function(e) {
            const firstName = document.querySelector('input[name="first_name"]').value.trim();
            const lastName = document.querySelector('input[name="last_name"]').value.trim();
            
            if (!firstName) {
                e.preventDefault();
                alert('First name is required');
                return;
            }
            
            document.getElementById('fullName').value = firstName + (lastName ? ' ' + lastName : '');
        });
        
        // Auto-dismiss success messages
        document.addEventListener('DOMContentLoaded', function() {
            const successAlert = document.getElementById('success-alert');
            if (successAlert) {
                setTimeout(function() {
                    successAlert.style.transition = 'opacity 0.3s ease';
                    successAlert.style.opacity = '0';
                    setTimeout(function() {
                        successAlert.remove();
                    }, 300);
                }, 5000);
            }
        });

        // Password confirmation validation
        const passwordInput = document.querySelector('input[name="password"]');
        const confirmPasswordInput = document.querySelector('input[name="password_confirmation"]');
        
        function validatePasswordMatch() {
            const password = passwordInput.value;
            const confirmPassword = confirmPasswordInput.value;
            
            if (confirmPassword && password !== confirmPassword) {
                confirmPasswordInput.classList.add('error');
                return false;
            } else {
                confirmPasswordInput.classList.remove('error');
                return true;
            }
        }
        
        passwordInput.addEventListener('input', validatePasswordMatch);
        confirmPasswordInput.addEventListener('input', validatePasswordMatch);
        
        // Form validation before submission
        document.getElementById('profileForm').addEventListener('submit', function(e) {
            const currentPassword = document.querySelector('input[name="current_password"]').value;
            const newPassword = document.querySelector('input[name="password"]').value;
            const confirmPassword = document.querySelector('input[name="password_confirmation"]').value;
            
            // If user wants to change password, validate password fields
            if (newPassword || confirmPassword) {
                if (!currentPassword) {
                    e.preventDefault();
                    alert('Please enter your current password to change password');
                    return;
                }
                
                if (newPassword !== confirmPassword) {
                    e.preventDefault();
                    alert('New password and confirmation do not match');
                    return;
                }
                
                if (newPassword.length < 8) {
                    e.preventDefault();
                    alert('New password must be at least 8 characters long');
                    return;
                }
            }
        });
    </script>
</body>
</html>
