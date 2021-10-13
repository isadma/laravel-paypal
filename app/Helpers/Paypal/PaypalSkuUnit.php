<?php

namespace App\Helpers\Paypal;

use Exception;

class PaypalSkuUnit
{
    public ?string $name = null;
    public ?string $description = null;
    public ?string $sku = null;
    private ?array $unit_amount = [];
    private ?array $tax = [];
    public int $quantity = 0;
    public ?string $category = null;

    public function setUnitAmount(string $currency, float $value){
        $this->unit_amount = [
            "currency_code" => $currency,
            "value" => number_format($value, 2),
        ];
    }

    public function setTax(string $currency, float $value){
        $this->tax = [
            "currency_code" => $currency,
            "value" => number_format($value, 2),
        ];
    }

    /**
     * @throws Exception
     */
    public function get(): array
    {
        if ( ! $this->name){
            throw new Exception("Name is required.");
        }
        if ( empty($this->unit_amount) ){
            throw new Exception("Unit amount is required.");
        }
        if ( empty($this->tax) ){
            throw new Exception("Tax is required.");
        }
        if ( $this->quantity < 1 ){
            throw new Exception("Quantity is required.");
        }
        if ( ! $this->sku ){
            throw new Exception("Sku is required.");
        }
        return [
            'name' => $this->name,
            'description' => $this->description,
            'sku' => $this->sku,
            'unit_amount' => $this->unit_amount,
            'tax' => $this->tax,
            'quantity' => $this->quantity,
            'category' => $this->category
        ];
    }
}
