<?php
namespace ktc\a2\controller;

use ktc\a2\Exception\BankException;
use ktc\a2\model\{AccountModel, AccountCollectionModel};
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
        $collection = new AccountCollectionModel();
        $accounts = $collection->getAccounts();
        $view = new View('accountIndex');
        echo $view->addData('accounts', $accounts)->render();
    }

    /**
     * Account Create action
     * @throws BankException
     */
    public function createAction()
    {
        if (isset($_POST['create'])) {
            $account = new AccountModel();
            $account->setType($_POST['accountType']);
            $account->setUser($_POST['userID']);
            $account->setBalance(0.0);
            $account->setDateStarted(date("d/m/y"));
            $account->save();
            if(!$account)
            {
                throw new BankException("Failed to create account");
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
     */
    public function deleteAction($id)
    {
        (new AccountModel())->load($id)->delete();
        $view = new View('accountDeleted');
        echo $view->addData('accountId', $id)->render();
    }
    /**
     * Account Update action
     *
     * @param int $id Account id to be updated
     */
    public function updateAction($id)
    {
        $account = (new AccountModel())->load($id);
        $account->setName('Joe')->save(); // new name will come from Form data
    }
}
