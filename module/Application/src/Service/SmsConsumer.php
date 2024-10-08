<?php

namespace Application\Service;

use Stomp\Client;
use Stomp\Network\Connection;

class SmsConsumer
{
    /** @var Client  */
    private $stomp;

    /**
     * Contruct Method ( connect to activeMQ )
     *
     * @throws \Stomp\Exception\StompException
     */
    public function __construct()
    {
        try {
            $connection = new Connection('tcp://172.18.0.3:61613', 2);
            $this->stomp = new Client($connection);
            $this->stomp->setLogin('artemis', 'artemis');
            $this->stomp->connect();
        } catch (\Exception $e) {
            error_log("error on Consumer Construct: " . $e->getMessage());
        }
    }

    /**
     * Consume Messages Method
     *
     * @return mixed
     */
    public function consumeMessages()
    {
        $this->stomp->subscribe('/queue/sms-queue');

        while (true) {
            if ($this->stomp->getConnection()->readFrame()) {
                $frame = $this->stomp->readFrame();

                if ($frame != null) {
                    $recipient = json_decode($frame->body, true);
                    $this->sendSms($recipient);
                    $this->stomp->ack($frame);
                }
            }
            sleep(1);
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
