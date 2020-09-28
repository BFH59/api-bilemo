<?php


namespace App\Service;


use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Validator\ConstraintViolationList;

class ConstraintsChecker
{

    public function checkRequest(ConstraintViolationList $violations)
    {
        if (count($violations) ) {
            $message = 'Le Json contient des données incorrectes: ';
            foreach ($violations as $violation) {
                $message .= sprintf(
                    "Champ %s: %s ;",
                    $violation->getPropertyPath(),
                    $violation->getMessage()
                );
            }
            throw new HttpException(400, $message);
        }
    }
}