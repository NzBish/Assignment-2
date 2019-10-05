<?php
namespace ktc\a2\model;

use ktc\a2\Exception\BankException;

/**
 * Class AccountModel
 *
 * @package ktc/a2
 * @author  Andrew Gilman <a.gilman@massey.ac.nz>
 * @author  K. Dempsey
 * @author  T. Crompton
 * @author  C. Bishop
 */
class AccountModel extends Model
{
    /**
     * @var integer Account ID
     */
    private $id;
    /**
     * @var string Account Name
     */
    private $type;

    private $balance;

    private $user;

    private $dateStarted;


    /**
     * @return int Account ID
     */
    public function getId()
    {
        return $this->id;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setType($type)
    {
        $this->type = mysqli_real_escape_string($this->db, $type);

        return $this;
    }

    public function getBalance()
    {
        return $this->balance;
    }

    public function setBalance($balance)
    {
        $this->balance = $balance;
        return $this;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function setUser($user)
    {
        $this->user = $user;
        return $this;
    }

    public function getDateStarted()
    {
        return $this->dateStarted;
    }

    public function setDateStarted()
    {
        $this->dateStarted = $this->db->query("SELECT NOW();");
        return $this;
    }

    /**
     * Loads account information from the database
     *
     * @param int $id Account ID
     *
     * @return $this AccountModel
     */
    public function load($id)
    {
        if (!$result = $this->db->query(
            "SELECT * FROM `account` WHERE `account_id` = $id;"
        )) {
            throw new BankException(99, 'No account found with id ' . $id);
        }
        if ($result->num_rows > 0) {
            $result = $result->fetch_assoc();
            $this->id = $id;
            $this->type = $result['account_type'];
            $this->balance = $result['account_bal'];
            $this->user = $result['user_id'];
            $this->dateStarted = $result['account_dateStarted'];
        }

        return $this;
    }

    public function save()
    {
        $id = $this->id;
        $type = $this->type ?? "NULL";
        $balance = $this->balance ?? "NULL";
        $user = $this->user ?? "NULL";
        //$dateStarted = $this->dateStarted ?? "NULL";
        if (!isset($this->id)) {
            // New account - Perform INSERT
            if (!$result = $this->db->query("INSERT INTO `account` VALUES
                                        (NULL,'$type','$balance','$user',NOW());")) {
                throw new BankException(99, 'Insert account failed: ' . mysqli_error($this->db));
            }
            $this->id = $this->db->insert_id;
        } else {
            // saving existing account - perform UPDATE
            if (!$result = $this->db->query("UPDATE `account` SET
                                        `account_type` = '$type',
                                        `account_bal` = '$balance',
                                        `user_id` = '$user'
                                         WHERE `account_id` = $id;")) {
                throw new BankException(99, 'Update account failed: ' . mysqli_error($this->db));
            }
        }

        return $this;
    }

    public function delete()
    {
        if (!$result = $this->db->query("DELETE FROM `account` WHERE `account_id` = $this->id;")) {
            throw new BankException(99, 'Delete account failed: ' . mysqli_error($this->db));
        }

        return $this;
    }

    private function updateBalance($amount, $transType)
    {
        $id = $this->id;
        $balance = $this->balance;
        $done = true;
        if (!$result = $this->db->query(
            "UPDATE `account`
            SET `account_bal` = $balance
            WHERE `account_id` = $id;"
        )) {
            $done = true;
        }
        if ($done) {
            if (!$result = $this->db->query(
                "INSERT INTO `transaction`(`trans_type`, `trans_amount`, `trans_datetime`, `account_id`)
                    VALUES ('$transType', '$amount', NOW(), '$id');"
            )) {
                throw new BankException(99, 'Error Updating Transaction ' . mysqli_error($this->db));
            }
        }
    }

    public function deposit($amount)
    {
        $this->balance += $amount;
        $transType = 'Deposit';
        try {
            $this->updateBalance($amount, $transType);
        } catch (BankException $e) {
            throw $e;
        }
    }

    public function withdraw($amount)
    {
        $this->balance -= $amount;
        $transType = 'Withdraw';
        try {
            $this->updateBalance($amount, $transType);
        } catch (BankException $e) {
            throw $e;
        }
    }
}
