<?php namespace Lingu\System;

    class Exception extends \Exception
    {
        public function __construct(string $msg)
        {
            parent::__construct($msg);

            $this->line = parent::getTrace()[0]["line"];
            $this->file = parent::getTrace()[0]["file"];
        }

        public static function new(string $message, int $i = 1)
        {
            $exception = new self($message);

            $exception->line = $exception->getTrace()[$i]["line"];
            $exception->file = $exception->getTrace()[$i]["file"];

            return $exception;
        }
    }