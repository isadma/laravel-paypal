<?php

namespace App\Helpers\Paypal;

use Exception;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use PayPalCheckoutSdk\Core\PayPalHttpClient;
use PayPalCheckoutSdk\Core\SandboxEnvironment;
use PayPalCheckoutSdk\Orders\OrdersCaptureRequest;
use PayPalCheckoutSdk\Orders\OrdersCreateRequest;
use PayPalCheckoutSdk\Orders\OrdersGetRequest;

class PaypalPayment
{
    public static function client(): PayPalHttpClient
    {
        $paypal_conf = Config::get('paypal');
        $environment = new SandboxEnvironment($paypal_conf["client_id"], $paypal_conf["secret"]);
        return new PayPalHttpClient($environment);
    }

    public static function getDataFromResponse($response): array
    {
        return [
            "status" => true,
            "statusCode" => $response->statusCode,
            "primary_data" => [
                "id" => $response->result->id,
                "code" => $response->statusCode,
                "status" => $response->result->status,
                "payer_name" => "{$response->result->payer->name->given_name} {$response->result->payer->name->surname}",
                "payer_email" => $response->result->payer->email_address,
                "payer_id" => $response->result->payer->payer_id,
            ],
            "body" => $response->result,
            "headers" => $response->headers,
        ];
    }

    /**
     * @throws Exception
     */
    public static function create(PaypalPurchaseUnit $purchase_unit): array
    {
        $request = new OrdersCreateRequest();
        $request->prefer('return=representation');
        $request->body = [
            'intent' => 'CAPTURE',
            'purchase_units' => [
                $purchase_unit->get()
            ]
        ];
        try {
            return [
                "status" => true,
                "response" => self::client()->execute($request)->result
            ];
        }catch (Exception $e) {
            Log::error($e);
            return [
                "status" => false,
                "statusCode" => $e->getCode(),
                "error" => $e
            ];
        }
    }

    public static function execute($orderID): array
    {
        try {
            $response = self::client()->execute(new OrdersCaptureRequest($orderID));
            if ($response->statusCode === 201 && $response->result->status === "COMPLETED"){
                return self::getDataFromResponse($response);
            }
            return [
                "status" => false,
                "statusCode" => $response->statusCode,
                "error" => "NOT COMPLETED",
                "response" => $response->result,
            ];
        }
        catch (Exception $e){
            Log::error($e);
            return [
                "status" => false,
                "statusCode" => $e->getCode(),
                "error" => $e->getMessage(),
                "response" => null,
            ];
        }
    }

    public static function status($id): array
    {
        try {
            $response = self::client()->execute(new OrdersGetRequest($id));
            if ($response->statusCode === 200 && $response->result->status === "COMPLETED") {
                return self::getDataFromResponse($response);
            }
            return [
                "status" => false,
                "statusCode" => $response->statusCode,
                "error" => "NOT COMPLETED",
                "response" => $response->result,
            ];
        }
        catch (Exception $e){
            Log::error($e);
            return [
                "status" => false,
                "statusCode" => $e->getCode(),
                "error" => $e->getMessage(),
                "response" => null,
            ];
        }
    }
}
