<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;


class CreditCard implements Rule
{
    /**
     * Determine if the validation rule passes.
     */
    public function passes($attribute, $value): bool
    {
        if ($this->isValidLuhn($value)) {
            return true;
        }

        return false;
    }

    /**
     * Get the validation error message.
     */
    public function message(): string
    {
        return 'The credit card has not passed Luhn validation.';
    }

    /**
     * Luhn algorithm number checker.
     * https://gist.github.com/troelskn/1287893
     * https://en.wikipedia.org/wiki/Luhn_algorithm
     */
    private function isValidLuhn($number): bool
    {
        settype($number, 'string');
        $sumTable = array(
            array(0,1,2,3,4,5,6,7,8,9),
            array(0,2,4,6,8,1,3,5,7,9));
        $sum = 0;
        $flip = 0;
        for ($i = strlen($number) - 1; $i >= 0; $i--) {
            $sum += $sumTable[$flip++ & 0x1][$number[$i]];
        }
        return $sum % 10 === 0;
    }
}
