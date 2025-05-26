<?php

namespace AuthLib\Validation;

/**
 * Interface for input validation
 */
interface InputValidatorInterface
{
    /**
     * Validate that required fields are present and not empty
     *
     * @param array $input Input data to validate
     * @param array $requiredFields List of required field names
     * @return bool Whether validation passed
     */
    public function validateRequired(array $input, array $requiredFields): bool;
}
