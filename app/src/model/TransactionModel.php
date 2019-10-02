<?php
namespace ktc\a2\model;

use ktc\a2\Exception\BankException;

/**
 * Class TransactionModel
 *
 * @package ktc/a2
 * @author
 */
class TransactionModel extends Model
{
    private $id;

    private $type;

    private $amount;

    private $datetime;

    private $accountID;

    public function getId()
    {
        return $this->id;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setType(string $type)
    {
        $this->type = $type;

        return $this;
    }

    public function getAmount()
    {
        return $this->amount;
    }

    public function setAmount(float $amount)
    {
        $this->amount = $amount;
        return $this;
    }

    public function getDateTime()
    {
        return $this->datetime;
    }

    public function setDateTime(string $date)
    {
        $this->datetime = $date;
        return $this;
    }

    public function getAccountId()
    {
        return $this->accountID;
    }

    public function setAccountId(int $account)
    {
        $this->accountID = $account;

        return $this;
    }

    public function load($id)
    {
        if (!$result = $this->db->query(
            "SELECT * FROM `transaction` WHERE `trans_id` = $id;")) {
            throw new BankException('No transaction found with id '.$id);
        }
        if($result->num_rows > 0) {
            $result = $result->fetch_assoc();
            $this->id = $id;
            $this->type = $result['trans_type'];
            $this->amount = $result['trans_amount'];
            $this->datetime = $result['trans_datetime'];
            $this->accountID = $result['account_id'];
        }

        return $this;
    }

    public function save()
    {

        $type = $this->type ?? "NULL";
        $amount = $this->amount ?? "NULL";
        $dateTime = $this->datetime ?? "NULL";
        $accountId = $this->accountID ?? "NULL";
        if (!isset($this->id)) {
            // New transaction - Perform INSERT
            if (!$result = $this->db->query("INSERT INTO `transaction` VALUES
                                        (NULL,'$type','$amount','$dateTime','$accountId');"))
            {
                throw new BankException("Insert transaction failed");
            }
            $this->id = $this->db->insert_id;
        }

        return $this;
    }
}
