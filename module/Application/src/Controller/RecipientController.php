<?php

namespace Application\Controller;

use Application\Model\Recipient;
use Application\Model\RecipientTable;
use Laminas\Mvc\Controller\AbstractRestfulController;
use Laminas\View\Model\JsonModel;

class RecipientController extends AbstractRestfulController
{
    private $recipientTable;

    public function __construct(RecipientTable $recipientTable)
    {
        $this->recipientTable = $recipientTable;
    }

    // GET /recipients
    public function getList()
    {
        $recipients = $this->recipientTable->fetchAll();
        $recipientsArray = [];

        foreach ($recipients as $recipient) {
            $recipientsArray[] = $recipient->getArrayCopy();
        }

        return new JsonModel(['data' => $recipientsArray]);
    }

    // GET /recipients/:id
    public function get($id)
    {
        $recipient = $this->recipientTable->getRecipient($id);
        return new JsonModel(['data' => $recipient->getArrayCopy()]);
    }

    // POST /recipients
    public function create($data)
    {
        $recipient = new Recipient();
        $recipient->exchangeArray($data);
        $inputFilter = $recipient->getInputFilter();
        $inputFilter->setData($data);

        if (!$inputFilter->isValid()) {
            return new JsonModel([
                'success' => false,
                'messages' => $inputFilter->getMessages(),
            ]);
        }

        $this->recipientTable->saveRecipient($recipient);

        return new JsonModel(['success' => true, 'data' => $recipient->getArrayCopy()]);
    }

    // PUT /recipients/:id
    public function update($id, $data)
    {
        try {
            $recipient = $this->recipientTable->getRecipient($id);
            $recipient->exchangeArray($data);
            $inputFilter = $recipient->getInputFilter();
            $inputFilter->setData($data);

            if (!$inputFilter->isValid()) {
                return new JsonModel([
                    'success' => false,
                    'messages' => $inputFilter->getMessages(),
                ]);
            }

            $this->recipientTable->saveRecipient($recipient);

            return new JsonModel(['success' => true, 'data' => $recipient->getArrayCopy()]);
        } catch (\Exception $e) {
            return new JsonModel([
                'success' => false,
                'messages' => $e->getMessage(),
            ]);
        }
    }

    // DELETE /recipients/:id
    public function delete($id)
    {
        $this->recipientTable->deleteRecipient($id);
        return new JsonModel(['data' => 'Deleted']);
    }
}
