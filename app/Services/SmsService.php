<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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
    private $sender;

    /**
     * Declare class __constructor
     */
    public function __construct()
    {
        $this->key = config('services.beem.key');
        $this->secret = config('services.beem.secret');
        $this->sender = config('services.beem.sender', 'AJISO');
        $this->headers = [
            'cache-control' => 'no-cache',
            'content-type' => 'application/json'
        ];
    }

    /**
     * Normalize a phone number into Beem's expected format (e.g. 2557XXXXXXXX).
     *
     * @param  string|null  $phone
     * @return string|null
     */
    public static function normalizeRecipient($phone)
    {
        $digits = preg_replace('/\D+/', '', (string) $phone);

        if ($digits === '') {
            return null;
        }

        if (strpos($digits, '0') === 0) {
            $digits = '255' . substr($digits, 1);
        } elseif (strpos($digits, '255') !== 0 && strlen($digits) === 9) {
            $digits = '255' . $digits;
        }

        return $digits;
    }

    /**
     * Sends SMS to destination numbers
     * @param  array  $recipients
     * @param string $message
     * @return \Illuminate\Http\Response
     */
    public function sendSMS($recipients, $message)
    {
        if (empty($recipients['dest_addr'])) {
            Log::warning('SMS send skipped: empty destination address.');
            return null;
        }

        // Setup the transaction
        $data = array(
            'source_addr' => $this->sender,
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

        if (!$response->successful()) {
            Log::warning('SMS send failed.', [
                'status' => $response->status(),
                'body' => $response->body(),
                'dest_addr' => $recipients['dest_addr'],
            ]);
        }

        return $response->getStatusCode();
    }
}
