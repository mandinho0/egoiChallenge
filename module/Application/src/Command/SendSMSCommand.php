<?php

namespace Application\Command;

use Application\Model\RecipientTable;
use Laminas\Cli\Command\AbstractParamAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Application\Service\SmsService;
use Application\Service\SmsProducer;

class SendSMSCommand extends AbstractParamAwareCommand
{
    /**
     * Command Constructor Method
     *
     * @param SmsService $smsService
     * @param SmsProducer $smsProducer
     * @param RecipientTable $recipientModel
     */
    public function __construct(
        protected SmsService $smsService,
        protected SmsProducer $smsProducer,
        protected RecipientTable $recipientModel
    ) {
        parent::__construct();
    }

    /**
     * Configure Method
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('app:sendSMS')
            ->setDescription('Send asynchronous SMS')
            ->addOption('amq-mode', null, InputOption::VALUE_NONE, "Enable AMQ mode");
    }

    /**
     * Execute Method
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $recipients = $this->recipientModel->fetchAll();
            $withAMQ = $input->getOption('amq-mode');

            if ($withAMQ) {
                $this->smsProducer->sendToQueue($recipients);
            } else {
                $this->smsService->sendSmsToRecipients($recipients);
            }
        } catch (\Exception $e) {
            $output->writeln("Error: " . $e->getMessage());

            return -1;
        }

        return 0;
    }
}
