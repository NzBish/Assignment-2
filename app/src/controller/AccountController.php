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
 * @author  K. Dempsey
 * @author  T. Crompton
 * @author  C. Bishop
 */
class AccountController extends Controller
{
    /**
     * Account Index action
     *
     * If the user is logged in:
     * - Creates and uses an AccountCollectionModel object based on provided username and ID
     *   to create an AccountModel generator
     * - Creates and renders an accountIndex template with the AccountModel generator attached
     * Otherwise redirects to HomeController::indexAction
     *
     * @uses $_SESSION['userName'] to determine if user is logged in and set $userName
     * @uses $_SESSION['userId'] to set $userId
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
                $view = new View('exception');
                echo $view->addData("exception", $ex)->addData("back", "Home")->render();
                if ($ex->getCode() == 9) {
                    $_SESSION['noAcc'] = true;
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
     *
     * If the user is logged in, either:
     * - Creates and renders an accountCreate template to be filled by the user
     * or:
     * - Creates a new AccountModel based on the provided form data
     * Otherwise redirects to HomeController::indexAction
     *
     * @uses $_SESSION['userName'] to determine if user is logged in
     * @uses $_POST['create'] to determine whether to create an AccountModel or an accountCreate
     * @uses $_POST['accountType'] to set $account->type
     * @uses $_POST['userId'] or $_SESSION['userId'] to set $account->user
     */
    public function createAction()
    {
        session_start();
        if (isset($_SESSION['userName'])) {
            if (isset($_POST['create'])) {
                try {
                    if ($_POST['accountType'] == "undefined") {
                        throw new BankException(99, "You must select an account type");
                    }
                    $account = new AccountModel();
                    $account->setType($_POST['accountType']);
                    if (isset($_POST['userId'])) {
                        $account->setUser($_POST['userId']);
                    } else {
                        $account->setUser($_SESSION['userId']);
                    }
                    $account->setBalance(0.0);
                    $account->setDateStarted(date("d/m/Y"));
                    $account->save();
                    if (!$account) {
                        throw new BankException(0);
                    }
                    if (isset($_SESSION['noAcc'])) {
                        unset($_SESSION['noAcc']);
                    }
                    if ($_POST['makeDeposit']) {
                        $this->redirect('accountDeposit', ['id' => $account->getId()]);
                    } else {
                        $this->redirect('accountIndex');
                    }
                } catch (BankException $ex) {
                    $view = new View('exception');
                    echo $view->addData("exception", $ex)->addData("back", "Home")->render();
                }
            } else {
                $view = new View('accountCreate');
                echo $view->render();
            }
        } else {
            $this->redirect('Home');
        }
    }

    /**
     * Account Delete action
     *
     * If the user is logged in, either:
     * - Deletes an account based on the provided $id, if the user is "admin", then creates and
     *   renders an accountDeleted template with the $id attached
     * or:
     * - Creates and renders an exception template asking the user to call in instead
     *
     * @param int $id Account id to be deleted
     * @uses $_SESSION['userName'] to determine if the logged in user is "admin"
     */
    public function deleteAction($id)
    {
        session_start();
        if ($_SESSION['userName'] == "admin") {
            try {
                (new AccountModel())->load($id)->delete();
            } catch (BankException $ex) {
                $view = new View('exception');
                echo $view->addData("exception", $ex)->addData("back", "accountIndex")->render();
            }
            $view = new View('accountDeleted');
            echo $view->addData('accountId', $id)->render();
        } else {
            $view = new View('exception');
            echo $view->addData("exception", (new BankException(
                99,
                "Closing account via internet banking unavailable. Please contact us by phone to close an account"
            )))->addData("back", "accountIndex")->render();
        }
    }

    /**
     * Account Deposit action
     *
     * Either:
     * - Creates and renders an accountDeposit template to be filled by the user
     * or:
     * - Creates an AccountModel from $id to call $account->deposit to increase account balance,
     *   then creates and renders an accountDeposited template to confirm success
     *
     * @param int $id Account ID to make the deposit into
     * @uses $_POST['deposit'] to determine whether to create an accountDeposit or an AccountModel
     * @uses $_POST['depositAmount'] to set $account->deposit($amount)
     */
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
                echo $view->addData("amount", $_POST['depositAmount'])->addData("account", $account)->render();
            } catch (BankException $ex) {
                $view = new View('exception');
                echo $view->addData("exception", $ex)->addData("back", "accountIndex")->render();
            }
        } else {
            $view = new View('accountDeposit');
            echo $view->render();
        }
    }

    /**
     * Account Withdraw action
     *
     * Either:
     * - Creates and renders an accountWithdraw template to be filled by the user
     * or:
     * - Creates an AccountModel from $id to call $account->withdraw to decrease account balance,
     *   then creates and renders an accountWithdrawn template to confirm success
     *
     * @param int $id Account ID to take the withdrawal from
     * @uses $_POST['withdraw'] to determine whether to create an accountWithdraw or an AccountModel
     * @uses $_POST['withdrawalAmount'] to set $account->withdraw($amount)
     */
    public function withdrawAction($id)
    {
        session_start();
        if (isset($_POST['withdraw'])) {
            try {
                $account = (new AccountModel())->load($id);
                $withdrawalAmount = $_POST['withdrawalAmount'];
                if ($withdrawalAmount <= $account->getBalance()) {
                    $account->withdraw($_POST['withdrawalAmount']);
                    $account->save();
                } else {
                    throw new BankException(99, "You can't withdraw more than than your balance");
                }
                if (!$account) {
                    throw new BankException(0);
                }
                $view = new View('accountWithdrawn');
                echo $view->addData("amount", $_POST['withdrawalAmount'])
                    ->addData("account", $account)->addData("back", "accountIndex")->render();
            } catch (BankException $ex) {
                $view = new View('exception');
                echo $view->addData("exception", $ex)->addData("back", "accountIndex")->render();
            }
        } else {
            $view = new View('accountWithdraw');
            echo $view->render();
        }
    }
}
