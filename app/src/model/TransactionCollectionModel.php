<?php
namespace ktc\a2\model;

use ktc\a2\Exception\BankException;

/**
 * Class TransactionCollectionModel
 *
 * @package ktc/a2
 * @author  Andrew Gilman <a.gilman@massey.ac.nz>
 * @author  K. Dempsey
 * @author  T. Crompton
 * @author  C. Bishop
 */
class TransactionCollectionModel extends Model
{
    /**
     * @var array Contains transaction IDs for lookup in TransactionCollectionModel::getTransactions
     */
    private $transIds;

    /**
     * @var int The number of indices in $transIds
     */
    private $N;

    /**
     * TransactionCollectionModel constructor
     *
     * Creates a TransactionCollectionModel, which is used to create a generator for TransactionModels
     *
     * @param $userName Used to determine if there is a logged in user and if that user is "admin"
     * @param $userId Used in the query for non-admin users to collect only their transactions
     * @throws BankException on database connection errors or lack of transactions for the specified user
     */
    public function __construct($userName, $userId)
    {
        parent::__construct();
        if (isset($userName)) {
            if ($userName == "admin") {
                if (!$result = $this->db->query("SELECT `trans_id` FROM `transaction`;")) {
                    throw new BankException(99, 'DB query failed: ' . mysqli_error($this->db));
                }
                if ($result->num_rows < 1) {
                    throw new BankException(99, "Transaction table is empty");
                }
            } else {
                if (!$result = $this->db->query("SELECT t.`trans_id` FROM `transaction` As t 
                                                                    INNER JOIN `account` AS a
                                                                    WHERE t.`account_id` = a.`account_id`
                                                                    AND a.`user_id`=$userId
                                                                    ORDER BY t.`trans_datetime` DESC;")) {
                    throw new BankException(99, 'DB query failed: ' . mysqli_error($this->db));
                }
                if ($result->num_rows < 1) {
                    throw new BankException(99, "No transactions found for this user");
                }
            }
        } else {
            throw new BankException(8);
        }

        $this->transIds = array_column($result->fetch_all(), 0);
        $this->N = $result->num_rows;
    }

    /**
     * Get transactions
     *
     * A generator function yielding one TransactionModel per ID in $transIds
     *
     * @uses \ktc\a2\model\TransactionCollectionModel::$transIds to create TransactionModels
     * @return \Generator|TransactionModel[] Transactions
     * @throws BankException via TransactionModel->load
     */
    public function getTransactions()
    {
        foreach ($this->transIds as $id) {
            // Use a generator to save on memory/resources
            // load accounts from DB one at a time only when required
            yield (new TransactionModel())->load($id);
        }
    }
}
