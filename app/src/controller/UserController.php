<?php
namespace ktc\a2\controller;

use ktc\a2\Exception\BankException;
use ktc\a2\model\UserModel;
use ktc\a2\model\UserCollectionModel;
use ktc\a2\view\View;

/**
 * Class UserController
 *
 * @package ktc/a2
 * @author
 */

class UserController extends Controller
{

    /**
     * User Index action
     * @throws BankException
     */
    public function indexAction()
    {
        if (isset($_SESSION['userName'])) {
            if ($_SESSION['userName'] == "admin") {
                $collection = new UserCollectionModel();
                $users = $collection->getUsers();
                $view = new View('userIndex');
                echo $view->addData('users', $users)->render();
            } else {
                $this->redirect('accountIndex'); // No other user should be able to get here but redirect anyway
            }
        } else {
            $this->redirect('userLogin');
        }
    }

    /**
     * User Login action
     */
    public function loginAction()
    {
        if (isset($_POST['login'])) {
            $user = new UserModel();
            $user->load($_POST['userName']);
            if (password_verify($_POST['password'], $user->getPassword())) {
                $_SESSION['userName'] = $user->getUserName();
                if (!isset($_SESSION['userName'])) throw new BankException("Couldn't set session details");
                $_SESSION['userId'] = $user->getId();
                if (!isset($_SESSION['userId'])) throw new BankException("Couldn't set session details");
                $this->redirect('accountIndex');
            } else {
                throw new BankException("Invalid username or password"); // Maybe not an exception?
            }
        } else {
            $view = new View('userLogin');
            echo $view->render();
        }
    }

    /**
     * User Logout action
     */
    public function logoutAction()
    {
        if (isset($_SESSION['userName'])) {
            session_unset();
            session_destroy();
        }
        $view = new View('userLogout');
        echo $view->render();
    }

    /**
     * User Create action
     */
    public function createAction()
    {
        if (isset($_POST['create'])) {
            $user = new UserModel();
            if ($user->check($_POST['userName'])) {
                throw new BankException("User with this name already exists");
            }
            $user->setUserName($_POST['userName']);
            $user->setFirstName($_POST['firstName']);
            $user->setLastName($_POST['lastName']);
            if (!$passHash = password_hash($_POST['password'], PASSWORD_BCRYPT)) {
                throw new BankException("Failed to hash entered password");
            }
            $user->setPassword($passHash);
            $user->setEmail($_POST['email']);
            $user->setPhone($_POST['phone']);
            $user->setDateOfBirth($_POST['dob']);
            if(!$user)
            {
                throw new BankException("Failed to create user");
            }
            $this->redirect('Home');
        } else {
            $view = new View('userCreate');
            echo $view->render();
        }
    }
}
