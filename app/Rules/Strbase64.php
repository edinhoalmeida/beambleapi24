<?php
namespace App\Rules;
 
use Illuminate\Contracts\Validation\InvokableRule;
 
class Strbase64 implements InvokableRule
{
    public function __invoke($attribute, $value, $fail)
    {
        if( ! is_string($value) || strpos($value,'data:image')!==0){
            $fail('Error :attribute . The image format is invalid. It should start with ´data:image´ ');
        }
    }
}