@extends('layouts.guest')

@section('title', 'Sign in')

@section('content')
    <h1 class="page-title" style="margin: 0 0 0.35rem; font-size: 1.75rem;">Welcome back</h1>
    <p class="page-subtitle" style="margin: 0 0 1.5rem;">Sign in to PRITECH Issue Tracker</p>

    @if (session('status'))
        <div class="flash flash--success" style="margin-bottom: 1rem;">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" class="form-control" value="{{ old('email') }}" required autofocus autocomplete="username">
            @error('email')<div class="form-error">{{ $message }}</div>@enderror
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" class="form-control" required autocomplete="current-password">
            @error('password')<div class="form-error">{{ $message }}</div>@enderror
        </div>

        <div class="form-group">
            <label class="meta" style="display: inline-flex; align-items: center; gap: 0.4rem;">
                <input type="checkbox" name="remember" style="accent-color: var(--strawberry);">
                Remember me
            </label>
        </div>

        <button type="submit" class="btn btn--primary" style="width: 100%;">Sign in</button>
    </form>
@endsection
