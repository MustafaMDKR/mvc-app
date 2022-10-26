<?php
namespace app\core;

abstract class Model
{
    public const RULE_REQUIRED = 'required';
    public const RULE_EMAIL = 'email';
    public const RULE_MIN = 'min';
    public const RULE_MAX = 'max';
    public const RULE_MATCH = 'match';
    public const RULE_UNIQUE = 'unique';
    public array $errors = [];

    public function loadData($data)
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }
    }

    abstract public function rules(): array;

    public function validate()
    {
        foreach ($this->rules() as $attr => $rules) {
            $value = $this->{$attr};
            foreach ($rules as $rule) {
                $ruleName = $rule;
                if (!is_string($ruleName)) {
                    $ruleName = $rule[0];
                }

                if ($ruleName === self::RULE_REQUIRED && !$value) {
                    $this->addErrorForRule($attr, self::RULE_REQUIRED);
                }

                if ($ruleName === self::RULE_EMAIL && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->addErrorForRule($attr, self::RULE_EMAIL);
                }

                if ($ruleName === self::RULE_MIN && strlen($value) < $rule['min']) {
                    $this->addErrorForRule($attr, self::RULE_MIN, $rule);
                }

                if ($ruleName === self::RULE_MAX && strlen($value) > $rule['max']) {
                    $this->addErrorForRule($attr, self::RULE_MAX, $rule);
                }

                if ($ruleName === self::RULE_MATCH && $value !== $this->{$rule['match']}) {
                    $rule['match'] = $this->getLabel($rule['match']);
                    $this->addErrorForRule($attr, self::RULE_MATCH, $rule);
                }

                if ($ruleName === self::RULE_UNIQUE) {
                    $className = $rule['class'];
                    $uniqueAttr = $rule['attr'] ?? $attr;
                    $tableName = $className::tableName();
                    $statement = Application::$app->db->prepare("SELECT * FROM $tableName WHERE
                        $uniqueAttr = :attr
                    ");
                    $statement->bindValue(":attr", $value);
                    $statement->execute();
                    $record = $statement->fetchObject();
                    if ($record) {
                        $this->addErrorForRule($attr, self::RULE_UNIQUE, ['field' => $this->getLabel($attr)]);
                    }
                }
            }
        }
        return empty($this->errors);
    }


    public function labels(): array
    {
        return [];
    }


    public function getLabel($attr)
    {
        return $this->labels()[$attr] ?? $attr;
    }


    private function addErrorForRule(string $attr, string $rule, $params = [])
    {
        $message = $this->errorMessage()[$rule] ?? '';
        foreach ($params as $key => $value) {
            $message = str_replace("{{$key}}", $value, $message); 
        }
        $this->errors[$attr][] = $message;
    }


    public function addError(string $attr, string $message)
    {
        $this->errors[$attr][] = $message;
    }


    public function errorMessage()
    {
        return [
            self::RULE_REQUIRED => 'This field is required.',
            self::RULE_EMAIL => 'This field must be a valid email.',
            self::RULE_MIN => 'Min length must be {min} chars.',
            self::RULE_MAX => 'Max length must be {max} chars.',
            self::RULE_MATCH => 'This must be a match of {match}.',
            self::RULE_UNIQUE => 'This {field} already exists'
        ];
    }

    public function hasError($attr)
    {
        return $this->errors[$attr] ?? false; 
    }

    public function getFirstError($attr)
    {
        $errors = $this->errors[$attr] ?? [];
        return $errors[0] ?? '';
    }
}