<?php
namespace ctk\a2\model;

use ctk\a2\Exception\BankException;

/**
 * Class AccountModel
 *
 * @package ctk/a2
 * @author  Andrew Gilman <a.gilman@massey.ac.nz>
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

    public function setType(string $type)
    {
        $this->type = $type;

        return $this;
    }

    public function getBalance()
    {
        return $this->balance;
    }

    public function setBalance(float $balance)
    {
        $this->balance = $balance;
        return $this;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function setUser(int $user)
    {
        $this->user = $user;

        return $this;
    }

    public function getDateStarted()
    {
        return $this->dateStarted;
    }

    public function setDateStarted(string $date)
    {
        $this->dateStarted = $date;
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
            "SELECT * FROM `account` WHERE `account_id` = $id;")) {
            throw new BankException('No account found with id '.$id);
        }
        if($result->num_rows > 0) {
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
        $dateStarted = $this->dateStarted ?? "NULL";
        if (!isset($this->id)) {
            // New account - Perform INSERT
            if (!$result = $this->db->query("INSERT INTO `account` VALUES
                                        (NULL,'$type','$balance','$user','$dateStarted');"))
            {
                throw new BankException("Insert account failed");
            }
            $this->id = $this->db->insert_id;
        } else {
            // saving existing account - perform UPDATE
            if (!$result = $this->db->query("UPDATE `account` SET
                                        `account_type` = '$type',
                                        `account_bal` = '$balance',
                                        `user_id` = '$user',
                                        `account_dateStarted` = '$dateStarted'
                                         WHERE `id` = $id;")) {
                throw new BankException("Update account failed");
            }
        }

        return $this;
    }

    public function delete()
    {
        if (!$result = $this->db->query("DELETE FROM `account` WHERE `account_id` = $this->id;")) {
            throw new BankException("Delete account failed");
        }

        return $this;
    }

    private function updateBalance() {
        $id =  $this->id;
        $balance = $this->balance;
        if (!$result = $this->db->query(
            "UPDATE `account`
            SET `account_bal` = $balance
            WHERE id = $id;"
        )) {
            throw new BankException('Error Updating Balance');
        }
    }

    public function deposit($amount) {
        $this->balance += $amount;
        try {
            $this->updateBalance();
        }
        catch (BankException $e) {
            throw $e;
        }
    }

    public function withdraw($amount) {
        $this->balance -= $amount;
        try {
            $this->updateBalance();
        }
        catch (BankException $e) {
            throw $e;
        }
    }


}
