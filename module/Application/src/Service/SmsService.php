<?php

namespace Application\Service;

use Application\Model\RecipientTable;

class SmsService
{
    /**
     * Construct Method
     *
     * @param RecipientTable $recipientModel
     */
    public function __construct(
        protected RecipientTable $recipientModel
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
                // Parent process
                continue;
            } else {
                // Child process
                $this->sendSms($recipient->getArrayCopy());
                exit(0);
            }
        }

        while (pcntl_waitpid(0, $status) != -1) {
            $status = pcntl_wexitstatus($status);
            error_log("Child process exited with status $status");
        }
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
        sleep(2);
        error_log("SMS sent to: {$recipient['name']}");
    }
}
