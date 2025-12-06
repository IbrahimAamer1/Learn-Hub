
@section('title')
    verify email page
@endsection
@section('logo')
@endsection
<!DOCTYPE html>
<html
  lang="en"
  class="light-style customizer-hide"
  dir="ltr"
  data-theme="theme-default"
  data-assets-path="{{ asset('assets-back') }}/"
  data-template="vertical-menu-template-free"
>
    @include('back.partials.AuthHead')
  <body>
   <!-- Content -->

   <div class="container-xxl">
      <div class="authentication-wrapper authentication-basic container-p-y">
        <div class="authentication-inner py-4">
          <!-- Forgot Password -->
          <div class="card">
            <div class="card-body">
                @include('back.partials.AuthLogo')
              <h4 class="mb-2">Verify Email</h4>
              <p class="mb-4">Click the button below to verify your email address.</p>


              <x-auth-session-status class="mb-4" :status="session('status')" />
              <form id="formAuthentication" class="mb-3" action="{{ route('verification.send') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <button type="submit" class="btn btn-primary d-grid w-100"> Send Verification Email</button>
                </div>
                
              </form>
              
              <form method="POST" action="{{ route('logout') }}">
                  @csrf
                  
                   <button type="submit" class="btn btn-primary d-grid w-100">  {{ __('Log Out') }}  </button>   
                </form>
            </div>
          </div>
          <!-- /Forgot Password -->
        </div>
      </div>
    </div>

    <!-- / Content -->

   

    @include('back.partials.AuthScripts')
  </body>
</html>
