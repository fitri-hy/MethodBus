<?php

namespace MethodBus\Core;

use MethodBus\Exceptions\ValidationException;

final class SchemaValidator
{
    public function validate(array $rules, array $payload): array
    {
        foreach ($rules as $field => $rule) {

            $value = $payload[$field] ?? null;

            if (str_contains($rule, 'required') && $value === null) {
                throw new ValidationException("Field '{$field}' is required");
            }

            if (str_contains($rule, 'numeric') && $value !== null && !is_numeric($value)) {
                throw new ValidationException("Field '{$field}' must be numeric");
            }
        }

        return $payload;
    }
}