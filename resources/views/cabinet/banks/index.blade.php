<x-app-layout title="{{ __('Banks List') }}">
    @include('cabinet.banks._nav')

    <div class="flex justify-between items-center my-2">
        <h1 class="text-2xl font-semibold">{{ __('Banks List') }}</h1>
        <x-links.success-link href="{{ route('cabinet.banks.create') }}">{{ __('Add New Bank') }}</x-links.success-link>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full bg-white rounded shadow-md text-sm">
            <thead>
                <tr class="bg-gray-200 text-xs">
                    <th class="border border-gray-300 p-2">#</th>
                    <th class="border border-gray-300 p-2">{{ __('Bank Name') }}</th>
                    <th class="border border-gray-300 p-2">{{ __('Bank Code') }}</th>
                    <th class="border border-gray-300 p-2">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($banks as $bank)
                    <tr class="border-t hover:bg-gray-50">
                        <td class="border p-2">{{ $loop->iteration }}</td>
                        <td class="border p-2 whitespace-nowrap max-w-xs overflow-hidden text-ellipsis">
                            {{ $bank->name }}
                        </td>
                        <td class="border p-2">{{ $bank->bank_code }}</td>
                        <td class="border p-2 whitespace-nowrap">
                            <x-icons.pencil :route="route('cabinet.banks.edit', $bank)" />
                            <x-icons.trash :route="route('cabinet.banks.destroy', $bank->id)" :confirmMessage="__('Are you sure you want to delete this bank? This action cannot be undone.')" />
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="border p-2 text-gray-600 text-center">{{ __('No banks found.') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $banks->links() }}</div>
</x-app-layout>
