<ul class="flex space-x-3 border-b">
    <li>
        <x-links.nav-link :href="route('cabinet.home')" :active="$page === 'dashboard'">
            {{ __('Dashboard') }}
        </x-links.nav-link>
    </li>
    <li>
        <x-links.nav-link :href="route('cabinet.documents.index')" :active="$page === 'documents'">
            {{ __('Invoices') }}
        </x-links.nav-link>
    </li>
    <li>
        <x-links.nav-link :href="route('cabinet.clients.index')" :active="$page === 'clients'">
            {{ __('Clients') }}
        </x-links.nav-link>
    </li>
    <li>
        <x-links.nav-link :href="route('cabinet.seller')" :active="$page === 'seller'">
            {{ __('Seller') }}
        </x-links.nav-link>
    </li>
    <li>
        <x-links.nav-link :href="route('cabinet.agents.index')" :active="$page === 'agents'">
            {{ __('Employees') }}
        </x-links.nav-link>
    </li>
    <li>
        <x-links.nav-link :href="route('cabinet.banks.index')" :active="$page === 'banks'">
            {{ __('Banks') }}
        </x-links.nav-link>
    </li>
    <li>
        <x-links.nav-link :href="route('cabinet.settings')" :active="$page === 'settings'">
            {{ __('Invoice Settings') }}
        </x-links.nav-link>
    </li>
    <li>
        <x-links.nav-link :href="route('profile.edit')" :active="$page === 'profile'">
            {{ __('Profile') }}
        </x-links.nav-link>
    </li>
</ul>
