<?php

namespace Tests\Unit\Services;

use App\Models\Document\DocumentItem;
use App\Models\User\User;
use App\Services\DocumentService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Auth;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class DocumentServiceTest extends TestCase
{
    // use DatabaseTransactions; // Since this is a pure unit tests, we do not need DatabaseTransactions

    protected DocumentService $service;
    // private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(DocumentService::class);

        // $this->user = User::factory()->create();
        // Auth::login($this->user);
    }

    // calculateTotals() method tests ---------------------------------------------

    #[Test]
    public function it_calculates_totals_with_no_items()
    {
        $result = $this->invokeMethod($this->service, 'calculateTotals', [[], 10.5]);

        $this->assertEquals([
            'total_quantity' => 0,
            'total_amount' => 0,
            'total_discount' => 0,
            'total_net_amount' => 0,
            'total_vat' => 0,
            'document_total' => 0,
            'payable_amount' => -10.5, // 0 - 10.5 advance
        ], $result);
    }

    #[Test]
    public function it_calculates_totals_with_single_item_no_discount_no_tax()
    {
        $items = [
            ['quantity' => 0.625, 'price' => 0.625, 'discount_rate' => 0, 'tax_rate' => 0]
        ];

        $result = $this->invokeMethod($this->service, 'calculateTotals', [$items, 0.10]);

        $this->assertEquals(0.625, $result['total_quantity']);
        $this->assertEquals(0.39, $result['total_amount']); // (0.625 * 0.625)
        $this->assertEquals(0, $result['total_discount']);
        $this->assertEquals(0.39, $result['total_net_amount']); // (0.625 * 0.625)
        $this->assertEquals(0, $result['total_vat']);
        $this->assertEquals(0.39, $result['document_total']);
        $this->assertEquals(0.29, $result['payable_amount']); // 0.39 - 0.10 advance
    }

    #[Test]
    public function it_calculates_totals_with_discount()
    {
        $items = [
            ['quantity' => 0.625, 'price' => 0.625, 'discount_rate' => 5.625, 'tax_rate' => 0]
        ];

        $result = $this->invokeMethod($this->service, 'calculateTotals', [$items, 0.05]);

        $this->assertEquals(0.625, $result['total_quantity']);
        $this->assertEquals(0.39, $result['total_amount']); // (0.625 * 0.625)
        $this->assertEquals(0.02, $result['total_discount']); // (0.625 * 0.625) * 5.625% discount
        $this->assertEquals(0.37, $result['total_net_amount']); // (0.625 * 0.625) - 0.02 discount
        $this->assertEquals(0, $result['total_vat']);
        $this->assertEquals(0.37, $result['document_total']);
        $this->assertEquals(0.32, $result['payable_amount']); // 0.37 - 0.05 advance
    }

    #[Test]
    public function it_calculates_totals_with_tax()
    {
        $items = [
            ['quantity' => 0.625, 'price' => 0.625, 'discount_rate' => 0, 'tax_rate' => 12.625]
        ];

        $result = $this->invokeMethod($this->service, 'calculateTotals', [$items, 0.20]);

        $this->assertEquals(0.625, $result['total_quantity']);
        $this->assertEquals(0.39, $result['total_amount']); // (0.625 * 0.625)
        $this->assertEquals(0, $result['total_discount']); // (0.625 * 0.625) * 0% discount
        $this->assertEquals(0.39, $result['total_net_amount']); // (0.625 * 0.625) - 0.00 discount
        $this->assertEquals(0.05, $result['total_vat']); // (0.39 * 12.625% VAT
        $this->assertEquals(0.44, $result['document_total']); // (0.39 + 0.05 VAT)
        $this->assertEquals(0.24, $result['payable_amount']); // 0.44 - 0.20 advance
    }

    #[Test]
    public function it_calculates_totals_with_discount_and_tax()
    {
        $items = [
            ['quantity' => 0.625, 'price' => 0.625, 'discount_rate' => 5.625, 'tax_rate' => 12.625]
        ];

        $result = $this->invokeMethod($this->service, 'calculateTotals', [$items, 0.15]);

        $this->assertEquals(0.625, $result['total_quantity']);
        $this->assertEquals(0.39, $result['total_amount']); // (0.625 * 0.625)
        $this->assertEquals(0.02, $result['total_discount']); // (0.625 * 0.625) * 5.625% discount
        $this->assertEquals(0.37, $result['total_net_amount']); // (0.625 * 0.625) - 0.02 discount
        $this->assertEquals(0.05, $result['total_vat']); // (0.37 * 12.625% VAT
        $this->assertEquals(0.42, $result['document_total']); // (0.37 + 0.05 VAT)
        $this->assertEquals(0.27, $result['payable_amount']); // 0.42 - 0.15 advance
    }

    #[Test]
    public function it_calculates_totals_with_several_items_with_discounts_and_different_tax()
    {
        $items = [
            ['quantity' => 0.625, 'price' => 1.17625, 'discount_rate' => 6.65, 'tax_rate' => 12.65],
            ['quantity' => 1.625, 'price' => 1.17625, 'discount_rate' => 17.35, 'tax_rate' => 12.65],
            ['quantity' => 0.625, 'price' => 3.17625, 'discount_rate' => 6.65, 'tax_rate' => 21.85],
            ['quantity' => 1.625, 'price' => 3.17625, 'discount_rate' => 17.35, 'tax_rate' => 21.85],

            // quantity	price	amount	discount_rate	discount	net_amount	tax_rate
            // 0.625	1.17625 0.740	6.65	        0.050	    0.690	    12.65
            // 1.625	1.17625	1.910	17.35	        0.330	    1.580	    12.65
            // 0.625	3.17625	1.990	6.65	        0.130	    0.860	    21.85
            // 1.625	3.17625	5.160	17.35	        0.900	    4.260	    21.85
            // -----------------------------------------------------------------------
            // 4.5              9.800		            1.410	    8.390
            //                                                      1.630
            //                                                      10.020

            //                                                      2.270	    12.65
            //           	                                        6.120	    21.85
            //           	                                        ------------------
            //                                                      0.29	    12.65
            //                                                      1.34	    21.85
        ];

        $result = $this->invokeMethod($this->service, 'calculateTotals', [$items, 5.00]);

        $this->assertEquals(4.5, $result['total_quantity']);
        $this->assertEquals(9.80, $result['total_amount']);
        $this->assertEquals(1.41, $result['total_discount']);
        $this->assertEquals(8.39, $result['total_net_amount']);
        $this->assertEquals(1.63, $result['total_vat']);
        $this->assertEquals(10.02, $result['document_total']);
        $this->assertEquals(5.02, $result['payable_amount']); // 10.02 - 5.00 advance
    }

    #[Test]
    public function it_converts_document_item_to_array()
    {
        // Create a mock DocumentItem that returns specific values when toArray() is called
        $mockItem = $this->createMock(DocumentItem::class);
        $mockItem->method('toArray')->willReturn([
            'quantity' => 1,
            'price' => 10,
            'discount_rate' => 0,
            'tax_rate' => 0,
        ]);

        $result = $this->invokeMethod($this->service, 'calculateTotals', [[$mockItem], 3.50]);

        $this->assertEquals(1, $result['total_quantity']);
        $this->assertEquals(10, $result['total_amount']);
        $this->assertEquals(0, $result['total_discount']);
        $this->assertEquals(10, $result['total_net_amount']);
        $this->assertEquals(0, $result['total_vat']);
        $this->assertEquals(10, $result['document_total']);
        $this->assertEquals(6.50, $result['payable_amount']); // 10 - 3.50 advance
    }

    #[Test]
    public function it_calculates_item_values_when_missing_amount_discount_net_amount()
    {
        $items = [
            // Missing amount, discount
            ['quantity' => 2, 'price' => 5, 'discount_rate' => 10, 'tax_rate' => 20, 'net_amount' => 10.8]
        ];

        $result = $this->invokeMethod($this->service, 'calculateTotals', [$items, 2.00]);

        // amount = 2 * 5 = 10
        // discount = 10 * 10% = 1
        // net_amount = 9
        // VAT = 9 * 20% = 1.8
        // Total = 10.8
        $this->assertEquals(2, $result['total_quantity']);
        $this->assertEquals(10, $result['total_amount']);
        $this->assertEquals(1, $result['total_discount']);
        $this->assertEquals(9, $result['total_net_amount']);
        $this->assertEquals(1.8, $result['total_vat']);
        $this->assertEquals(10.8, $result['document_total']);
        $this->assertEquals(8.8, $result['payable_amount']); // 10.8 - 2.00 advance

        $items = [
            // Missing amount, net_amount and is set invalid discount 10.8
            ['quantity' => 2, 'price' => 5, 'discount_rate' => 10, 'tax_rate' => 20, 'discount' => 10.8]
        ];

        $result = $this->invokeMethod($this->service, 'calculateTotals', [$items, 1.50]);

        // amount = 2 * 5 = 10
        // discount = 10 * 10% = 1
        // net_amount = 9
        // VAT = 9 * 20% = 1.8
        // Total = 10.8
        $this->assertEquals(2, $result['total_quantity']);
        $this->assertEquals(10, $result['total_amount']);
        $this->assertEquals(1, $result['total_discount']);
        $this->assertEquals(9, $result['total_net_amount']);
        $this->assertEquals(1.8, $result['total_vat']);
        $this->assertEquals(10.8, $result['document_total']);
        $this->assertEquals(9.3, $result['payable_amount']); // 10.8 - 1.50 advance
    }

    // calculateItemValues() method tests ---------------------------------------------

    #[Test]
    public function it_calculates_calculateItemValueses_correctly()
    {
        $item = [
            'quantity' => 0.625,
            'price' => 1.17625,
            'discount_rate' => 6.65,
        ];

        $result = $this->invokeMethod($this->service, 'calculateItemValues', [$item]);

        $this->assertEquals(0.74, $result['amount']);
        $this->assertEquals(0.05, $result['discount']);
        $this->assertEquals(0.69, $result['net_amount']);
    }

    #[Test]
    public function zero_quantity_returns_zeroes()
    {
        $item = [
            'quantity' => 0,
            'price' => 20,
            'discount_rate' => 5,
        ];

        $result = $this->invokeMethod($this->service, 'calculateItemValues', [$item]);

        $this->assertEquals(0.00, $result['amount']);
        $this->assertEquals(0.00, $result['discount']);
        $this->assertEquals(0.00, $result['net_amount']);
    }

    #[Test]
    public function missing_fields_are_treated_as_zero()
    {
        $item = [];

        $result = $this->invokeMethod($this->service, 'calculateItemValues', [$item]);

        $this->assertEquals(0.00, $result['amount']);
        $this->assertEquals(0.00, $result['discount']);
        $this->assertEquals(0.00, $result['net_amount']);
    }

    #[Test]
    public function discount_rate_is_zero()
    {
        $item = [
            'quantity' => 1.625,
            'price' => 3.17625,
            'discount_rate' => 0,
        ];

        $result = $this->invokeMethod($this->service, 'calculateItemValues', [$item]);

        $this->assertEquals(5.16, $result['amount']);
        $this->assertEquals(0.00, $result['discount']);
        $this->assertEquals(5.16, $result['net_amount']);
    }

    #[Test]
    public function float_values_are_handled_properly()
    {
        $item = [
            'quantity' => 2.5555,
            'price' => 19.995555,
            'discount_rate' => 12.555,
        ];

        $result = $this->invokeMethod($this->service, 'calculateItemValues', [$item]);

        $this->assertEquals(51.10, $result['amount']);
        $this->assertEquals(6.42, $result['discount']);
        $this->assertEquals(44.68, $result['net_amount']);
    }

    protected function invokeMethod(&$object, $methodName, array $parameters = [])
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);
        return $method->invokeArgs($object, $parameters);
    }
}
