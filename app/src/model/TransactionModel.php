<?php
namespace ktc\a2\model;

use ktc\a2\Exception\BankException;

/**
 * Class TransactionModel
 *
 * @package ktc/a2
 * @author  Andrew Gilman <a.gilman@massey.ac.nz>
 * @author  K. Dempsey
 * @author  T. Crompton
 * @author  C. Bishop
 */
class TransactionModel extends Model
{
    /**
     * @var int Transaction ID (primary key)
     */
    private $id;

    /**
     * @var string Transaction type
     */
    private $type;

    /**
     * @var float Transaction amount
     */
    private $amount;

    /**
     * @var string Date / Time of transaction
     */
    private $datetime;

    /**
     * @var int Account ID (foreign key)
     */
    private $accountID;

    /**
     * Get Transaction ID
     *
     * @return int Transaction ID
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get Transaction type
     *
     * @return string Transaction type
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set Transaction type
     *
     * @param string $type The new type for the transaction
     * @return $this A TransactionModel
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Get Transaction amount
     *
     * @return float Transaction amount
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Set Transaction amount
     *
     * @param float $amount The new amount for the transaction
     * @return $this A TransactionModel
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
        return $this;
    }

    /**
     * Get date of Transaction
     *
     * @return string Date / Time of transaction
     */
    public function getDateTime()
    {
        return $this->datetime;
    }

    /**
     * Set date of Transaction
     *
     * @param string $date The new date / time of the transaction
     * @return $this A TransactionModel
     */
    public function setDateTime($date)
    {
        $this->datetime = $date;
        return $this;
    }

    /**
     * Get Account ID
     *
     * @return int Account ID
     */
    public function getAccountId()
    {
        return $this->accountID;
    }

    /**
     * Set Account ID
     *
     * @param string $account The new account ID for the transaction
     * @return $this A TransactionModel
     */
    public function setAccountId($account)
    {
        $this->accountID = $account;

        return $this;
    }

    /**
     * Transaction load
     *
     * Loads transaction information from the database into this TransactionModel
     *
     * @param int $id Transaction ID
     * @return $this A TransactionModel
     * @throws BankException on database connection errors
     */
    public function load($id)
    {
        if (!$result = $this->db->query("SELECT * FROM `transaction` WHERE `trans_id` = $id;")) {
            throw new BankException(99, 'DB query failed: '.mysqli_error($this->db));
        }
        if ($result->num_rows < 1) {
            throw new BankException(99, 'No transaction found with ID '.$id);
        }
        if ($result->num_rows == 1) {
            $result = $result->fetch_assoc();
            $this->id = $id;
            $this->type = $result['trans_type'];
            $this->amount = $result['trans_amount'];
            $this->datetime = $result['trans_datetime'];
            $this->accountID = $result['account_id'];
        } else {
            throw new BankException(99, 'Transaction ID ' . $id . ' is not unique');
        }

        return $this;
    }

    /**
     * Transaction save
     *
     * Saves transaction information from this TransactionModel into the database
     *
     * @return $this A TransactionModel
     * @throws BankException on database connection errors
     */
    public function save()
    {
        $type = $this->type ?? "NULL";
        $amount = $this->amount ?? "NULL";
        $dateTime = $this->datetime ?? "NULL";
        $accountId = $this->accountID ?? "NULL";
        if (!isset($this->id)) {
            /** New transaction - Perform INSERT */
            if (!$result = $this->db->query("INSERT INTO `transaction` VALUES
                                        (NULL,'$type','$amount','$dateTime','$accountId');")) {
                throw new BankException(99, "Insert transaction failed");
            }
            $this->id = $this->db->insert_id;
        } else {
            throw new BankException(99, "Transactions should not be updated");
        }

        return $this;
    }
}
