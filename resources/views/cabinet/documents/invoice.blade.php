<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $documentType }} #{{ $document->number }}</title>
    <style>
        @page {
            margin-top: 1.5cm;
            margin-bottom: 1.5cm;
            margin-left: 2cm;
            margin-right: 1cm;
        }

        body {
            font-size: 11px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            white-space: normal;
            /* word-wrap: break-word; */
        }

        thead th {
            padding-top: 8px;
            padding-bottom: 8px;
            padding-right: 2px;
            padding-left: 2px;
            /* text-align: center; */
            font-weight: 600;
            /* border-top: 1px solid black;
            border-bottom: 1px solid black; */
        }

        tbody td {
            /* padding-top: 4px;
            padding-bottom: 4px;
            padding-right: 2px;
            padding-left: 2px; */
            /* font-size: 12px; */
        }

        /* tbody tr {
            border-bottom: 0.5px solid lightgray;
        } */

        tfoot td {
            padding: 2px;
            font-weight: normal;
        }

        table td,
        table th {
            vertical-align: top;
        }

        /* id selectors -------------------------------------------------------------------- */

        /* #items-tbody {
            padding: 8px;
        } */

        /* class selectors -------------------------------------------------------------------- */

        /* .entity-table {
            padding: 8px;
        } */

        /* .entity-label {
            font-weight: bold;
        }
 */
        .border-top {
            border-top: 1px solid black;
        }

        .border-bottom {
            border-bottom: 1px solid black;
        }

        .border-lightgray {
            border-bottom: 0.5px solid gray;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .text-justify {
            text-align: justify;
        }

        .text-left {
            text-align: left;
        }

        /* .text-top {
            vertical-align: top;
        } */

        .font-bold {
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div>
        <h2 style="margin: 0;">{{ $documentType }}</h2>
        {{ __('No.') }}: {{ $document->number }}</br>
        {{ __('Making Date') }}: {{ $document->document_date }}
    </div>

    <!-- Combined Seller and Client Information Table -->
    <table id="entity-table" class="border-top border-bottom">
        <tbody>
            {{-- Seller Section --}}
            <tr>
                <td class="entity-label font-bold">{{ __('Supplier') }}:</td>
                <td class="font-bold">{{ $seller->fullName }}</td>
                {{-- Horisintal Spacer Column --}} <td>{{ '  ' }}</td>
                <td class="entity-label font-bold">{{ __('Reg. No') }}:</td>
                <td class="font-bold">{{ $seller->id_number ?? __('N/A') }}</td>
            </tr>
            <tr>
                <td class="entity-label" style="white-space: nowrap;">{{ __('Reg. address') }}:</td>
                <td>{{ $seller->fullAddress }}</td>
                {{-- Horisintal Spacer Column --}} <td>{{ '  ' }}</td>
                <td class="entity-label">{{ __('VAT No') }}:</td>
                <td>{{ $seller->vat_number ?? __('N/A') }}</td>
            </tr>
            @foreach ($seller->bankAccounts as $bankAccount)
                <tr>
                    <td class="entity-label">{{ $loop->first ? __('Bank') . ':' : '' }}</td>
                    <td>{{ $bankAccount->bank->fullName ?? __('N/A') }}</td>
                    {{-- Horisintal Spacer Column --}} <td>{{ '  ' }}</td>
                    <td class="entity-label" style="white-space: nowrap;">
                        {{ $loop->first ? __('Account No') . ':' : '' }}</td>
                    <td>{{ $bankAccount->bank_account ?? __('N/A') }}</td>
                </tr>
            @endforeach

            {{-- Vertical Spacer Row --}}
            <tr>
                <td colspan="5" class="border-top border-bottom" style="padding-top: 10px;"></td>
            </tr>

            {{-- Client Section --}}
            <tr class="font-bold">
                <td class="entity-label font-bold">{{ __('Payee') }}:</td>
                <td class="font-bold">{{ $client->fullName }}</td>
                {{-- Horisintal Spacer Column --}} <td>{{ '  ' }}</td>
                <td class="entity-label font-bold">{{ __('Reg. No') }}:</td>
                <td class="font-bold">{{ $client->id_number ?? __('N/A') }}</td>
            </tr>
            <tr>
                <td class="entity-label">{{ __('Reg. address') }}:</td>
                <td>{{ $client->fullAddress }}</td>
                {{-- Horisintal Spacer Column --}} <td>{{ '  ' }}</td>
                <td class="entity-label">{{ __('VAT No') }}:</td>
                <td>{{ $client->vat_number ?? __('N/A') }}</td>
            </tr>
            @foreach ($client->bankAccounts as $bankAccount)
                <tr>
                    <td class="entity-label">{{ $loop->first ? __('Bank') . ':' : '' }}</td>
                    <td>{{ $bankAccount->bank->fullName ?? __('N/A') }}</td>
                    {{-- Horisintal Spacer Column --}} <td>{{ '  ' }}</td>
                    <td class="entity-label">{{ $loop->first ? __('Account No') . ':' : '' }}</td>
                    <td>{{ $bankAccount->bank_account ?? __('N/A') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Items Table -->
    <table>
        <thead style="font-size: 10px;">
            <tr class="border-top border-bottom" style="font-size: 9px;">
                <th class="text-left">#</th>
                <th class="text-left" style="width: 25%">{{ __('Title') }}</th>
                <th class="text-left">{{ __('Unit') }}</th>
                <th class="text-right">{{ __('Quantity') }}</th>
                <th class="text-right">{{ __('Price') }}</th>
                <th class="text-right">{{ __('Amount') }}</th>
                <th class="text-right">{{ __('Discount %') }}</th>
                <th class="text-right">{{ __('Discount') }}</th>
                <th class="text-right">{{ __('VAT %') }}</th>
                <th class="text-right">{{ __('Net Amount') }}</th>
            </tr>
        </thead>
        <tbody id="items-tbody">
            @foreach ($document->documentItems as $index => $item)
                <tr class="{{ $loop->last ? 'border-bottom' : 'border-lightgray' }}">
                    <td class="text-left">{{ $index + 1 }}</td>
                    <td class="text-left">{{ $item->name }}</td>
                    <td class="text-left">{{ $item->unit }}</td>
                    <td class="text-right">{{ $item->quantity }}</td>
                    <td class="text-right">{{ format_decimal_with_precision($item->price, 2) }}</td>
                    <td class="text-right">{{ number_format($item->amount, 2) }}</td>
                    <td class="text-right">{{ $item->discount_rate }}%</td>
                    <td class="text-right">{{ number_format($item->discount, 2) }}</td>
                    <td class="text-right">{{ $item->tax_rate }}%</td>
                    <td class="text-right">{{ number_format($item->netAmount, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="border-lightgray">
                <td colspan="5" class="text-right">{{ __('Totals (EUR)') }}:</td>
                <td class="text-right font-bold">{{ number_format($totals['total_amount'], 2) }}</td>
                <td class="text-right font-bold"></td>
                <td class="text-right font-bold">{{ number_format($totals['total_discount'], 2) }}</td>
                <td class="text-right font-bold"></td>
                <td class="text-right font-bold">{{ number_format($totals['total_net_amount'], 2) }}</td>
            </tr>
            <tr>
                <td colspan="9" class="text-right">{{ __('VAT (EUR)') }}:</td>
                <td class="text-right font-bold">{{ number_format($totals['total_vat'], 2) }}</td>
            <tr>
                <td colspan="9" class="text-right">{{ __('Total amount VAT incl. (EUR)') }}:</td>
                <td class="text-right font-bold">{{ number_format($totals['document_total'], 2) }}</td>
            </tr>
            <tr class="border-bottom">
                <td colspan="9" class="text-right">{{ __('Advance payment (EUR)') }}:</td>
                <td class="text-right font-bold">-{{ number_format($document->advance_paid, 2) }}</td>
            </tr>
            <tr>
                <td colspan="9" class="text-right font-bold">{{ __('Total amount due (EUR)') }}:</td>
                <td class="text-right font-bold">{{ number_format($totals['payable_amount'], 2) }}</td>
            </tr>
        </tfoot>
    </table>

    {{-- Invoice Extra Information --}}
    <div>
        <table style="width: 100%;">
            <tbody>
                <tr>
                    <td style="width: 1%; white-space: nowrap;">{{ __('Due date') }}:</td>
                    <td>{{ $document->due_date }}</td>
                </tr>
                <tr>
                    <td style="width: 1%; white-space: nowrap;">
                        {{ __('Total amount due in words') }}:</td>
                    <td class="text-justify">{{ $totalInWords }}</td>
                </tr>
                @if ($document->delivery_date)
                    <tr>
                        <td style="width: 1%; white-space: nowrap;">{{ __('Delivery date') }}:
                        </td>
                        <td>{{ $document->delivery_date }}</td>
                    </tr>
                @endif

                @if ($document->transaction_description)
                    <tr>
                        <td style="width: 1%; white-space: nowrap;">
                            {{ __('Transaction description') }}:</td>
                        <td class="text-justify">{{ $document->transaction_description }}</td>
                    </tr>
                @endif

                @if ($document->tax_note)
                    <tr>
                        <td style="width: 1%; white-space: nowrap;">{{ __('VAT Ref.') }}:</td>
                        <td class="text-justify">{{ $document->tax_note }}</td>
                    </tr>
                @endif

                @if ($document->document_note)
                    <tr>
                        <td class="text-top" style="width: 1%; white-space: nowrap;">
                            {{ __('Notes') }}:</td>
                        <td class="text-justify">{{ $document->document_note }}</td>
                    </tr>
                @endif

                @if ($document->show_created_by && $document->agent?->fullName)
                    <tr>
                        <td style="width: 1%; white-space: nowrap; padding-top: 10px;">
                            {{ __('Invoice prepared by') }}:</td>
                        <td style="padding-top: 10px;">{{ $document->agent?->fullName }}</td>
                    </tr>
                @endif

                <tr>
                    <td colspan="2" style="padding-top: 20px;">
                        {{ $document->show_signature
                            ? __('Signature') . ' ______________________________'
                            : __('Invoice was prepared electronically and is valid without signature') }}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</body>

</html>
