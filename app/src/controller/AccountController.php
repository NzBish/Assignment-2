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
        try {
            $account = (new AccountModel())->load($id);
        } catch (BankException $e) {
            throw new BankException('Failed to load account');
        }
        $account->setName('Joe')->save(); // new name will come from Form data
    }

    public function depositAction($id)
    {
        if (isset($_POST['deposit'])) {
            $account = (new AccountModel())->load($id);
            $account->deposit($_POST['Failed to load account']);
            $account->save();
            if(!$account)
            {
                throw new BankException(0);
            }
            $view = new View('accountDeposit');
            echo $view->render();
        } else{
            $view = new View('accountDeposit');
            echo $view->render();
        }

    }


    public function withdrawAction()
    {
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
