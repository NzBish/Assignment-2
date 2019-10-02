<?php
namespace ktc\a2\model;

use ktc\a2\Exception\BankException;

/**
 * Class UserCollectionModel
 *
 * @package ktc/a2
 * @author
 */
class UserCollectionModel extends Model
{
    private $userIds;

    private $N;

    public function __construct()
    {
        parent::__construct();
        if (!$result = $this->db->query("SELECT `user_id` FROM `user`;")) {
            throw new BankException("User db table is empty");
        }
        $this->userIds = array_column($result->fetch_all(), 0);
        $this->N = $result->num_rows;
    }


    public function getUsers()
    {
        foreach ($this->userIds as $id) {
            // Use a generator to save on memory/resources
            // load accounts from DB one at a time only when required
            yield (new UserModel())->load($id);
        }
    }
}
