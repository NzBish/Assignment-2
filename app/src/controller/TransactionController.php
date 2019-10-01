<?php
namespace agilman\a2\controller;

use agilman\a2\Exception\BankException;
use agilman\a2\model\{TransactionModel, TransactionCollectionModel};
use agilman\a2\view\View;


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
