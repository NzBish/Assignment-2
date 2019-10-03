<?php

namespace ktc\a2\Exception;

/**
 * Class BankException
 *
 * @package ktc/a2
 *
 * @author
 */

class BankException extends \Exception
{
   public function BankException($message)
   {
       parent::__construct($message);
   }
   /*
    public function __construct($code = 0)
    {
        switch($code) {
            case 0:
                $message = 'Failed to load account';
                break;
            case 1:
                $message = 'Invalid Transaction Type';
                break;
            case 2:
                $message = 'Invalid Amount';
                break;
            case 3:
                $message = 'Insufficient Balance';
                break;
            default:
                $message = 'Invalid Data Entered';
                break;
        }

        parent::__construct($message, $code);
    }*/
}
