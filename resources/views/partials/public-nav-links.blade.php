@php
    $loginButtonClass = $loginButtonClass ?? 'btn-nav';
@endphp

<a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">Home</a>
<a href="{{ route('home') }}#about">About</a>
<a href="{{ route('products.index') }}" class="{{ request()->routeIs('products.*') ? 'active' : '' }}">Products</a>
<a href="{{ route('concessionaires.index') }}" class="{{ request()->routeIs('concessionaires.*') ? 'active' : '' }}">Concessionaires</a>
@guest
    <a href="{{ route('application') }}" class="{{ request()->routeIs('partnership.*') || request()->routeIs('application*') ? 'active' : '' }}">Partner With Us</a>
@endguest
@auth
    @include('partials.public-profile-dropdown')
@else
    <a href="{{ route('login') }}" class="{{ $loginButtonClass }}">Log in</a>
@endauth