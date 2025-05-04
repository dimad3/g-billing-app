<?php

namespace App\Services;

use App\Models\Document\Document;
use App\Models\Document\DocumentItem;
use App\Repositories\Interfaces\DocumentRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class DocumentService
{
    public function __construct(
        protected DocumentRepositoryInterface $documentRepository,
    ) {}

    /** Create a new document with related data. */
    public function createDocument(array $data): Document
    {
        // dd($data);
        return DB::transaction(function () use ($data) {
            ($totals = $this->calculateTotals($data['items'], $data['advance_paid'] ?? 0)); // Calculate totals
            ($data = array_merge($data, $totals));

            $document = $this->documentRepository->create($data); // create document

            // Create document_items
            ($items = $data['items']);
            $document->documentItems()->createMany($items);

            auth()->user()->documentSetting?->increment('next_number');  // increment by 1

            return $document;
        });
    }

    /** Update an existing document with related data. */
    public function updateDocument(Document $document, array $data): Document
    {
        return DB::transaction(function () use ($document, $data) {
            ($totals = $this->calculateTotals($data['items'], $data['advance_paid'] ?? 0)); // Calculate totals
            ($data = array_merge($data, $totals));

            $document = $this->documentRepository->update($document, $data); // update document

            // Update document_items
            $items = $data['items'];
            $document->documentItems()->delete();
            $document->documentItems()->createMany($items);
            // Uncomment if you want to save item totals in the database
            // foreach ($totals['items'] as $item) {
            //     $document->documentItems()->create($item);
            // }

            return $document;
        });
    }

    /** Delete a document. */
    public function deleteDocument(Document $document): bool
    {
        $deleted = $this->documentRepository->delete($document);

        return $deleted;
    }

    /**
     * Calculate totals for a document items.
     */
    public function calculateTotals(Collection|array $items, float $advancePaid = 0): array
    {
        // dd($items);

        // Initialize totals
        $sumOfQuantity = 0;
        $sumOfAmount = 0;
        $sumOfDiscount = 0;
        $sumOfNetAmount = 0;
        $totalVat = 0;
        $payableAmount = 0;

        $groupedByTaxRate = [];

        foreach ($items as &$item) {
            if ($item instanceof DocumentItem) {
                // if(is_array($item)) {
                $item = $item->toArray(); // Convert to array if it's a collection
            }
            // dd($item, ! isset($item['amount']));

            if (! isset($item['amount']) || ! isset($item['discount']) || ! isset($item['net_amount'])) {
                $item = $this->calculateItemValues($item);
            }
            // dd($item);

            // Update totals
            $sumOfQuantity += $item['quantity'];
            $sumOfAmount += $item['amount'];
            $sumOfDiscount += $item['discount'];
            $sumOfNetAmount += $item['net_amount'];

            // Process tax rate as a string key to prevent float key conversion issues
            $taxRate = isset($item['tax_rate']) ? round((float) $item['tax_rate'], 2) : 0;
            $taxRateKey = (string) $taxRate; // Convert tax rate to string for safe array indexing

            // Ensure that the key exists before adding to it
            if (!isset($groupedByTaxRate[$taxRateKey])) {
                $groupedByTaxRate[$taxRateKey] = 0;
            }

            $groupedByTaxRate[$taxRateKey] += $item['net_amount'];
        }

        // Calculate VAT for each tax rate group
        $totalVat = 0.0;
        // dd($groupedByTaxRate); // Debugging output

        foreach ($groupedByTaxRate as $taxRateKey => $netAmountSubTotal) {
            $taxRate = (float) $taxRateKey; // Convert back to float for calculation
            $taxSubTotal = round(($netAmountSubTotal * $taxRate) / 100, 2);
            $totalVat += $taxSubTotal;
        }

        // Calculate total and payable amount
        $total = round($sumOfNetAmount + $totalVat, 2);
        $payableAmount = round($total - $advancePaid, 2);

        return [
            // 'items' => $items, // Uncomment if you want to save item totals in the database
            'total_quantity' => $sumOfQuantity ? round($sumOfQuantity, 3) : 0,
            'total_amount' => $sumOfAmount ? round($sumOfAmount, 2) : 0,
            'total_discount' => $sumOfDiscount ? round($sumOfDiscount, 2) : 0,
            'total_net_amount' => $sumOfNetAmount ? round($sumOfNetAmount, 2) :  0,
            'total_vat' => $totalVat ? round($totalVat, 2) :  0,
            'document_total' => $total ? round($total, 2) :  0,
            'payable_amount' => $payableAmount ? round($payableAmount, 2) : 0,
        ];
    }

    /**
     * Calculate item values for a document item.
     * This method calculates the amount, discount, and net amount for a document item.
     */
    protected function calculateItemValues(array $item): array
    {
        $quantity = isset($item['quantity']) ? (float) $item['quantity'] : 0;
        $price = isset($item['price']) ? (float) $item['price'] : 0;
        $discountRate = isset($item['discount_rate']) ? (float) $item['discount_rate'] : 0;

        $calculatedValues = [];
        $amount = round($quantity * $price, 2); // Calculate the total amount before discount
        $discount = round(($amount * $discountRate) / 100, 2); // Calculate the discount amount based on the discount rate
        $netAmount = round($amount - $discount, 2); // Calculate the net amount after applying the discount
        $calculatedValues['amount'] = $amount ?? 0;
        $calculatedValues['discount'] = $discount ?? 0;
        $calculatedValues['net_amount'] = $netAmount ?? 0;

        // Return an array with the calculated values and item details
        return array_merge($item, $calculatedValues);
    }
}
