<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        {{-- Name field --}}
        <x-adminlte-input name="name" label="Name" placeholder="John Doe"
            icon="fas fa-user" value="{{ old('name') }}" required autofocus
            autocomplete="name" />

        {{-- Email field --}}
        <x-adminlte-input name="email" type="email" label="Email" placeholder="email@example.com"
            icon="fas fa-envelope" value="{{ old('email') }}" required 
            autocomplete="username" />

        {{-- Password field --}}
        <x-adminlte-input name="password" type="password" label="Password"
            placeholder="password" icon="fas fa-lock" required
            autocomplete="new-password" />

        {{-- Confirm Password field --}}
        <x-adminlte-input name="password_confirmation" type="password" label="Confirm Password"
            placeholder="confirm password" icon="fas fa-lock" required
            autocomplete="new-password" />

        {{-- Register button --}}
        <x-adminlte-button type="submit" label="Register" theme="primary"
            class="btn-block" icon="fas fa-user-plus"/>

        <a class="btn btn-link p-0" href="{{ route('login') }}">
            Already registered?
        </a>

    </form>
</x-guest-layout>