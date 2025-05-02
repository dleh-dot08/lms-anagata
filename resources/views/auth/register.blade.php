<x-guest-layout>
    <div class="container-xxl">
        <div class="authentication-wrapper authentication-basic container-p-y">
          <div class="authentication-inner">
            <!-- Register Card -->
            <div class="card">
              <div class="card-body">
                <h4 class="mb-2">Mulai Bergabung 🚀</h4>
                <p class="mb-4">untuk akses pembelajaran!</p>
  
                <form id="formAuthentication" class="mb-3" action="{{ route('register') }}" method="POST">
                  {{ csrf_field() }}
  
              <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                  <div class="mb-3">
                    <label for="username" class="form-label">Nama Lengkap</label>
                    <input id="name" type="text" class="form-control" name="name" value="{{ old('name') }}" placeholder="Masukkan Nama Lengkap" required autofocus/>
                    @if ($errors->has('name'))
  <span class="help-block">
                        <strong>{{ $errors->first('name') }}</strong>
                    </span>
  @endif
                  </div>
              </div>
  
              <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                  <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" placeholder="Masukkan E-mail Anda" required/>
                    @if ($errors->has('email'))
  <span class="help-block">
                              <strong>{{ $errors->first('email') }}</strong>
                          </span>
  @endif
                  </div>
              </div>
  
              <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                  <div class="mb-3 form-password-toggle">
                    <label class="form-label" for="password">Password</label>
                    <div class="input-group input-group-merge">
                      <input id="password" type="password" class="form-control" name="password" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" required>
                      <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                    </div>
                  </div>
              </div>
  
              <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                  <div class="mb-3 form-password-toggle">
                    <label class="form-label" for="password-confirm">Confirm Password</label>
                    <div class="input-group input-group-merge">
                      <input id="password-confirm" type="password" class="form-control" name="password_confirmation" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" required>
                      <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                      @if ($errors->has('password'))
                      <span class="help-block">
                        <strong>{{ $errors->first('password') }}</strong>
                    </span>
  @endif
                    </div>
                  </div>
              </div>
  
                <br>
                  <button type="submit" class="btn btn-primary d-grid w-100">Sign up</button>
                </form>
  
                <p class="text-center">
                  <span>Sudah punya akun?</span>
                  <a href="{{ route('login') }}">
                    <span>Sign in</span>
                  </a>
                </p>
              </div>
            </div>
            <!-- Register Card -->
          </div>
        </div>
      </div>
</x-guest-layout>
