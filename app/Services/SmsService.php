<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

/**
 * A service for utilizing the Beem SMS API
 */

class SmsService
{

    /**
     * Define api endpoint
     */
    private $send_url = 'https://apisms.beem.africa/v1/send';

    /**
     * Initialize and assign headers, api access key and secret
     */
    private  $key;

    private $secret;

    private $headers;

    /**
     * Declare class __constructor
     */
    public function __construct()
    {
        $this->key = config('services.beem.key');
        $this->secret = config('services.beem.secret');
        $this->headers = [
            'cache-control' => 'no-cache',
            'content-type' => 'application/json'
        ];
    }

    /**
     * Sends SMS to destination numbers
     * @param  array  $recipients
     * @param string $message
     * @return \Illuminate\Http\Response
     */
    public function sendSMS($recipients, $message)
    {
        // Setup the transaction
        $data = array(
            'source_addr' => 'AJISO',
            'encoding' => "0",
            'schedule_time' => '',
            'message' =>  $message,
            'recipients' => [
                array('recipient_id' => $recipients['recipient_id'], 'dest_addr' => $recipients['dest_addr']),
            ]
        );

        // Instantiate the transaction
        $response = Http::acceptJson()->withHeaders($this->headers)->withBasicAuth($this->key, $this->secret)
            ->post($this->send_url, $data);
        //return $response;
        return $response->getStatusCode();
    }
}
