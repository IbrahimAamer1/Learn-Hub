
@section('title')
    register page
@endsection
@section('logo')
@endsection
<!DOCTYPE html>
<html
  lang="en"
  class="light-style customizer-hide"
  dir="ltr"
  data-theme="theme-default"
  data-assets-path="{{ asset('assets-front') }}/"
  data-template="vertical-menu-template-free"
>
    @include('front.partials.AuthHead')
  <body>
  <!-- Content -->

  <div class="container-xxl">
      <div class="authentication-wrapper authentication-basic container-p-y">
        <div class="authentication-inner">
          <!-- Register Card -->
          <div class="card">
            <div class="card-body">
                @include('front.partials.AuthLogo')
             
              <h4 class="mb-2">Adventure starts here 🚀</h4>
              <p class="mb-4">Make your learning easy and fun!</p>

              <form id="formAuthentication" class="mb-3" action="{{ route('register') }}" method="POST">
                @csrf

                <!-- Name -->
                <div class="mb-3">
                  <label for="username" class="form-label">Username</label>
                  <input
                    type="text"
                    class="form-control"
                    id="username"
                    name="name"
                    :value="old('name')"
                    required
                    autofocus
                    placeholder="Enter your username"
                    autofocus
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />

                  />
                </div>
                <!-- Email -->
                <div class="mb-3">
                  <label for="email" class="form-label">Email</label>
                  <input type="text" class="form-control" id="email" name="email" placeholder="Enter your email" />
                  <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <!-- User Type -->
                <div class="mb-3">
                  <label class="form-label">{{ __('lang.account_type') ?? 'Account Type' }} <span class="text-danger">*</span></label>
                  <div class="form-check">
                    <input class="form-check-input" type="radio" name="type" id="type_student" value="student" {{ old('type', 'student') === 'student' ? 'checked' : '' }} required>
                    <label class="form-check-label" for="type_student">
                      {{ __('lang.student') ?? 'Student' }}
                    </label>
                  </div>
                  <div class="form-check">
                    <input class="form-check-input" type="radio" name="type" id="type_instructor" value="instructor" {{ old('type') === 'instructor' ? 'checked' : '' }}>
                    <label class="form-check-label" for="type_instructor">
                      {{ __('lang.instructor') ?? 'Instructor' }}
                    </label>
                  </div>
                  <x-input-error :messages="$errors->get('type')" class="mt-2" />
                </div>

                <!-- Password -->
                <div class="mb-3 form-password-toggle">
                  <label class="form-label" for="password">Password</label>
                  <div class="input-group input-group-merge">
                    <input
                      type="password"
                      id="password"
                      class="form-control"
                      name="password"
                      :value="old('password')"
                      required
                      autofocus
                      autocomplete="password"
                      placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                      aria-describedby="password"
                    />
                    <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                  </div>
                  <!-- Confirm Password -->
                  <div class="mb-3 form-password-toggle">
                    <label class="form-label" for="password_confirmation">Confirm Password</label>
                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="Confirm your password" />
                  </div>
                  <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                </div>

              <!--   <div class="mb-3">
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="terms-conditions" name="terms" />
                    <label class="form-check-label" for="terms-conditions">
                      I agree to
                      <a href="javascript:void(0);">privacy policy & terms</a>
                    </label>
                  </div>
                </div> -->
                <button type="submit"  class="btn btn-primary d-grid w-100">Sign up</button>
              </form>

              <p class="text-center">
                <span>Already have an account?</span>
                <a href="{{ route('front.login') }}">
                  <span>Sign in instead</span>
                </a>
              </p>
            </div>
          </div>
          <!-- Register Card -->
        </div>
      </div>
    </div>

    <!-- / Content -->

   

    @include('front.partials.AuthScripts')
  </body>
</html>
