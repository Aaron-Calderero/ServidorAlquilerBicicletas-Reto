<nav x-data="{ open: false }" class="">
    <!-- Primary Navigation Menu -->
    <div class="">
        <div class="">
            <div class="">
                <!-- Logo -->
                <div class="">
                    <a href="{{ route('bikes.index') }}">
                        <x-application-logo class="" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="">
                    <x-nav-link :href="route('bikes.index')" :active="request()->routeIs('bikes.*')">
                        {{ __('Catálogo') }}
                    </x-nav-link>
                    <x-nav-link :href="route('bookings.index')" :active="request()->routeIs('bookings.*')">
                        {{ __('Mis reservas') }}
                    </x-nav-link>
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="">
                            <div>{{ Auth::user()->name }}</div>

                            <div class="">
                                <svg class="" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="">
                <button @click="open = ! open" class="">
                    <svg class="" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="" class="" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="" class="" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="" class="">
        <div class="">
            <x-nav-link :href="route('bikes.index')" :active="request()->routeIs('bikes.*')">
                {{ __('Catálogo') }}
            </x-nav-link>
            <x-nav-link :href="route('bookings.index')" :active="request()->routeIs('bookings.*')">
                {{ __('Mis reservas') }}
            </x-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        <div class="">
            <div class="">
                <div class="">{{ Auth::user()->name }}</div>
                <div class="">{{ Auth::user()->email }}</div>
            </div>

            <div class="">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
