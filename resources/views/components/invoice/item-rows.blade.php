@props(['items'])
@foreach ($items as $index => $item)
    <tr data-row-index="{{ $index }}" class="">
        <td class="px-0 py-0 border border-gray-100 text-gray-400 text-center">{{ $index + 1 }}</td>
        <td class="px-0 py-0 border border-gray-100 text-gray-400 text-center">
            <input type="text" name="items[{{ $index }}][name]"
                value="{{ old('items.' . $index . '.name', $item['name']) }}"
                class="no-border px-1 py-1 w-full border-0 text-gray-800 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 outline-none truncate">
        </td>
        <td class="px-0 py-0 border border-gray-100 text-gray-400 text-center">
            <input type="text" name="items[{{ $index }}][unit]"
                value="{{ old('items.' . $index . '.unit', $item['unit']) }}"
                class="no-border px-1 py-1 w-full border-0 text-gray-800 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 outline-none">
        </td>
        <td class="px-0 py-0 border border-gray-100 text-gray-400 text-center">
            <input type="text" name="items[{{ $index }}][quantity]"
                value="{{ old('items.' . $index . '.quantity', $item['quantity']) }}" autocomplete='off'
                class="no-border px-1 py-1 w-full border-0 text-gray-800 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 outline-none text-right"
                data-quantity="{{ $index }}" onchange="calculateRowValues({{ $index }})">
        </td>
        <td class="px-0 py-0 border border-gray-100 text-gray-400 text-center">
            <input type="text" name="items[{{ $index }}][price]"
                value="{{ old('items.' . $index . '.price', format_decimal_with_precision($item['price'])) }}"
                autocomplete='off'
                class="no-border px-1 py-1 w-full border-0 text-gray-800 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 outline-none text-right"
                onchange="calculateRowValues({{ $index }})">
        </td>
        <td class="px-1 py-1 border border-gray-100 text-blue-700 text-right" data-amount="{{ $index }}">
            {{ number_format($item->amount ?? 0, 2) }}
        </td>
        <td class="px-0 py-0 border border-gray-100 text-gray-400 text-center">
            <input type="text" name="items[{{ $index }}][discount_rate]" autocomplete='off'
                value="{{ old('items.' . $index . '.discount_rate', $item['discount_rate']) }}" autocomplete='off'
                class="no-border px-1 py-1 w-full border-0 text-gray-800 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 outline-none text-right"
                onchange="calculateRowValues({{ $index }})">
        </td>
        <td class="px-1 py-1 border border-gray-100 text-blue-700 text-right" data-discount="{{ $index }}">
            {{ number_format($item->discount ?? 0, 2) }}
        </td>
        <td class="px-1 py-1 border border-gray-100 text-blue-700 text-right" data-net-amount="{{ $index }}">
            {{ number_format($item->net_amount ?? 0, 2) }}
        </td>
        <td class="px-0 py-0 border border-gray-100 text-gray-400 text-center">
            <input type="text" name="items[{{ $index }}][tax_rate]"
                value="{{ old('items.' . $index . '.tax_rate', $item['tax_rate']) }}"
                class="no-border px-1 py-1 w-full border-0 text-gray-800 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 outline-none text-right"
                onchange="calculateRowValues({{ $index }})">
        </td>
        <td class="px-0 py-0 border border-gray-100 text-center">
            <x-buttons.remove-button onclick="deleteRow(this)" />
        </td>
    </tr>
@endforeach
