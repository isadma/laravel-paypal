<?php

namespace App\Helpers\Paypal;

use Exception;

class PaypalAddress
{
    public ?string $address_line_1 = null;
    public ?string $address_line_2 = null;
    public ?string $city = null;
    public ?string $state = null;
    public ?string $postal_code = null;
    public ?string $country_code = null;

    /**
     * @throws Exception
     */
    public function get(): array
    {
        if (strlen($this->country_code) != 2){
            throw new Exception("Country code should be 2 character.");
        }
        if ( ! $this->address_line_1 ){
            throw new Exception("Address line 1 is required.");
        }
        if ( ! $this->city ){
            throw new Exception("City is required.");
        }
        if ( ! $this->state ){
            throw new Exception("State is required.");
        }
        return [
            'address_line_1' => $this->address_line_1,
            'address_line_2' => $this->address_line_2,
            'admin_area_2' => $this->city,
            'admin_area_1' => $this->state,
            'postal_code' => $this->postal_code,
            'country_code' => $this->country_code
        ];
    }
}
