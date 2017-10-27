<?php

namespace CDP\BookingBundle\Stripe;


class Stripe
{
    private $api_key;

    public function __construct($api_key)
    {
        $this->api_key = $api_key;
    }

    /**
     * @return mixed
     */
    public function api($endpoint, $data)
    {
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => "https://api.stripe.com/v1/$endpoint",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_USERPWD => $this->api_key,
            CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
            CURLOPT_POSTFIELDS => http_build_query($data)
        ]);
        $response = json_decode(curl_exec($curl));
        curl_close($curl);
        return $response;
    }

}