<?php
namespace ktc\a2\controller;

use ktc\a2\Exception\BankException;
use ktc\a2\model\AccountModel;
use ktc\a2\model\AccountCollectionModel;
use ktc\a2\view\View;

/**
 * Class AccountController
 *
 * @package ktc/a2
 * @author  Andrew Gilman <a.gilman@massey.ac.nz>
 */
class AccountController extends Controller
{
    /**
     * Account Index action
     */
    public function indexAction()
    {
        session_start();
        if (isset($_SESSION['userName'])) {
            try {
                $userName = $_SESSION['userName'];
                $userId = $_SESSION['userId'];
                $collection = new AccountCollectionModel($userName, $userId);
                $accounts = $collection->getAccounts();
                $view = new View('accountIndex');
                echo $view->addData('accounts', $accounts)->render();
            } catch (BankException $ex) {
                if ($ex->getCode() == 9) {
                    $this->redirect('accountCreate');
                } else {
                    $view = new View('exception');
                    echo $view->addData("exception", $ex)->addData("back", "Home")->render();
                }
            }
        } else {
            $this->redirect('Home');
        }
    }

    /**
     * Account Create action
     * @throws BankException
     */
    public function createAction()
    {
        session_start();
        if (isset($_POST['create'])) {
            $account = new AccountModel();
            $account->setType($_POST['accountType']);
            $account->setUser($_POST['userID']);
            $account->setBalance(0.0);
            $account->setDateStarted(date("d/m/y"));
            $account->save();
            if(!$account)
            {
                throw new BankException(0);
            }
            $view = new View('accountCreate');
            echo $view->render();
        } else{
            $view = new View('accountCreate');
            echo $view->render();
        }
    }

    /**
     * Account Delete action
     *
     * @param int $id Account id to be deleted
     * @throws BankException
     */
    public function deleteAction($id)
    {
        session_start();
        try {
            (new AccountModel())->load($id)->delete();
        } catch (BankException $e) {
            throw new BankException('Failed to load account');
        }
        $view = new View('accountDeleted');
        echo $view->addData('accountId', $id)->render();
    }

    /**
     * Account Update action
     *
     * @param int $id Account id to be updated
     * @throws BankException
     */
    public function updateAction($id)
    {
        session_start();
        try {
            $account = (new AccountModel())->load($id);
        } catch (BankException $e) {
            throw new BankException('Failed to load account');
        }
        $account->setName('Joe')->save(); // new name will come from Form data
    }

    public function depositAction($id)
    {
        session_start();
        if (isset($_POST['deposit'])) {
            try {
                $account = (new AccountModel())->load($id);
                $account->deposit($_POST['depositAmount']);
                $account->save();
                if (!$account) {
                    throw new BankException(0);
                }
                $view = new View('accountDeposited');
                echo $view->addData("amount", $_POST['depositAmount'])->addData("account", $account)->addData("back", "accountIndex")->render();
            } catch (BankException $ex) {
                $view = new View('exception');
                echo $view->addData("exception", $ex)->addData("back", "accountIndex")->render();
            }
        } else {
            $view = new View('accountDeposit');
            echo $view->render();
        }
    }


    public function withdrawAction($id)
    {
        session_start();
        if (isset($_POST['withdraw'])) {
            $account = (new AccountModel())->load($id);
            $account->withdraw($_POST['withdrawalAmount']);
            $account->save();
            if(!$account)
            {
                throw new BankException('Failed to load account');
            }
            $view = new View('accountWithdraw');
            echo $view->render();
        } else{
            $view = new View('accountWithdraw');
            echo $view->render();
        }

    }
}
