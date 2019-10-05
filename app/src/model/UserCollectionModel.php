<?php
namespace ktc\a2\model;

use ktc\a2\Exception\BankException;

/**
 * Class UserCollectionModel
 *
 * @package ktc/a2
 * @author  Andrew Gilman <a.gilman@massey.ac.nz>
 * @author  K. Dempsey
 * @author  T. Crompton
 * @author  C. Bishop
 */
class UserCollectionModel extends Model
{
    private $userNames;

    private $N;

    public function __construct()
    {
        parent::__construct();
        if (!$result = $this->db->query("SELECT `user_name` FROM `user`;")) {
            throw new BankException(99,'DB query failed: '.mysqli_error($this->db));
        }
        if ($result->num_rows < 1) {
            throw new BankException(99,'User db table is empty');
        }
        $this->userNames = array_column($result->fetch_all(), 0);
        $this->N = $result->num_rows;
    }


    public function getUsers()
    {
        foreach ($this->userNames as $name) {
            // Use a generator to save on memory/resources
            // load accounts from DB one at a time only when required
            yield (new UserModel())->load($name);
        }
    }
}
