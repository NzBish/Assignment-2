<?php
namespace ktc\a2\controller;

use ktc\a2\Exception\BankException;
use ktc\a2\model\{UserModel, UserCollectionModel};
use ktc\a2\view\View;

/**
 * Class UserController
 *
 * @package ktc/a2
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
