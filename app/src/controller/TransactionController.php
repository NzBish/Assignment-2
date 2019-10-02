<?php
namespace ktc\a2\controller;

use ktc\a2\Exception\BankException;
use ktc\a2\model\{TransactionModel, TransactionCollectionModel};
use ktc\a2\view\View;
/**
 * Class TransactionController
 *
 * @package ktc/a2
 * @author
 */

class TransactionController extends Controller
{

    public function indexAction()
    {
        $collection = new TransactionCollectionModel();
        $transactions = $collection->getTransactions();
        $view = new View('transactionIndex');
        echo $view->addData('transactions', $transactions)->render();
    }
}
