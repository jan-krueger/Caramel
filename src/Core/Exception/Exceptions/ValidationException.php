<?php

namespace Caramel\Core\Exception\Exceptions;

class ValidationException extends \Exception
{

    public function __construct(public readonly array $errors, public readonly array $old) {}

}