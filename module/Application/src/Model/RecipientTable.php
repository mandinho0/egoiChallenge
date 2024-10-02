<?php

namespace Application\Model;

use Laminas\Db\TableGateway\TableGatewayInterface;
use Laminas\Db\ResultSet\ResultSetInterface;

class RecipientTable
{
    protected $tableGateway;

    public function __construct(TableGatewayInterface $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    /**
     * Fetch all recipients
     *
     * @return ResultSetInterface
     */
    public function fetchAll()
    {
        return $this->tableGateway->select();
    }

    /**
     * Get a specific recipient by ID
     *
     * @param int $id
     * @return Recipient|null
     * @throws \RuntimeException
     */
    public function getRecipient($id)
    {
        $rowset = $this->tableGateway->select(['id' => (int) $id]);
        $arrayRowset = iterator_to_array($rowset);
        $recipient = current($arrayRowset);

        if (!$recipient) {
            throw new \RuntimeException(sprintf(
                'Could not find recipient with ID %d',
                $id
            ));
        }

        return $recipient;
    }

    /**
     * Save or update a recipient
     *
     * @param Recipient $recipient
     */
    public function saveRecipient(Recipient $recipient)
    {
        $data = $recipient->getArrayCopy();

        if ($recipient->id) {
            $this->tableGateway->update($data, ['id' => (int) $recipient->id]);
        } else {
            $this->tableGateway->insert($data);
        }
    }

    /**
     * Delete a recipient by ID
     *
     * @param int $id
     */
    public function deleteRecipient($id)
    {
        $this->tableGateway->delete(['id' => (int) $id]);
    }
}
