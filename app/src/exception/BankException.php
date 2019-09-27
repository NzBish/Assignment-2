<?php

namespace agilman\a2\Exception;


class BankException extends \Exception
{
   public function BankException($message)
   {
       parent::__construct($message);
   }
}
