<?php
declare(strict_types = 1);

namespace AppBundle\Validator;

use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\TraceableValidator;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class Validator
 */
class Validator extends TraceableValidator
{
    /**
     * @param ValidatorInterface $validator
     */
    public function __construct(ValidatorInterface $validator)
    {
        parent::__construct($validator);
    }

    /**
     * @param ConstraintViolationList $errors
     *
     * @return array
     */
    public function parseErrors(ConstraintViolationList $errors): array
    {
        $errorsArray = [];

        foreach ($errors->getIterator() as $val) {
            $key = $val->getPropertyPath();
            $errorsArray[$key] = $val->getMessage();
        }

        return $errorsArray;
    }
}
