<x-app-layout title="{{ __('Employees List') }}">
    @include('cabinet.agents._nav')

    <div class="flex justify-between items-center my-2">
        <h1 class="text-2xl font-semibold">{{ __('Employees List') }}</h1>
        <x-links.success-link href="{{ route('cabinet.agents.create') }}">{{ __('Add New Employee') }}</x-links.success-link>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full bg-white rounded shadow-md text-sm">
            <thead>
                <tr class="bg-gray-200 text-xs">
                    <th class="border border-gray-300 p-2">#</th>
                    <th class="border border-gray-300 p-2">{{ __('First Name') }}</th>
                    <th class="border border-gray-300 p-2">{{ __('Last Name') }}</th>
                    <th class="border border-gray-300 p-2 hidden sm:table-cell">{{ __('Position') }}</th>
                    <th class="border border-gray-300 p-2 hidden sm:table-cell">{{ __('Email') }}</th>
                    <th class="border border-gray-300 p-2 hidden md:table-cell">{{ __('Role') }}</th>
                    <th class="border border-gray-300 p-2">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($agents as $agent)
                    <tr class="border-t hover:bg-gray-50">
                        <td class="border p-2">{{ $loop->iteration }}</td>
                        <td class="border p-2">{{ $agent->first_name }}</td>
                        <td class="border p-2 whitespace-nowrap max-w-xs overflow-hidden text-ellipsis">
                            {{ $agent->last_name }}
                        </td>
                        <td class="border p-2 hidden sm:table-cell">{{ $agent->position }}</td>
                        <td class="border p-2 hidden sm:table-cell">{{ $agent->email }}</td>
                        <td class="border p-2 hidden md:table-cell">{{ $agent->role }}</td>
                        <td class="border p-2 whitespace-nowrap">
                            <x-icons.pencil :route="route('cabinet.agents.edit', $agent)" />
                            <x-icons.trash :route="route('cabinet.agents.destroy', $agent->id)" :confirmMessage="__('Are you sure you want to delete this employee? This action cannot be undone.')" />
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="border p-2 text-gray-600 text-center">{{ __('No agents found.') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $agents->links() }}</div>
</x-app-layout>
