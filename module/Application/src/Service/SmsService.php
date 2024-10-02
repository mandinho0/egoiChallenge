<?php

namespace Application\Service;

class SmsService
{
    public function sendSmsToRecipients(array $recipients)
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
                $this->sendSms($recipient);
                exit(0);
            }
        }

        while (pcntl_waitpid(0, $status) != -1);
    }

    private function sendSms(array $recipient)
    {
        // Simulate sending SMS
        error_log("Sending SMS to: {$recipient['name']} - {$recipient['phone_number']}");
        sleep(2);
        error_log("SMS sent to: {$recipient['name']}");
    }
}