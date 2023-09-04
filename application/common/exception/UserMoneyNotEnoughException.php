<?php


namespace app\common\exception;


use think\Exception;
use Throwable;

class UserMoneyNotEnoughException extends Exception
{

    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

}