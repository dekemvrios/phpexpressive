<?php

namespace Solis\Expressive;

use Solis\Breaker\Abstractions\TExceptionAbstract;
use Solis\Breaker\Classes\TInfo;

/**
 * Class Exception
 *
 * @package Solis\Expressive
 */
class Exception extends TExceptionAbstract
{

    /**
     * __construct
     *
     * @param mixed $reason explanation for TException
     * @param mixed $code   error code
     */
    public function __construct($reason, $code)
    {
        // create new Tinfo object to store default TException information
        $error = Tinfo::build([
          'code'    => $code,
          'message' => $reason,
        ]);

        // create new Tinfo object to store debug TException information
        $debug = Tinfo::build([
          'class'  => $this->getClassName(),
          'method' => $this->getMethodName(),
          'trace'  => $this->getTrace(),
        ]);

        parent::__construct($error, $debug);

        $this->message = $reason;
    }

    protected function getClassName()
    {
        $stack = $this->getTrace();
        $class = $stack[0]['class'] ?? '';

        return $class;
    }

    protected function getMethodName()
    {
        $stack  = $this->getTrace();
        $method = $stack[0]['function'] ?? '';

        return $method;
    }
}
