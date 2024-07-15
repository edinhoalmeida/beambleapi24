<?php

namespace Babel;

use Babel\Drivers\Google as GoogleDriver;
use Babel\Drivers\OpenaiClient as OpenaiDriver;

class Babel
{

    private $text_to_text_driver = 'openai';
    // private $text_to_text_driver = 'google';

    // private $text_to_speech_driver = 'openai';
    private $text_to_speech_driver = 'google';

    private $speech_to_text_driver = 'openai';
    // private $speech_to_text_driver = 'google';

    public function text_to_text($text = "bom dia, amigos!", $targetLanguage = 'fr', $fromLanguage = 'auto')
    {
        if($this->text_to_text_driver=='openai'){
            $translated = OpenaiDriver::text_to_text($text, $targetLanguage, $fromLanguage);
            return $translated;
        } else {
            $translated = GoogleDriver::text_to_text($text, $targetLanguage, $fromLanguage);
            return $translated;
        }
        
    }

    public function text_to_speech($text, $fromLanguage)
    {
        if($this->text_to_speech_driver=='openai'){
            $translated = OpenaiDriver::text_to_speech($text, $fromLanguage);
            return $translated;
        } else {
            $translated = GoogleDriver::text_to_speech($text, $fromLanguage);
            return $translated;
        }
    }

    public function speech_to_text($audio = null, $fromLanguage = 'fr')
    {
        if($this->speech_to_text_driver=='openai'){
            $translated = OpenaiDriver::speech_to_text($audio, $fromLanguage);
            return $translated;
        } else {
            $translated = GoogleDriver::speech_to_text($audio, $fromLanguage);
            return $translated;
        }
    }

}
