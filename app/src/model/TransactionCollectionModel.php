<?php
namespace ktc\a2\model;



use ktc\a2\Exception\BankException;

/**
 * Class TransactionCollectionModel
 *
 * @package ktc/a2
 * @author
 */
class TransactionCollectionModel extends Model
{
    private $transIds;

    private $N;

    public function __construct()
    {
        parent::__construct();
        if (!$result = $this->db->query("SELECT `trans_id` FROM `transaction`;")) {
            throw new BankException("Transaction db table is empty");
        }
        $this->transIds = array_column($result->fetch_all(), 0);
        $this->N = $result->num_rows;
    }


    public function getTransactions()
    {
        foreach ($this->transIds as $id) {
            // Use a generator to save on memory/resources
            // load accounts from DB one at a time only when required
            yield (new TransactionModel())->load($id);
        }
    }
}