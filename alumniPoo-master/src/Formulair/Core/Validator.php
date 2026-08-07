<?php

namespace Formulair\Core;

class Validator
{
    private array $errors = [];
    private array $data = [];

    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    public function validate(array $rules): bool
    {
        $this->errors = [];

        foreach ($rules as $field => $fieldRules) {
            $value = $this->data[$field] ?? null;
            $rules_array = is_string($fieldRules) ? explode('|', $fieldRules) : $fieldRules;
            $isNumericField = in_array('numeric', $rules_array, true);

            foreach ($rules_array as $rule) {
                $this->applyRule($field, $value, $rule, $isNumericField);
            }
        }

        return count($this->errors) === 0;
    }

    private function applyRule(string $field, mixed $value, string $rule, bool $isNumericField): void
    {
        if (strpos($rule, ':') !== false) {
            [$ruleName, $parameter] = explode(':', $rule, 2);
        } else {
            $ruleName = $rule;
            $parameter = null;
        }

        match ($ruleName) {
            'required' => $this->validateRequired($field, $value),
            'email' => $this->validateEmail($field, $value),
            'min' => $this->validateMin($field, $value, (int)$parameter, $isNumericField),
            'max' => $this->validateMax($field, $value, (int)$parameter, $isNumericField),
            'numeric' => $this->validateNumeric($field, $value),
            'alpha' => $this->validateAlpha($field, $value),
            'alphanumeric' => $this->validateAlphanumeric($field, $value),
            'url' => $this->validateUrl($field, $value),
            'date' => $this->validateDate($field, $value),
            'regex' => $this->validateRegex($field, $value, $parameter),
            default => null,
        };
    }

    private function validateRequired(string $field, mixed $value): void
    {
        if (empty($value)) {
            $this->addError($field, "$field is required");
        }
    }

    private function validateEmail(string $field, mixed $value): void
    {
        if (!empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->addError($field, "$field must be a valid email address");
        }
    }

    private function validateMin(string $field, mixed $value, int $min, bool $isNumericField): void
    {
        if (empty($value)) {
            return;
        }

        if ($isNumericField) {
            if (is_numeric($value) && (float) $value < $min) {
                $this->addError($field, "$field must be at least $min");
            }
            return;
        }

        if (strlen($value) < $min) {
            $this->addError($field, "$field must be at least $min characters");
        }
    }

    private function validateMax(string $field, mixed $value, int $max, bool $isNumericField): void
    {
        if (empty($value)) {
            return;
        }

        if ($isNumericField) {
            if (is_numeric($value) && (float) $value > $max) {
                $this->addError($field, "$field must not exceed $max");
            }
            return;
        }

        if (strlen($value) > $max) {
            $this->addError($field, "$field must not exceed $max characters");
        }
    }

    private function validateNumeric(string $field, mixed $value): void
    {
        if (!empty($value) && !is_numeric($value)) {
            $this->addError($field, "$field must be numeric");
        }
    }

    private function validateAlpha(string $field, mixed $value): void
    {
        if (!empty($value) && !preg_match('/^[\p{L}\s-]+$/u', $value)) {
            $this->addError($field, "$field must contain only letters");
        }
    }

    private function validateAlphanumeric(string $field, mixed $value): void
    {
        if (!empty($value) && !ctype_alnum(str_replace(' ', '', $value))) {
            $this->addError($field, "$field must be alphanumeric");
        }
    }

    private function validateUrl(string $field, mixed $value): void
    {
        if (!empty($value) && !filter_var($value, FILTER_VALIDATE_URL)) {
            $this->addError($field, "$field must be a valid URL");
        }
    }

    private function validateDate(string $field, mixed $value): void
    {
        if (!empty($value)) {
            $date = \DateTime::createFromFormat('Y-m-d', $value);
            if (!$date || $date->format('Y-m-d') !== $value) {
                $this->addError($field, "$field must be a valid date (Y-m-d format)");
            }
        }
    }

    private function validateRegex(string $field, mixed $value, ?string $pattern): void
    {
        if (!empty($value) && $pattern && !preg_match($pattern, $value)) {
            $this->addError($field, "$field format is invalid");
        }
    }

    private function addError(string $field, string $message): void
    {
        if (!isset($this->errors[$field])) {
            $this->errors[$field] = [];
        }
        $this->errors[$field][] = $message;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function getFirstError(string $field): ?string
    {
        return $this->errors[$field][0] ?? null;
    }

    public function hasError(string $field): bool
    {
        return isset($this->errors[$field]);
    }
}
