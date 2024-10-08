<?php

namespace Application\Service;

use Stomp\Client;
use Stomp\Network\Connection;
use Stomp\StatefulStomp;
use Stomp\Transport\Frame;

class SmsConsumer
{
    /** @var StatefulStomp */
    private $stomp;

    /**
     * Construct Method (connect to ActiveMQ)
     *
     * @throws \Stomp\Exception\StompException
     */
    public function __construct()
    {
        try {
            $connection = new Connection('tcp://172.18.0.3:61613', 2);
            $client = new Client($connection);
            $client->setLogin('artemis', 'artemis');
            $this->stomp = new StatefulStomp($client);
        } catch (\Exception $e) {
            error_log("Error on Consumer Construct: " . $e->getMessage());
        }
    }

    /**
     * Consume Messages Method
     *
     * @return void
     */
    public function consumeMessages()
    {
        try {
            $this->stomp->subscribe('/queue/sms-queue', 'client-individual');

            while (true) {
                $frame = $this->stomp->read();

                if ($frame instanceof Frame) {
                    $recipient = json_decode($frame->body, true);
                    $this->sendSms($recipient);
                    $this->stomp->ack($frame); // Acknowledge the message after processing
                }

                sleep(1); // Optional sleep to avoid high CPU usage
            }
        } catch (\Exception $e) {
            error_log("Error on consumeMessages: " . $e->getMessage());
        }
    }

    /**
     * Simulate sending SMS
     *
     * @param array $recipient
     * @return void
     */
    private function sendSms(array $recipient)
    {
        error_log("Sending SMS to: {$recipient['name']} - {$recipient['phone_number']}");
    }
}
