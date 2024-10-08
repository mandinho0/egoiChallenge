<?php

namespace Application\Service;

use Stomp\Client;
use Stomp\Exception\StompException;
use Stomp\Network\Connection;

class SmsProducer
{
    /** @var Client  */
    private $stomp;

    /**
     * Construct Method ( connect to activeMQ )
     *
     * @throws StompException
     * @throws \Exception
     */
    public function __construct()
    {
        try {
            $connection = new Connection('tcp://172.18.0.3:61613', 2);
            $this->stomp = new Client($connection);
            $this->stomp->setLogin('artemis', 'artemis');
            $this->stomp->connect();
        } catch (StompException $e) {
            error_log("error on Producer Construct: " . $e->getMessage());
        } catch (\Exception $e) {
            error_log("error on Producer Construct: " . $e->getMessage());
        }
    }

    /**
     * Send to Queue Method
     *
     * @param mixed $recipients
     * @return void
     * @throws \Exception
     */
    public function sendToQueue(mixed $recipients)
    {
        if (! $this->stomp->isConnected()) {
            throw new \Exception("Not connected to ActiveMQ");
        }

        foreach ($recipients as $recipient) {
            $message = json_encode($recipient->getArrayCopy());

            if ($message === false) {
                throw new \Exception("Error encoding message to JSON: " . json_last_error_msg());
            }

            $this->stomp->send('/queue/sms-queue', $message);
            error_log("Message sent to ActiveMQ: " . $message);
        }
    }
}
