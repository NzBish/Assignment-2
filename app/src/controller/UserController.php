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
        session_start();
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
        session_start();
        try {
            if (isset($_POST['login'])) {
                $user = new UserModel();
                $user->load($_POST['userName']);
                if (password_verify($_POST['password'], $user->getPassword())) {
                    session_start();
                    $_SESSION['userName'] = $user->getUserName();
                    $_SESSION['userId'] = $user->getId();
                    $this->redirect('accountIndex');
                } else {
                    throw new BankException(4); // Maybe not an exception?
                }
            } else {
                $view = new View('userLogin');
                echo $view->render();
            }
        } catch (BankException $ex) {
            $view = new View('exception');
            echo $view->addData("exception", $ex)->addData("back", "userLogin")->render();
        }
    }

    /**
     * User Logout action
     */
    public function logoutAction()
    {
        session_start();
        if (isset($_SESSION['userName'])) {
            session_unset();
            session_destroy();
        }
        $this->redirect('Home');
    }

    /**
     * User Create action
     */
    public function createAction()
    {
        session_start();
        try {
            if (isset($_POST['create'])) {
                $user = new UserModel();
                if ($user->check($_POST['userName'])) {
                    throw new BankException(5);
                }
                $user->setUserName($_POST['userName']);
                $user->setFirstName($_POST['firstName']);
                $user->setLastName($_POST['lastName']);
                if (!$passHash = password_hash($_POST['password'], PASSWORD_BCRYPT)) {
                    throw new BankException(6);
                }
                $user->setPassword($passHash);
                $user->setEmail($_POST['email']);
                $user->setPhone($_POST['phone']);
                $user->setDateOfBirth($_POST['dob']);
                if (!$user) {
                    throw new BankException(7);
                }
                $this->redirect('Home');
            } else {
                $view = new View('userCreate');
                echo $view->render();
            }
        } catch (BankException $ex) {
            $view = new View('exception');
            echo $view->addData("exception", $ex)->addData("back", "userCreate")->render();
        }
    }
}
