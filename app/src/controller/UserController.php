<?php
namespace agilman\a2\controller;

use agilman\a2\Exception\BankException;
use agilman\a2\model\{UserModel, UserCollectionModel};
use agilman\a2\view\View;


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
