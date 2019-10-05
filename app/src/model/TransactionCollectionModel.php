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
    private $transIds;

    private $N;

    public function __construct($userName, $userId)
    {
        parent::__construct();
        if (isset($userName)) {
            if ($userName == "admin") {
                if (!$result = $this->db->query("SELECT `trans_id` FROM `transaction`;")) {
                    throw new BankException(99,'DB query failed: '.mysqli_error($this->db));
                }
                if ($result->num_rows < 1) {
                    throw new BankException(99,"Transaction table is empty");
                }
            } else {
                if (!$result = $this->db->query("SELECT t.`trans_id` FROM `transaction` As t 
                                                                    INNER JOIN `account` AS a
                                                                    WHERE t.`account_id` = a.`account_id`
                                                                    AND a.`user_id`=$userId
                                                                    ORDER BY t.`trans_datetime` DESC;")) {
                    throw new BankException(99,'DB query failed: '.mysqli_error($this->db));
                }
                if ($result->num_rows < 1) {
                    throw new BankException(99,"No transactions found for this user");
                }
            }
        } else {
            throw new BankException(8);
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