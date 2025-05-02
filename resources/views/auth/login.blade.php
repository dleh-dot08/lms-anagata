<x-guest-layout>
    <div class="container-xxl">
        <div class="authentication-wrapper authentication-basic container-p-y">
          <div class="authentication-inner">
            <div class="card">
              <div class="card-body">
                <h4 class="mb-2">Selamat Datang! 👋</h4>
                <p class="mb-4">Log-in untuk mengakses fitur</p>
  
                <form id="formAuthentication" method="POST" action="{{ route('login') }}">
                  {{ csrf_field() }}
  
              <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                  <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" placeholder="Masukkan E-mail Anda" autofocus/>
                    @if ($errors->has('email'))
  <span class="help-block">
                                          <strong>{{ $errors->first('email') }}</strong>
                                      </span>
  @endif
                  </div>
  
              <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                  <div class="mb-3 form-password-toggle">
                    <div class="d-flex justify-content-between" >
                      <label class="form-label" for="password">Password</label>
                      @if ($errors->has('password'))
  <span class="help-block">
                              <strong>{{ $errors->first('password') }}</strong>
                          </span>
  @endif
                    </div>
                    <div class="input-group input-group-merge">
                      <input
                          type="password"
                          id="password"
                          class="form-control"
                          name="password"
                          placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                          aria-describedby="password"
                      />
                      <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                  </div>
                  <div class="d-flex justify-content-end mt-2">
                      @if (Route::has('password.request'))
  <a class="underline text-sm text-gray-600 hover:text-gray-900" href="{{ route('password.request') }}">
                              {{ __('Lupa Password?') }}
                          </a>
  @endif
                  </div>
                  <div class="mb-3">
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }} />
                      <label class="form-check-label" for="remember-me"> Remember Me </label>
                    </div>
                  </div>
                  <div class="mb-3">
                    <button class="btn btn-primary d-grid w-100" type="submit">Sign in</button>
                  </div>
                </form>
  
                <p class="text-center">
                  <span>Baru bergabung?</span>
                  <a href="{{ route('register') }}">
                    <span>Buat Akun</span>
                  </a>
                </p>
              </div>
              </div>
            </div>
          </div>
        </div>
      </div>
</x-guest-layout>
