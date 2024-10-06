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
        $connection = new Connection('tcp://activemq:61616');
        $this->stomp = new Client($connection);
        $this->stomp->connect('admin', 'admin');
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
