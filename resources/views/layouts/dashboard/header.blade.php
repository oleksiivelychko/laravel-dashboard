@php
    $locale = app()->getLocale();
@endphp

<header class="navbar navbar-dark sticky-top bg-dark flex-md-nowrap p-0 shadow">
    <a class="navbar-brand col-md-3 col-lg-2 me-0 px-3" href="{{ route('dashboard', $locale) }}">
        @component('components.laravel-logo')
            @slot('width', '40px')
        @endcomponent
        {{ config('app.name', 'Laravel') }}
    </a>
    <button class="navbar-toggler position-absolute d-md-none collapsed" data-bs-toggle="collapse" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="search-bar">
        <form method="post" action="{{ route('search', $locale) }}">
            @csrf
            <input class="form-control form-control-dark w-100"
                   type="search"
                   id="search"
                   placeholder="🔎&nbsp;&nbsp;{{ __('dashboard.search') }} . . ."
                   aria-label="{{ __('dashboard.search') }}">
        </form>
        <div id="searchResults" class="d-none"></div>
    </div>

    @include('components.change-locale')

    <ul class="navbar-nav px-3">
        <li class="nav-item text-nowrap">
            <form method="post" action="{{ route('logout', $locale) }}">
                @csrf
                <a class="nav-link" href="{{ route('logout', $locale) }}"
                   onclick="event.preventDefault();this.closest('form').submit();">
                    {{ __('auth.logout') }}
                </a>
            </form>
        </li>
    </ul>
</header>
