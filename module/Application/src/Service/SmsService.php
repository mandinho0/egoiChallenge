<?php

namespace Application\Service;

use Application\Model\RecipientRepository;

class SmsService
{
    /**
     * Construct Method
     *
     * @param RecipientRepository $recipientModel
     */
    public function __construct(
        protected RecipientRepository $recipientModel
    ) {
    }

    /**
     * Fork process to send SMS
     *
     * @return void
     */
    public function sendSmsToRecipients($recipients)
    {
        foreach ($recipients as $recipient) {
            $pid = pcntl_fork();

            if ($pid == -1) {
                error_log("Failed to fork process for recipient: {$recipient['name']}");
            } elseif ($pid) {
                continue;
            } else {
                // Child process: Handle SMS sending
                $this->sendSms($recipient->getArrayCopy());
                exit(0);
            }
        }

        error_log("Parent process finished, SMS are being sent asynchronously.");
        pcntl_signal(SIGCHLD, SIG_IGN);
    }

    /**
     * Simulate sending SMS method
     *
     * @param array $recipient
     * @return void
     */
    private function sendSms(array $recipient)
    {
        error_log("Sending SMS to: {$recipient['name']} - {$recipient['phone_number']}");
        sleep(5);
        error_log("SMS sent to: {$recipient['name']}");
    }
}
