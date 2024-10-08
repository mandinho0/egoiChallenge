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
     * @throws \Exception
     */
    public function __construct()
    {
        $connection = new Connection('tcp://0.0.0.0:61616', 10);
        $this->stomp = new Client($connection);

        try {
            $this->stomp->connect('admin', 'admin');
            echo "Connected successfully!";
        } catch (\Stomp\Exception\StompException $e) {
            throw new \Exception("Connection failed: " . $e->getMessage());
        } catch (\Exception $e) {
            throw new \Exception("An unexpected error occurred while connecting: " . $e->getMessage());
        }
    }


    public function connect()
    {
        if (! $this->stomp->isConnected()) {
            $this->stomp->connect('admin', 'admin');
        }
    }

    public function disconnect()
    {
        if ($this->stomp->isConnected()) {
            $this->stomp->disconnect();
        }
    }

    public function sendToQueue(mixed $recipients)
    {
        $this->connect();
        if (!$this->stomp->isConnected()) {
            throw new \Exception("Not connected to ActiveMQ");
        }

        foreach ($recipients as $recipient) {
            $message = "teste simples"; //json_encode($recipient->getArrayCopy());

            if ($message === false) {
                throw new \Exception("Error encoding message to JSON: " . json_last_error_msg());
            }

            $this->stomp->send('/queue/sms-queue', utf8_encode($message), ['content-type' => 'text/plain']);

        }
        $this->disconnect();
    }
}
