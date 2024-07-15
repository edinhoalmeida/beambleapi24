<?php

namespace OpenAI\Enums;

class BaseEnum
{
    static function getConstants() {
        $oClass = new ReflectionClass(__CLASS__);
        return $oClass->getConstants();
    }
}
