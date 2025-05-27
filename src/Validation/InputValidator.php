<?php

namespace AuthLib\Validation;

/**
 * Stub implementation of input validator
 */
class InputValidator implements InputValidatorInterface
{
    /**
     * {@inheritdoc}
     */
    public function validateRequired(array $input, array $requiredFields): bool
    {
        foreach ($requiredFields as $field) {
            if (!isset($input[$field]) || $input[$field] === '' || $input[$field] === null) {
                return false;
            }
        }
        
        return true;
    }
}
