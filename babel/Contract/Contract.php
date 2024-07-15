<?php

namespace Babel\Contract;

interface Contract
{

    public static function text_to_text($text, $targetLanguage = 'fr', $fromLanguage = 'auto');

    public static function text_to_speech($fromText, $fromLanguage);

    public static function speech_to_text($audio = null, $fromLanguage = 'fr');

}
