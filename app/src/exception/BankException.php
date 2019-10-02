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
}
