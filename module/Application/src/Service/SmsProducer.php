<?php

namespace Application\Service;

use Stomp\Client;
use Stomp\Network\Connection;

class SmsProducer
{
    /** @var Client  */
    private $stomp;

    /**
     * Construct Method ( connect to activeMQ )
     *
     * @throws \Stomp\Exception\StompException
     */
    public function __construct()
    {
        $connection = new Connection('tcp://activemq:61616', 10);
        $this->stomp = new Client($connection);
        $this->stomp->setHeartbeat(5000);
        $this->stomp->connect('admin', 'admin');
    }

    /**
     * Send to Queue Method
     *
     * @param mixed $recipients
     * @return void
     */
    public function sendToQueue(mixed $recipients)
    {
        foreach ($recipients as $recipient) {
            $message = json_encode($recipient->getArrayCopy());
            $this->stomp->send('/queue/sms-queue', $message);
        }
    }
}
