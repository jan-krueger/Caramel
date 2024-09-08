<?php

namespace Caramel\Core\Verifier;

use Caramel\Core\Collection\Collection;
use Caramel\Core\Exception\Exceptions\ValidationException;
use Caramel\Core\Http\Request;


class Verifier
{

    public static function verify(Request|Collection|array $data, array $rules)
    {
        if($data instanceof Request)
        {
            $data = $data->body();
        }
        else if($data instanceof Collection)
        {
            $data = $data->toArray();
        }

        return new Verifier($data, $rules);
    }

    private array $rules = [];
    private array $messages = [];
    private array $errors = [];

    public function __construct(private array $data, array $rules)
    {
        foreach($rules as $field => $rule_string)
        {
            foreach(explode('|', $rule_string) as $rule)
            {
                [$name, $params_string] = explode(':', $rule, 2);
                $params = is_null($params_string) ? [] : explode(',',$params_string);
                $this->rule($field, $name, $params);
            }
        }
    }

    public function rule(string $field, string $rule, array $params): self
    {
        $this->rules[$field][$rule] = $params;
        return $this;
    }

    public function message(string $field, string $message): self
    {
        $this->messages[$field] = $message;
        return $this;
    }

    public function validate(): bool
    {
        $this->errors = [];

        foreach($this->rules as $field => $rules) 
        {
            foreach($rules as $rule => $params)
            {
                $value = $this->data[$field] ?? null;
                array_unshift($params, $value); // --- prepend value

                if(!call_user_func_array([$this, "validate_{$rule}"], $params)) 
                {
                    $this->errors[$field][$rule] = $this->messages[$field] ?? "The $field field is invalid.";
                }
            }
        }

        return empty($this->errors);
    }

    public function validateOrFail():void
    {
        if(!$this->validate())
        {
            throw new ValidationException($this->errors, $this->data);
        }
    }

    public function errors(): array
    {
        return $this->errors;
    }

    private function validate_required($value): bool
    {
        return !empty($value);
    }

    private function validate_email($value): bool
    {
        return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
    }

    private function validate_min($value, $min): bool
    {
        return strlen($value) >= (int) $min;
    }

    private function validate_max($value, $max): bool
    {
        return strlen($value) <= (int) $max;
    }

    private function validate_between($value, $min, $max): bool
    {
        $length = strlen($value);
        return $length >= (int) $min && $length <= (int) $max;
    }

    private function validate_in($value, ...$values): bool
    {
        return in_array($value, $values, true);
    }
}
