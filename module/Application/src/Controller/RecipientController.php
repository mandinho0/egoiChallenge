<?php

namespace Application\Controller;

use Application\Model\Recipient;
use Application\Model\RecipientRepository;
use Laminas\Mvc\Controller\AbstractRestfulController;
use Laminas\View\Model\JsonModel;

class RecipientController extends AbstractRestfulController
{

    /**
     * Constructor Method
     *
     * @param RecipientRepository $recipientRepository
     */
    public function __construct(
        private RecipientRepository $recipientRepository,
    ) {
    }

    /**
     * GET /recipients
     *
     * @return JsonModel
     */
    public function getList()
    {
        $recipients = $this->recipientRepository->findAll();
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
     * @throws \Exception
     */
    public function get(mixed $id)
    {
        $recipient = $this->recipientRepository->getRecipient($id);
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
        $this->recipientRepository->saveRecipient($recipient);

        return new JsonModel(['success' => true, 'data' => $recipient->getArrayCopy()]);
    }

    /**
     * Bulk insert /bulk
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

            $this->recipientRepository->saveRecipient($recipient);
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

            $recipient = $this->recipientRepository->getRecipient((int)$id);
            $recipient->exchangeArray($data, true);

            $this->recipientRepository->saveRecipient($recipient, true);

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
        $this->recipientRepository->deleteRecipient($id);
        return new JsonModel(['data' => 'Deleted']);
    }
}
