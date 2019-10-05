<?php
namespace ktc\a2\controller;

use ktc\a2\Exception\BankException;
use ktc\a2\model\TransactionModel;
use ktc\a2\model\TransactionCollectionModel;
use ktc\a2\view\View;
/**
 * Class TransactionController
 *
 * @package ktc/a2
 * @author  Andrew Gilman <a.gilman@massey.ac.nz>
 */

class TransactionController extends Controller
{

    public function indexAction()
    {
        session_start();
        if (isset($_SESSION['userName'])) {
            try {
                $userName = $_SESSION['userName'];
                $userId = $_SESSION['userId'];
                $collection = new TransactionCollectionModel($userName, $userId);
                $transactions = $collection->getTransactions();
                $view = new View('transactionIndex');
                echo $view->addData('transactions', $transactions)->render();
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



}
