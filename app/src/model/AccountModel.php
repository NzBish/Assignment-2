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
     * @var int Account ID (primary key)
     */
    private $id;

    /**
     * @var string Account type
     */
    private $type;

    /**
     * @var float Account balance
     */
    private $balance;

    /**
     * @var int User ID (foreign key)
     */
    private $user;

    /**
     * @var string Date / Time account was opened
     */
    private $dateStarted;

    /**
     * Get Account ID
     *
     * @return int Account ID
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get Account type
     *
     * @return string Account type
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set Account type
     *
     * @param string $type The new type of the account
     * @return $this An AccountModel
     */
    public function setType($type)
    {
        $this->type = mysqli_real_escape_string($this->db, $type);

        return $this;
    }

    /**
     * Get Account balance
     *
     * @return float Account balance
     */
    public function getBalance()
    {
        return $this->balance;
    }

    /**
     * Set Account balance
     *
     * @param float $balance The new balance of the account
     * @return $this An AccountModel
     */
    public function setBalance($balance)
    {
        $this->balance = $balance;
        return $this;
    }

    /**
     * Get User ID
     *
     * @return int User ID
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set User ID
     *
     * @param int $user The new user ID associated with the account
     * @return $this An AccountModel
     */
    public function setUser($user)
    {
        $this->user = $user;
        return $this;
    }

    /**
     * Get date Account started
     *
     * @return string Date / Time account was opened
     */
    public function getDateStarted()
    {
        return $this->dateStarted;
    }

    /**
     * Set date Account started
     *
     * @return $this An AccountModel
     */
    public function setDateStarted()
    {
        $this->dateStarted = $this->db->query("SELECT NOW();");
        return $this;
    }

    /**
     * Account load
     *
     * Loads account information from the database into this AccountModel
     *
     * @param int $id Account ID
     * @return $this An AccountModel
     * @throws BankException on database connection errors or failure to find the specified account ID
     */
    public function load($id)
    {
        if (!$result = $this->db->query("SELECT * FROM `account` WHERE `account_id` = $id;")) {
            throw new BankException(99, 'DB query failed: '.mysqli_error($this->db));
        }
        if ($result->num_rows < 1) {
            throw new BankException(99, 'No account found with ID '.$id);
        }
        if ($result->num_rows == 1) {
            $result = $result->fetch_assoc();
            $this->id = $id;
            $this->type = $result['account_type'];
            $this->balance = $result['account_bal'];
            $this->user = $result['user_id'];
            $this->dateStarted = $result['account_dateStarted'];
        } else {
            throw new BankException(99, 'Account ID ' . $id . ' is not unique');
        }

        return $this;
    }

    /**
     * Account save
     *
     * Saves account information from this AccountModel into the database
     *
     * @return $this An AccountModel
     * @throws BankException on database connection errors
     */
    public function save()
    {
        $id = $this->id;
        $type = $this->type ?? "NULL";
        $balance = $this->balance ?? "NULL";
        $user = $this->user ?? "NULL";
        //$dateStarted = $this->dateStarted ?? "NULL";
        if (!isset($this->id)) {
            /** New account - perform INSERT */
            if (!$result = $this->db->query("INSERT INTO `account` VALUES
                                        (NULL,'$type','$balance','$user',NOW());")) {
                throw new BankException(99, 'Insert account failed: ' . mysqli_error($this->db));
            }
            $this->id = $this->db->insert_id;
        } else {
            /** Saving existing account - perform UPDATE */
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

    /**
     * Account delete
     *
     * Deletes the account specified by the information in the AccountModel from the database
     *
     * @return $this An AccountModel
     * @throws BankException on database connection errors
     */
    public function delete()
    {
        if (!$result = $this->db->query("DELETE FROM `account` WHERE `account_id` = $this->id;")) {
            throw new BankException(99, 'Delete account failed: ' . mysqli_error($this->db));
        }

        return $this;
    }

    /**
     * Account balance update
     *
     * Updates the balance of the account specified in the AccountModel
     *
     * @param float $amount The amount being deposited or withdrawn
     * @param string $transType The type of transaction being performed
     * @throws BankException on database connection errors
     */
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

    /**
     * Account deposit
     *
     * Deposits an amount into the account specified by the AccountModel by calling updateBalance
     *
     * @param float $amount The amount being deposited into the account
     * @throws BankException via AccountModel::updateBalance
     */
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

    /**
     * Account withdraw
     *
     * Withdraws an amount from the account specified by the AccountModel by calling updateBalance
     *
     * @param float $amount The amount being withdrawn from the account
     * @throws BankException via AccountModel::updateBalance
     */
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
