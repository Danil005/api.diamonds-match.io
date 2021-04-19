<?php

namespace App\Utils;

trait Phone
{
    public function phoneFormatting(string $phone)
    {
        if(  preg_match( '/^\+\d(\d{3})(\d{3})(\d{4})$/', $phone,  $matches ) )
        {
            $result = $matches[1] . '-' .$matches[2] . '-' . $matches[3];
            return $result;
        }
    }
}
