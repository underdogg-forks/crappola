<?php

namespace Tests\Unit;

use App\Models\Account;
use App\Models\AccountGateway;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @SuppressWarnings("PHPMD.TooManyPublicMethods")
 */
class ChargesFeesTraitTest extends TestCase
{
    use RefreshDatabase;

    private Account $account;
    private Client $client;
    private Invoice $invoice;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Setup test data
        $this->account = Account::factory()->create([
            'gateway_fee_enabled' => true,
        ]);
        
        $this->client = Client::factory()->create([
            'account_id' => $this->account->id,
        ]);
        
        $this->invoice = Invoice::factory()->create([
            'account_id' => $this->account->id,
            'client_id' => $this->client->id,
            'balance' => 100.00,
            'partial' => 0,
        ]);
    }

    #[Test]
    public function itCalculatesGatewayFeeWithFlatAmount(): void
    {
        $this->markTestIncomplete();
        
        // Arrange
        $gatewaySettings = (object) [
            'fee_amount' => 2.50,
            'fee_percent' => 0,
            'fee_tax_rate1' => 0,
            'fee_tax_rate2' => 0,
        ];
        
        // Mock getGatewaySettings
        $this->account->shouldReceive('getGatewaySettings')
            ->andReturn($gatewaySettings);
        
        // Act
        $fee = $this->invoice->calcGatewayFee(false, false);
        
        // Assert
        $this->assertEquals(2.50, $fee);
    }

    #[Test]
    public function itCalculatesGatewayFeeWithPercentage(): void
    {
        $this->markTestIncomplete();
        
        // Arrange
        $gatewaySettings = (object) [
            'fee_amount' => 0,
            'fee_percent' => 3.5,
            'fee_tax_rate1' => 0,
            'fee_tax_rate2' => 0,
        ];
        
        $this->account->shouldReceive('getGatewaySettings')
            ->andReturn($gatewaySettings);
        
        // Act
        $fee = $this->invoice->calcGatewayFee(false, false);
        
        // Assert
        $this->assertEquals(3.50, $fee);
    }

    #[Test]
    public function itCalculatesGatewayFeeWithAmountAndPercentage(): void
    {
        $this->markTestIncomplete();
        
        // Arrange
        $gatewaySettings = (object) [
            'fee_amount' => 0.30,
            'fee_percent' => 2.9,
            'fee_tax_rate1' => 0,
            'fee_tax_rate2' => 0,
        ];
        
        $this->account->shouldReceive('getGatewaySettings')
            ->andReturn($gatewaySettings);
        
        // Act
        $fee = $this->invoice->calcGatewayFee(false, false);
        
        // Assert
        $this->assertEquals(3.20, $fee); // 0.30 + (100 * 0.029)
    }

    #[Test]
    public function itCalculatesGatewayFeeWithTax(): void
    {
        $this->markTestIncomplete();
        
        // Arrange
        $gatewaySettings = (object) [
            'fee_amount' => 2.00,
            'fee_percent' => 0,
            'fee_tax_rate1' => 10,
            'fee_tax_rate2' => 0,
        ];
        
        $this->account->shouldReceive('getGatewaySettings')
            ->andReturn($gatewaySettings);
        
        // Act
        $fee = $this->invoice->calcGatewayFee(false, true);
        
        // Assert
        $this->assertEquals(2.20, $fee); // 2.00 + (2.00 * 0.10)
    }

    #[Test]
    public function itCalculatesGatewayFeeWithMultipleTaxes(): void
    {
        $this->markTestIncomplete();
        
        // Arrange
        $gatewaySettings = (object) [
            'fee_amount' => 2.00,
            'fee_percent' => 0,
            'fee_tax_rate1' => 10,
            'fee_tax_rate2' => 5,
        ];
        
        $this->account->shouldReceive('getGatewaySettings')
            ->andReturn($gatewaySettings);
        
        // Act
        $fee = $this->invoice->calcGatewayFee(false, true);
        
        // Assert
        $this->assertEquals(2.30, $fee); // 2.00 + (2.00 * 0.10) + (2.00 * 0.05)
    }

    #[Test]
    public function itReturnsFalseWhenGatewayFeeDisabled(): void
    {
        $this->markTestIncomplete();
        
        // Arrange
        $this->account->gateway_fee_enabled = false;
        $this->account->save();
        
        // Act
        $fee = $this->invoice->calcGatewayFee();
        
        // Assert
        $this->assertFalse($fee);
    }

    #[Test]
    public function itCalculatesGatewayFeeOnPartialAmount(): void
    {
        $this->markTestIncomplete();
        
        // Arrange
        $this->invoice->partial = 50.00;
        $this->invoice->save();
        
        $gatewaySettings = (object) [
            'fee_amount' => 0,
            'fee_percent' => 10,
            'fee_tax_rate1' => 0,
            'fee_tax_rate2' => 0,
        ];
        
        $this->account->shouldReceive('getGatewaySettings')
            ->andReturn($gatewaySettings);
        
        // Act
        $fee = $this->invoice->calcGatewayFee(false, false);
        
        // Assert
        $this->assertEquals(5.00, $fee); // 50.00 * 0.10
    }

    #[Test]
    public function itRoundsGatewayFeeToTwoDecimals(): void
    {
        $this->markTestIncomplete();
        
        // Arrange
        $this->invoice->balance = 33.33;
        $this->invoice->save();
        
        $gatewaySettings = (object) [
            'fee_amount' => 0,
            'fee_percent' => 3,
            'fee_tax_rate1' => 0,
            'fee_tax_rate2' => 0,
        ];
        
        $this->account->shouldReceive('getGatewaySettings')
            ->andReturn($gatewaySettings);
        
        // Act
        $fee = $this->invoice->calcGatewayFee(false, false);
        
        // Assert
        $this->assertEquals(1.00, $fee);
        $this->assertIsFloat($fee);
    }

    #[Test]
    public function itGetsGatewayFeeFromInvoiceItem(): void
    {
        $this->markTestIncomplete();
        
        // Arrange
        InvoiceItem::factory()->create([
            'invoice_id' => $this->invoice->id,
            'invoice_item_type_id' => INVOICE_ITEM_TYPE_PENDING_GATEWAY_FEE,
            'cost' => 2.50,
            'qty' => 1,
        ]);
        
        // Act
        $fee = $this->invoice->getGatewayFee();
        
        // Assert
        $this->assertEquals(2.50, $fee);
    }

    #[Test]
    public function itReturnsZeroWhenNoGatewayFeeItem(): void
    {
        $this->markTestIncomplete();
        
        // Arrange
        // No gateway fee items
        
        // Act
        $fee = $this->invoice->getGatewayFee();
        
        // Assert
        $this->assertEquals(0, $fee);
    }

    #[Test]
    public function itGetsGatewayFeeItemWhenExists(): void
    {
        $this->markTestIncomplete();
        
        // Arrange
        $feeItem = InvoiceItem::factory()->create([
            'invoice_id' => $this->invoice->id,
            'invoice_item_type_id' => INVOICE_ITEM_TYPE_PENDING_GATEWAY_FEE,
            'cost' => 3.00,
            'qty' => 1,
        ]);
        
        // Act
        $item = $this->invoice->getGatewayFeeItem();
        
        // Assert
        $this->assertNotFalse($item);
        $this->assertEquals($feeItem->id, $item->id);
    }

    #[Test]
    public function itReturnsFalseWhenNoGatewayFeeItemExists(): void
    {
        $this->markTestIncomplete();
        
        // Arrange
        // No gateway fee items
        
        // Act
        $item = $this->invoice->getGatewayFeeItem();
        
        // Assert
        $this->assertFalse($item);
    }

    #[Test]
    public function itIgnoresNonPendingGatewayFeeItems(): void
    {
        $this->markTestIncomplete();
        
        // Arrange
        InvoiceItem::factory()->create([
            'invoice_id' => $this->invoice->id,
            'invoice_item_type_id' => INVOICE_ITEM_TYPE_PAID_GATEWAY_FEE,
            'cost' => 5.00,
            'qty' => 1,
        ]);
        
        InvoiceItem::factory()->create([
            'invoice_id' => $this->invoice->id,
            'invoice_item_type_id' => INVOICE_ITEM_TYPE_STANDARD,
            'cost' => 10.00,
            'qty' => 1,
        ]);
        
        // Act
        $item = $this->invoice->getGatewayFeeItem();
        
        // Assert
        $this->assertFalse($item);
    }
}