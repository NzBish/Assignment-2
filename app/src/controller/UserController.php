<?php
namespace ctk\a2\controller;

use ctk\a2\Exception\BankException;
use ctk\a2\model\{UserModel, UserCollectionModel};
use ctk\a2\view\View;

/**
 * Class UserController
 *
 * @package ctk/a2
 * @author
 */

class UserController extends Controller
{

    public function indexAction()
    {
        $collection = new UserCollectionModel();
        $users = $collection->getUsers();
        $view = new View('userIndex');
        echo $view->addData('users', $users)->render();
    }
}
