<x-guest-layout>
    <form method="POST" action="{{ route('login') }}">
        @csrf

        {{-- Email field --}}
        <x-adminlte-input name="email" type="email" label="Email" placeholder="email@example.com"
            icon="fas fa-envelope" value="{{ old('email') }}" required autofocus 
            autocomplete="username" />

        {{-- Password field --}}
        <x-adminlte-input name="password" type="password" label="Password"
            placeholder="password" icon="fas fa-lock" required
            autocomplete="current-password" />

        {{-- Remember Me Checkbox --}}
        <div class="d-flex justify-content-between">
            <x-adminlte-input-switch name="remember" label="Remember Me" data-on-color="primary" />

            @if (Route::has('password.request'))
                <a class="btn btn-link" href="{{ route('password.request') }}">
                    Forgot your password?
                </a>
            @endif
        </div>

        {{-- Login button --}}
        <x-adminlte-button type="submit" label="Log In" theme="primary"
            class="btn-block" icon="fas fa-sign-in-alt"/>

    </form>
</x-guest-layout>