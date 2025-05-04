<x-app-layout title="Welcome!">
    <!-- Hero Section -->
    <section class="text-center py-20 bg-gradient-to-r from-blue-500 to-indigo-600 text-white">
        <div class="container mx-auto">
            <h1 class="text-5xl font-extrabold">{{ __('Welcome to Billing System') }}</h1>
            <p class="mt-4 text-lg text-gray-200">{{ __('Manage your invoices, payments, and clients efficiently.') }}
            </p>
            <div class="mt-6">
                <a href="{{ route('cabinet.home') }}"
                    class="bg-white text-blue-600 px-6 py-3 rounded-lg shadow-md font-semibold hover:bg-gray-200 transition">
                    {{ __('Dashboard') }}
                </a>
                @guest
                    <a href="{{ route('register') }}"
                        class="ml-4 bg-yellow-500 px-6 py-3 rounded-lg text-white shadow-md font-semibold hover:bg-yellow-600 transition">
                        {{ __('Get Started') }}
                    </a>
                @endguest
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="container mx-auto my-16 px-6 grid grid-cols-1 md:grid-cols-3 gap-8">
        <div class="p-6 bg-white rounded-lg shadow-lg text-center">
            <h3 class="text-xl font-semibold text-gray-800">{{ __('Manage Invoices') }}</h3>
            <p class="mt-2 text-gray-600">{{ __('Create and manage your invoices with ease.') }}</p>
        </div>
        <div class="p-6 bg-white rounded-lg shadow-lg text-center">
            <h3 class="text-xl font-semibold text-gray-800">{{ __('Track Payments') }}</h3>
            <p class="mt-2 text-gray-600">{{ __('Keep track of all your payments and due dates.') }}</p>
        </div>
        <div class="p-6 bg-white rounded-lg shadow-lg text-center">
            <h3 class="text-xl font-semibold text-gray-800">{{ __('Client Management') }}</h3>
            <p class="mt-2 text-gray-600">{{ __('Manage your clients and their billing information.') }}</p>
        </div>
    </section>
</x-app-layout>
