<?php
namespace ktc\a2\model;

use ktc\a2\Exception\BankException;

/**
 * Class AccountCollectionModel
 *
 * @package ktc/a2
 * @author  Andrew Gilman <a.gilman@massey.ac.nz>
 */
class AccountCollectionModel extends Model
{
    private $accountIds;

    private $N;

    public function __construct($userName, $userId)
    {
        parent::__construct();
        if (isset($userName)) {
            if ($userName == "admin") {
                if (!$result = $this->db->query("SELECT `account_id` FROM `account`;")) {
                    throw new BankException(99,"Account db table is empty");
                }
            } else {
                if (!$result = $this->db->query("SELECT `account_id` FROM `account` WHERE `user_id` IS '$userId';")) {
                    throw new BankException(10);
                }
            }
        } else {
            throw new BankException(8);
        }
        $this->accountIds = array_column($result->fetch_all(), 0);
        $this->N = $result->num_rows;
    }

    /**
     * Get account collection
     *
     * @return \Generator|AccountModel[] Accounts
     */
    public function getAccounts()
    {
        foreach ($this->accountIds as $id) {
            // Use a generator to save on memory/resources
            // load accounts from DB one at a time only when required
            yield (new AccountModel())->load($id);
        }
    }
}
