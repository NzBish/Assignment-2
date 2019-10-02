<?php

namespace ctk\a2\Exception;

/**
 * Class BankException
 *
 * @package ctk/a2
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
