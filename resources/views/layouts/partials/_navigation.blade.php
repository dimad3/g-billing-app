<nav class="bg-gray-900 text-white p-4">
    <div class="container mx-auto flex justify-between items-center">
        <!-- Logo -->
        <a href="{{ route('home') }}" class="text-lg font-bold">{{ __('Home') }}</a>

        <!-- Navigation Links -->
        <div class="flex items-center space-x-4">
            @auth
                <span class="text-gray-300">{{ __('Welcome') }},
                    <a href="{{ route('profile.edit') }}" class="text-white hover:underline">
                        {{ auth()->user()->name }}!
                    </a>
                </span>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-buttons.danger-button>
                        {{ __('Logout') }}
                    </x-buttons.danger-button>
                </form>
            @else
                <x-links.success-link href="{{ route('login') }}">
                    {{ __('Login') }}
                </x-links.success-link>

                <x-links.warning-link href="{{ route('register') }}">
                    {{ __('Register') }}
                </x-links.warning-link>
            @endguest
        </div>
    </div>
</nav>
