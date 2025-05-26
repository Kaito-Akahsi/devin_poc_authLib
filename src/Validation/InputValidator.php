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
        return true;
    }
}
