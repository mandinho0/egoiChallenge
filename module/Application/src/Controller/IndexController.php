<?php

declare(strict_types=1);

namespace Application\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use Laminas\Db\Adapter\Adapter;
use Laminas\Db\Sql\Sql;

class IndexController extends AbstractActionController
{

    /**
     * Contructor Method
     *
     * @param Adapter $dbAdapter
     */
    public function __construct(
        protected Adapter $dbAdapter
    ) {
    }

    /**
     * Index method
     *
     * @return ViewModel
     */
    public function indexAction()
    {
        $this->checkAndCreateTable();
        return new ViewModel();
    }

    /**
     * Check and Create table if not Exists
     *
     * @return void
     */
    protected function checkAndCreateTable()
    {
        $sql = "CREATE TABLE IF NOT EXISTS recipients (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            phone_number VARCHAR(20) NOT NULL,
            email VARCHAR(255) NOT NULL
        )";

        $statement = $this->dbAdapter->query($sql);
        $statement->execute();
    }
}
