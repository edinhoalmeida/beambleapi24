<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class Cnpj implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $cnpj = preg_replace('/[^0-9]/', '', $value);

        if (strlen($cnpj) <> 14)
        {
            return false;
        }

        $calcOne = 0;
        $calcTwo = 0;

        // Check first digit.
        for ($i = 0, $x = 5; $i <= 11; $i++, $x--)
        {
            $x         = ($x < 2) ? 9 : $x;
            $numberOne = substr($cnpj, $i, 1);
            $calcOne   += $numberOne * $x;
        }

        // Check second digit.
        for ($i = 0, $x = 6; $i <= 12; $i++, $x--)
        {
            $x         = ($x < 2) ? 9 : $x;
            $numberTwo = substr($cnpj, $i, 1);
            $calcTwo   += $numberTwo * $x;
        }

        $digitOne = (($calcOne % 11) < 2) ? 0 : 11 - ($calcOne % 11);
        $digitTwo = (($calcTwo % 11) < 2) ? 0 : 11 - ($calcTwo % 11);

        // Test the CNPJ if is valid.
        if ($digitOne <> substr($cnpj, 12, 1) || $digitTwo <> substr($cnpj, 13, 1))
        {
            return false;
        }
        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Cnpj não é válido.';
    }
}
