<?php

declare(strict_types=1);

namespace App\Application\Exception;

use Symfony\Component\Validator\ConstraintViolationListInterface;

/**
 * Exception thrown when request validation fails
 */
final class ValidationException extends \RuntimeException
{
    /** @var array<string, string> */
    private array $errors;

    public function __construct(ConstraintViolationListInterface $violations)
    {
        $errors = [];
        foreach ($violations as $violation) {
            $propertyPath = $violation->getPropertyPath();
            $errors[$propertyPath] = (string) $violation->getMessage();
        }

        $this->errors = $errors;

        parent::__construct('Validation failed', 400);
    }

    /**
     * Get validation errors
     *
     * @return array<string, string>
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
