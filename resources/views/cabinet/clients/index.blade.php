<x-app-layout title="{{ __('Clients List') }}">
    @include('cabinet.clients._nav')

    <div class="flex justify-between items-center my-2">
        <h1 class="text-2xl font-semibold">{{ __('Clients List') }}</h1>
        <x-links.success-link href="{{ route('cabinet.clients.create') }}">{{ __('Add New Client') }}</x-links.success-link>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full bg-white rounded shadow-md text-sm">
            <thead>
                <tr class="bg-gray-200 text-xs">
                    {{-- <th class="border border-gray-300 p-2">#</th> --}}
                    <th class="border border-gray-300 p-2">{{ __('Client type') }}</th>
                    <th class="border border-gray-300 p-2">{{ __('Name') }}</th>
                    <th class="border border-gray-300 p-2 hidden sm:table-cell">{{ __('Address') }}</th>
                    <th class="border border-gray-300 p-2 hidden sm:table-cell">{{ __('Email') }}</th>
                    <th class="border border-gray-300 p-2 hidden md:table-cell">{{ __('Payment Terms') }}</th>
                    <th class="border border-gray-300 p-2 hidden md:table-cell">{{ __('Discount Rate') }}</th>
                    <th class="border border-gray-300 p-2 hidden sm:table-cell">{{ __('Created At') }}</th>
                    <th class="border border-gray-300 p-2">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($clients as $client)
                    <tr class="border-t hover:bg-gray-50">
                        {{-- <td class="border p-2">{{ $loop->iteration }}</td> --}}
                        <td class="border p-2">{{ $client->entity->type }}</td>
                        <td class="border p-2 whitespace-nowrap max-w-xs overflow-hidden text-ellipsis">
                            {{-- <strong>{{ $client->entity->name ?? $client->entity->first_name . ' ' . $client->entity->last_name }}</strong><br> --}}
                            <strong>{{ $client->entity->fullName }}</strong><br>
                            <span
                                class="text-xs">{{ __('ID Number') . ': ' . ($client->entity->id_number ?? '-') }}</span><br>
                            <span
                                class="text-xs">{{ __('VAT Number') . ': ' . ($client->entity->vat_number ?? '-') }}</span>
                        </td>
                        <td class="border p-2 hidden sm:table-cell">{{ $client->entity->fullAddress ?? __('N/A') }}</td>
                        <td class="border p-2 hidden sm:table-cell">{{ $client->email }}</td>
                        <td class="border p-2 hidden md:table-cell">{{ $client->due_days }} {{ __('days') }}
                        </td>
                        <td class="border p-2 hidden md:table-cell">{{ $client->discount_rate }}%</td>
                        <td class="border p-2 hidden sm:table-cell">{{ $client->created_at->format('Y-m-d') }}</td>
                        <td class="border p-2 whitespace-nowrap">
                            <x-icons.pencil :route="route('cabinet.clients.edit', $client)" />
                            <x-icons.trash :route="route('cabinet.clients.destroy', $client->id)" :confirmMessage="__('Are you sure you want to delete this client? This action cannot be undone.')" />
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="border p-2 text-gray-600 text-center">{{ __('No clients found.') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $clients->links() }}</div>
</x-app-layout>
