<?php
// src/Service/TwilioService.php

namespace App\Service;

use Twilio\Rest\Client; // Import the Client class

class TwilioService
{
    private $accountSid;
    private $authToken;
    private $fromNumber;

    public function __construct(string $accountSid, string $authToken, string $fromNumber)
    {
        $this->accountSid = $accountSid;
        $this->authToken = $authToken;
        $this->fromNumber = $fromNumber;
    }
    
    public function sendSms(string $number, string $name, string $text)
    {
        // Your SMS sending logic using Twilio
        $client = new Client($this->accountSid, $this->authToken); // Instantiate the Client class

        $toNumber = $number;
        $message = "$name vous a envoyé le message suivant: $text";

        $client->messages->create(
            $toNumber,
            [
                'from' => $this->fromNumber,
                'body' => $message,
            ]
        );
    }
}


