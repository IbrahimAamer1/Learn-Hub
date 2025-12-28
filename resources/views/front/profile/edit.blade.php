@extends('front.master')
@section('title', __('lang.my_profile') ?? 'My Profile')

@section('content')
    <div class="container-xxl py-4">
        <div class="row mb-4">
            <div class="col-12">
                <h2 class="mb-2">{{ __('lang.my_profile') ?? 'My Profile' }}</h2>
                <p class="text-muted">{{ __('lang.update_profile_info') ?? 'Update your profile information and password' }}</p>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8 mx-auto">
                <!-- Success Message -->
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bx bx-check-circle me-2"></i>
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <!-- Profile Information Form -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="bx bx-user me-2"></i>
                            {{ __('lang.profile_information') ?? 'Profile Information' }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('front.profile.update') }}" id="profile-form" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <!-- Avatar Upload -->
                            <div class="mb-4">
                                <label class="form-label">
                                    {{ __('lang.profile_picture') ?? 'Profile Picture' }}
                                </label>
                                <div class="d-flex align-items-center">
                                    <!-- Current Avatar -->
                                    <div class="me-3">
                                        <img 
                                            src="{{ $user->avatar_url }}" 
                                            alt="Avatar" 
                                            class="rounded-circle" 
                                            style="width: 80px; height: 80px; object-fit: cover; border: 2px solid #dee2e6;"
                                            id="avatar-preview"
                                        >
                                    </div>
                                    
                                    <!-- Upload Button -->
                                    <div class="flex-grow-1">
                                        <input 
                                            type="file" 
                                            class="form-control @error('avatar') is-invalid @enderror" 
                                            id="avatar" 
                                            name="avatar" 
                                            accept="image/*"
                                            onchange="previewAvatar(this)"
                                        />
                                        @error('avatar')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                        <small class="text-muted">
                                            {{ __('lang.avatar_hint') ?? 'Max size: 2MB. Supported formats: JPG, PNG, GIF' }}
                                        </small>
                                    </div>
                                </div>
                            </div>

                            <!-- Name -->
                            <div class="mb-3">
                                <label for="name" class="form-label">
                                    {{ __('lang.name') ?? 'Name' }}
                                    <span class="text-danger">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    class="form-control @error('name') is-invalid @enderror" 
                                    id="name" 
                                    name="name" 
                                    value="{{ old('name', $user->name) }}" 
                                    required 
                                    autofocus
                                    placeholder="{{ __('lang.enter_your_name') ?? 'Enter your name' }}"
                                />
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div class="mb-3">
                                <label for="email" class="form-label">
                                    {{ __('lang.email') ?? 'Email' }}
                                    <span class="text-danger">*</span>
                                </label>
                                <input 
                                    type="email" 
                                    class="form-control @error('email') is-invalid @enderror" 
                                    id="email" 
                                    name="email" 
                                    value="{{ old('email', $user->email) }}" 
                                    required
                                    placeholder="{{ __('lang.enter_your_email') ?? 'Enter your email' }}"
                                />
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                @if($user->email_verified_at)
                                    <small class="text-success">
                                        <i class="bx bx-check-circle"></i> {{ __('lang.email_verified') ?? 'Email verified' }}
                                    </small>
                                @else
                                    <small class="text-warning">
                                        <i class="bx bx-error-circle"></i> {{ __('lang.email_not_verified') ?? 'Email not verified' }}
                                    </small>
                                @endif
                            </div>

                            <!-- Account Type (Read-only) -->
                            <div class="mb-3">
                                <label class="form-label">
                                    {{ __('lang.account_type') ?? 'Account Type' }}
                                </label>
                                <input 
                                    type="text" 
                                    class="form-control" 
                                    value="{{ ucfirst($user->type) }}" 
                                    readonly
                                    disabled
                                />
                                <small class="text-muted">{{ __('lang.account_type_cannot_be_changed') ?? 'Account type cannot be changed' }}</small>
                            </div>

                            <!-- Submit Button -->
                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bx bx-save me-2"></i>
                                    {{ __('lang.update_profile') ?? 'Update Profile' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Password Update Form -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="bx bx-lock me-2"></i>
                            {{ __('lang.update_password') ?? 'Update Password' }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('front.profile.password.update') }}" id="password-form">
                            @csrf
                            @method('PUT')

                            <!-- Current Password -->
                            <div class="mb-3">
                                <label for="current_password" class="form-label">
                                    {{ __('lang.current_password') ?? 'Current Password' }}
                                    <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <input 
                                        type="password" 
                                        class="form-control @error('current_password') is-invalid @enderror" 
                                        id="current_password" 
                                        name="current_password" 
                                        required
                                        placeholder="{{ __('lang.enter_current_password') ?? 'Enter current password' }}"
                                    />
                                    <span class="input-group-text cursor-pointer" onclick="togglePassword('current_password')">
                                        <i class="bx bx-hide" id="current_password_icon"></i>
                                    </span>
                                </div>
                                @error('current_password')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- New Password -->
                            <div class="mb-3">
                                <label for="password" class="form-label">
                                    {{ __('lang.new_password') ?? 'New Password' }}
                                    <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <input 
                                        type="password" 
                                        class="form-control @error('password') is-invalid @enderror" 
                                        id="password" 
                                        name="password" 
                                        required
                                        placeholder="{{ __('lang.enter_new_password') ?? 'Enter new password' }}"
                                    />
                                    <span class="input-group-text cursor-pointer" onclick="togglePassword('password')">
                                        <i class="bx bx-hide" id="password_icon"></i>
                                    </span>
                                </div>
                                @error('password')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Confirm Password -->
                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label">
                                    {{ __('lang.confirm_password') ?? 'Confirm Password' }}
                                    <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <input 
                                        type="password" 
                                        class="form-control @error('password_confirmation') is-invalid @enderror" 
                                        id="password_confirmation" 
                                        name="password_confirmation" 
                                        required
                                        placeholder="{{ __('lang.confirm_new_password') ?? 'Confirm new password' }}"
                                    />
                                    <span class="input-group-text cursor-pointer" onclick="togglePassword('password_confirmation')">
                                        <i class="bx bx-hide" id="password_confirmation_icon"></i>
                                    </span>
                                </div>
                                @error('password_confirmation')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Submit Button -->
                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bx bx-save me-2"></i>
                                    {{ __('lang.update_password') ?? 'Update Password' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Preview avatar before upload
        function previewAvatar(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('avatar-preview').src = e.target.result;
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        // Toggle password visibility
        function togglePassword(inputId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(inputId + '_icon');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('bx-hide');
                icon.classList.add('bx-show');
            } else {
                input.type = 'password';
                icon.classList.remove('bx-show');
                icon.classList.add('bx-hide');
            }
        }

        // Form submission with loading state
        document.addEventListener('DOMContentLoaded', function() {
            const profileForm = document.getElementById('profile-form');
            const passwordForm = document.getElementById('password-form');

            if (profileForm) {
                profileForm.addEventListener('submit', function(e) {
                    const submitBtn = this.querySelector('button[type="submit"]');
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>{{ __("lang.updating") ?? "Updating..." }}';
                    }
                });
            }

            if (passwordForm) {
                passwordForm.addEventListener('submit', function(e) {
                    const submitBtn = this.querySelector('button[type="submit"]');
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>{{ __("lang.updating") ?? "Updating..." }}';
                    }
                });
            }
        });
    </script>
@endpush

