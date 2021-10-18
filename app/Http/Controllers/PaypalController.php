<?php

namespace App\Http\Controllers;

use App\Helpers\Paypal\PaypalAddress;
use App\Helpers\Paypal\PaypalPayment;
use App\Helpers\Paypal\PaypalPurchaseUnit;
use App\Helpers\Paypal\PaypalSkuUnit;
use Exception;
use Illuminate\Http\Request;

class PaypalController extends Controller
{

    /**
     * @throws Exception
     */
    public function create(){
        $paypalPurchaseUnit = new PaypalPurchaseUnit();
        $paypalPurchaseUnit->setAmount("USD", 100, 90, 0, 10);

        $paypalSkuUnit = new PaypalSkuUnit();
        $paypalSkuUnit->name = "T-Shirt";
        $paypalSkuUnit->description = "Green XL";
        $paypalSkuUnit->sku = "sku01";
        $paypalSkuUnit->quantity = 1;
        $paypalSkuUnit->category = "PHYSICAL_GOODS";
        $paypalSkuUnit->setUnitAmount("USD", 90);
        $paypalSkuUnit->setTax("USD", 10);

        $paypalPurchaseUnit->addSkuUnits($paypalSkuUnit);

        $shippingAddress = new PaypalAddress();
        $shippingAddress->address_line_1 = "DIP";
        $shippingAddress->city = "Dubai";
        $shippingAddress->state = "Dubai";
        $shippingAddress->country_code = "AE";
        $paypalPurchaseUnit->setShippingAddress($shippingAddress);

        $payment = PaypalPayment::create($paypalPurchaseUnit);
        if ($payment["status"]){
            return response()->json($payment["response"]);
        }else{
            return response()->json([
                "status" => false,
                "message" => "Something went wrong",
                "error" => $payment["error"]
            ], $payment["statusCode"]);
        }
    }

    public function execute(Request $request){
        $request->validate([
            "orderID" => "required|string"
        ]);
        $payment = PaypalPayment::execute($request->get("orderID"));
        if ($payment["status"]){
            return response()->json([
                "status" => true,
                "message" => "Payment is successfully completed. Thank you for your purchase.",
                "data" => $payment
            ]);
        }else{
            return response()->json([
                "status" => false,
                "message" => "Payment couldn't completed. Please try again and contact to customer service.",
                "error" => $payment["error"],
                "response" => $payment["response"]
            ], $payment["statusCode"]);
        }
    }

    public function status($id){
        $payment = PaypalPayment::status($id);
        if ($payment["status"]){
            return response()->json([
                "status" => true,
                "primary_data" => $payment["primary_data"],
                "body" => $payment["body"],
                "headers" => $payment["headers"],
            ]);
        }else{
            return response()->json([
                "status" => false,
                "error" => $payment["error"],
                "response" => $payment["response"]
            ], $payment["statusCode"]);
        }
    }
}
