@props([
    'document',
])

<!-- Summary Row -->
<tr class="bg-gray-100 font-semibold" id="summary-row">
    <td colspan="3" class="px-1 py-1 border border-gray-200 text-right">
        {{ __('Totals:') }}
    </td>
    <td class="px-1 py-1 border border-gray-200 text-right text-blue-800 " id="sumOfQuantity">0.00</td>
    <td class="px-1 py-1 border border-gray-200 text-center text-blue-800 ">x</td>
    <td class="px-1 py-1 border border-gray-200 text-right text-blue-800 " id="sumOfAmount">0.00</td>
    <td class="px-1 py-1 border border-gray-200 text-center text-blue-800 ">x</td>
    <td class="px-1 py-1 border border-gray-200 text-right text-blue-800 " id="sumOfDiscount">0.00</td>
    <td class="px-1 py-1 border border-gray-200 text-right text-blue-800 " id="totalNetAmount">0.00</td>
    <td class="px-1 py-1 border border-gray-200" colspan="3"></td>
</tr>

<!-- VAT Row -->
<tr class="bg-gray-100 font-semibold" id="vat-row">
    <td colspan="8" class="px-1 py-1 border border-gray-200 text-right">
        {{ __('VAT:') }}
    </td>
    <td class="px-1 py-1 border border-gray-200 text-right text-blue-800 " id="vat">0.00</td>
    <td class="px-1 py-1 border border-gray-200" colspan="3"></td>
</tr>

<!-- Total Row -->
<tr class="bg-gray-100 font-semibold" id="total-row">
    <td colspan="8" class="px-1 py-1 border border-gray-200 text-right">
        {{ __('Amount VAT incl.:') }}
    </td>
    <td class="px-1 py-1 border border-gray-200 text-right text-blue-800 " id="total">0.00</td>
    <td class="px-1 py-1 border border-gray-200" colspan="3"></td>
</tr>

<!-- Advance Paid Row -->
<tr class="bg-gray-100 font-semibold" id="advance-row">
    <td colspan="8" class="px-1 py-1 border border-gray-200 text-right">
        {{ __('Advance Paid:') }}
    </td>
    <td class="px-0 py-0">
        <input type="text" name="advance_paid"
            value="{{ old('advance_paid', number_format($document->advance_paid, 2)) }}" autocomplete='off'
            class="no-border px-1 py-1 w-full border-0 text-gray-800 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 outline-none text-right"
            id="advance_paid" onchange="calculatePayableAmount()">
    </td>
    <td class="px-1 py-1 border border-gray-200" colspan="3"></td>
</tr>

<!-- Payable Amount Row -->
<tr class="bg-gray-100 font-semibold" id="payable-row">
    <td colspan="8" class="px-1 py-1 border border-gray-200 text-right">
        {{ __('Payable Amount:') }}</td>
    <td class="px-1 py-1 border border-gray-200 text-right text-blue-800 " id="payableAmount">0.00/td>
    <td class="px-1 py-1 border border-gray-200" colspan="3"></td>
</tr>
