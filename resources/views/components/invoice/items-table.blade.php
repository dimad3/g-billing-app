<section class="bg-white">
    <table class="min-w-full table-auto border-collapse border border-gray-300 shadow-sm overflow-hidden">
        <caption class="caption-top">
            {{ __('Invoice Items') }}
        </caption>
        <thead class="bg-gray-200 border border-gray-300 text-center text-gray-700 text-xs">
            <tr>
                <th class="px-1 py-1 border border-gray-300 w-12 md:w-12"><x-buttons.add-button onclick="addRow()" /></th>
                <th class="px-1 py-1 border border-gray-300 w-48 md:w-80">{{ __('Title') }}</th>
                <th class="px-1 py-1 border border-gray-300 w-16 md:w-16">{{ __('Unit') }}</th>
                <th class="px-1 py-1 border border-gray-300 w-24 md:w-24">{{ __('Quantity') }}</th>
                <th class="px-1 py-1 border border-gray-300 w-24 md:w-24">{{ __('Price') }}</th>
                <th class="px-1 py-1 border border-gray-300 w-24 md:w-24">{{ __('Amount') }}</th>
                <th class="px-1 py-1 border border-gray-300 w-16 md:w-16">{{ __('Discount %') }}</th>
                <th class="px-1 py-1 border border-gray-300 w-24 md:w-24">{{ __('Discount') }}</th>
                <th class="px-1 py-1 border border-gray-300 w-24 md:w-24">{{ __('Net Amount') }}</th>
                <th class="px-1 py-1 border border-gray-300 w-16 md:w-16">{{ __('VAT %') }}</th>
                <th class="px-1 py-1 border border-gray-300 w-24 md:w-12">
                    <!-- Empty header for delete button -->
                </th>
            </tr>
        </thead>

        <tbody>
            @php
                use \Illuminate\Support\Collection;

                // Prepare items collection
                if (old('items')) {
                    // display old values if available (e.g., after validation failure)
                    $items = collect(old('items'));
                } elseif ($document->exists) {
                    // display existing values if document exists (e.g., editing an existing document)
                    $items = $document->documentItems;
                } else {
                    // $items = new Collection();
                    $items = new Collection();
                }
            @endphp

            <x-invoice.item-rows :items="$items" />

            <x-invoice.summary-rows :document="$document" />
        </tbody>
    </table>
</section>
