<x-app-layout title="{{ __('Documents List') }}">
    @include('cabinet.documents._nav')

    <div class="flex justify-between items-center my-2">
        <h1 class="text-2xl font-semibold">{{ __('Invoices List') }}</h1>
        <x-links.success-link
            href="{{ route('cabinet.documents.create') }}">{{ __('Add New Document') }}</x-links.success-link>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full bg-white rounded shadow-md text-sm">
            <thead>
                <tr class="bg-gray-200 text-xs">
                    {{-- <th class="border border-gray-300 p-2">#</th> --}}
                    <th class="border border-gray-300 p-2 hidden sm:table-cell">{{ __('Docum. Date') }}</th>
                    <th class="border border-gray-300 p-2">{{ __('Document') }}</th>
                    <th class="border border-gray-300 p-2">{{ __('Payee') }}</th>
                    <th class="border border-gray-300 p-2 hidden sm:table-cell">{{ __('Amount') }}</th>
                    <th class="border border-gray-300 p-2 hidden sm:table-cell">{{ __('Due Date') }}</th>
                    <th class="border border-gray-300 p-2 hidden md:table-cell">{{ __('Note') }}</th>
                    <th class="border border-gray-300 p-2 hidden sm:table-cell">{{ __('Created At') }}</th>
                    <th class="border border-gray-300 p-2 hidden sm:table-cell">{{ __('Updated At') }}</th>
                    <th class="border border-gray-300 p-2">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($documents as $document)
                    <tr class="border-t hover:bg-gray-50">
                        {{-- <td class="border p-2">{{ $loop->iteration }}</td> --}}
                        <td class="border p-2 hidden sm:table-cell whitespace-nowrap">
                            {{ $document->document_date }}
                        </td>
                        <td class="border p-2 whitespace-nowrap max-w-xs overflow-hidden text-ellipsis">
                            <span>{{ $documentTypes[$document->document_type] ?? $document->document_type }}</span><br>
                            <div class="flex justify-between items-center">
                                <span>{{ __('Nr. ') . ($document->number ?? '-') }}</span>
                                <x-other.badge dataSet="statuses"
                                    :key="$document->status">{{ $statuses[$document->status] ?? $document->status }}</x-other.badge>
                            </div>
                        </td>
                        <td class="border p-2 whitespace-nowrap max-w-xs overflow-hidden text-ellipsis">
                            <strong>{{ $document->client->entity->fullName }}</strong><br>
                            <span>{{ __('ID Number') . ': ' . ($document->client->entity->id_number ?? '-') }}</span>
                        </td>
                        <td class="border p-2 hidden sm:table-cell text-right">
                            {{ number_format($document->document_total ?? 0, 2) }}</td>
                        <td class="border p-2 hidden sm:table-cell whitespace-nowrap">
                            {{ $document->due_date }}
                        </td>
                        <td class="border p-2 hidden md:table-cell">{{ $document->document_note }}</td>
                        <td class="border p-2 hidden md:table-cell">{{ $document->created_at }}</td>
                        <td class="border p-2 hidden sm:table-cell">{{ $document->updated_at }}</td>
                        <td class="border p-2 whitespace-nowrap">
                            <x-icons.pencil :route="route('cabinet.documents.edit', $document)" />
                            <x-icons.trash :route="route('cabinet.documents.destroy', $document)" :confirmMessage="__(
                                'Are you sure you want to delete this document? This action cannot be undone.',
                            )" />
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="border p-2 text-gray-600 text-center">{{ __('No documents found.') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $documents->links() }}</div>
</x-app-layout>
