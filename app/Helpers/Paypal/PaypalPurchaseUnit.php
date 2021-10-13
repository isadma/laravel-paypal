<?php

namespace App\Helpers\Paypal;

use Exception;

class PaypalPurchaseUnit
{
    public ?string $reference_id = null;
    private array $amount = [];
    private array $items = [];
    private array $shipping = [];

    /**
     * @throws Exception
     */
    public function get(): array
    {
        if ( ! $this->reference_id ){
            $this->reference_id = uniqid();
        }
        return [
            "reference_id" => $this->reference_id,
            "amount" => $this->amount,
            "items" => $this->items,
            "shipping" => $this->shipping,
        ];
    }

    public function setAmount(string $currency, float $total, float $item_total = 0, float $shipping = 0, float $tax_total = 0){
        $amount = [
            "currency_code" => $currency,
            "value" => number_format($total, 2),
        ];
        if ($item_total > 0 || $shipping > 0 || $tax_total > 0){
            $amount["breakdown"] = [];
            if ($item_total > 0){
                $amount["breakdown"]["item_total"] = [
                    "currency_code" => $currency,
                    "value" => number_format($item_total, 2),
                ];
            }
            if ($shipping > 0){
                $amount["breakdown"]["shipping"] = [
                    "currency_code" => $currency,
                    "value" => number_format($shipping, 2),
                ];
            }
            if ($tax_total > 0){
                $amount["breakdown"]["tax_total"] = [
                    "currency_code" => $currency,
                    "value" => number_format($tax_total, 2),
                ];
            }
        }
        $this->amount = $amount;
    }

    /**
     * @throws Exception
     */
    public function setShippingAddress(PaypalAddress $paypalAddress){
        $this->shipping = [
            "address" => $paypalAddress->get()
        ];
    }

    /**
     * @throws Exception
     */
    public function addSkuUnits(PaypalSkuUnit $paypalSkuUnit){
        array_push($this->items, $paypalSkuUnit->get());
    }
}
