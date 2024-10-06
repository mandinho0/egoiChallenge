<?php

namespace Application\Controller;

use Application\Model\Recipient;
use Application\Model\RecipientTable;
use Laminas\Mvc\Controller\AbstractRestfulController;
use Laminas\View\Model\JsonModel;

use function PHPUnit\Framework\isJson;

class RecipientController extends AbstractRestfulController
{
    /** @var RecipientTable  */
    private $recipientTable;

    /**
     * Constructor Method
     *
     * @param RecipientTable $recipientTable
     */
    public function __construct(RecipientTable $recipientTable)
    {
        $this->recipientTable = $recipientTable;
    }

    /**
     * GET /recipients
     *
     * @return JsonModel
     */
    public function getList()
    {
        $recipients = $this->recipientTable->fetchAll();
        $recipientsArray = [];

        foreach ($recipients as $recipient) {
            $recipientsArray[] = $recipient->getArrayCopy();
        }

        return new JsonModel(['data' => $recipientsArray]);
    }

    /**
     * GET /recipients/:id
     *
     * @param mixed $id
     * @return JsonModel
     */
    public function get(mixed $id)
    {
        $recipient = $this->recipientTable->getRecipient($id);
        return new JsonModel(['data' => $recipient->getArrayCopy()]);
    }

    /**
     * POST /recipients
     *
     * @param mixed $data
     * @return JsonModel
     */
    public function create(mixed $data)
    {
        $routeMatch = $this->getEvent()->getRouteMatch();
        $actionName = $routeMatch->getParam('id');

        if ($actionName === 'bulk') {
            return $this->bulk($data);
        }

        $inputFilter = Recipient::getInputFilter();
        $inputFilter->setData($data);

        if (! $inputFilter->isValid()) {
            return new JsonModel([
                'success' => false,
                'messages' => $inputFilter->getMessages(),
            ]);
        }

        $recipient = new Recipient();
        $recipient->exchangeArray($data);
        $this->recipientTable->saveRecipient($recipient);

        return new JsonModel(['success' => true, 'data' => $recipient->getArrayCopy()]);
    }

    /**
     * Bulk insert /recipients/bulk
     *
     * @param mixed $data
     * @return JsonModel
     */
    public function bulk(mixed $data)
    {
        $recipients = [];
        foreach ($data as $item) {
            $inputFilter = Recipient::getInputFilter();
            $inputFilter->setData($item);

            if (! $inputFilter->isValid()) {
                return new JsonModel([
                    'success' => false,
                    'messages' => $inputFilter->getMessages(),
                ]);
            }

            $recipient = new Recipient();
            $recipient->exchangeArray($item);

            $this->recipientTable->saveRecipient($recipient);
            $recipients[] = $recipient->getArrayCopy();
        }

        return new JsonModel(['success' => true, 'data' => $recipients]);
    }

    /**
     * PUT /recipients/:id
     *
     * @param mixed $id
     * @param mixed $data
     * @return JsonModel
     */
    public function update(mixed $id, mixed $data)
    {
        try {
            $inputFilter = Recipient::getInputFilter();
            $inputFilter->setData($data);

            if (! $inputFilter->isValid()) {
                return new JsonModel([
                    'success' => false,
                    'messages' => $inputFilter->getMessages(),
                ]);
            }

            $recipient = $this->recipientTable->getRecipient((int)$id);
            $recipient->exchangeArray($data, true);

            $this->recipientTable->saveRecipient($recipient, true);

            return new JsonModel(['success' => true, 'data' => $recipient->getArrayCopy()]);
        } catch (\Exception $e) {
            return new JsonModel([
                'success' => false,
                'messages' => $e->getMessage(),
            ]);
        }
    }

    /**
     * DELETE /recipients/:id
     *
     * @param mixed $id
     * @return JsonModel
     */
    public function delete(mixed $id)
    {
        $this->recipientTable->deleteRecipient($id);
        return new JsonModel(['data' => 'Deleted']);
    }
}
