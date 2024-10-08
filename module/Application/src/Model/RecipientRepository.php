<?php

namespace Application\Model;

use Exception;
use Laminas\Db\TableGateway\TableGatewayInterface;
use Laminas\Db\ResultSet\ResultSetInterface;
use RuntimeException;

class RecipientRepository
{
    /** @var TableGatewayInterface  */
    protected $tableGateway;

    /**
     * Construct Method
     *
     * @param TableGatewayInterface $tableGateway
     */
    public function __construct(TableGatewayInterface $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    /**
     * Fetch all recipients
     *
     * @return ResultSetInterface
     */
    public function findAll()
    {
        return $this->tableGateway->select();
    }

    /**
     * Get a specific recipient by ID
     *
     * @param int $id
     * @return Recipient|null
     * @throws RuntimeException|Exception
     */
    public function getRecipient(int $id): ?Recipient
    {
        $rowset = $this->tableGateway->select(['id' => $id]);
        $row = $rowset->current();

        if (! $row) {
            throw new Exception("Could not find recipient with id $id");
        }

        $recipient = new Recipient();
        $recipient->exchangeArray($row->getArrayCopy());

        return $recipient;
    }


    /**
     * Save or update a recipient
     *
     * @param Recipient $recipient
     */
    public function saveRecipient(Recipient $recipient, $update = false)
    {
        $data = $recipient->getArrayCopy();

        if ($update) {
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
