<?php
namespace ctk\a2\controller;

use ctk\a2\Exception\BankException;
use ctk\a2\model\{TransactionModel, TransactionCollectionModel};
use ctk\a2\view\View;
/**
 * Class TransactionController
 *
 * @package ctk/a2
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
