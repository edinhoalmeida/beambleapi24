<?php
namespace Babel\Drivers;

use Babel\Contract\Contract as BabelContract;
use Babel\Exceptions\NotImplemented as BabelNotImplemented;

use Babel\BabelRessource;

use OpenAI;

class OpenaiClient implements BabelContract {


    public static function get_client(){
        $ApiKey = getenv('OPENAI_KEY');
        return OpenAI::client($ApiKey);
    }

    public static function text_to_text($text, $targetLanguage = 'fr', $fromLanguage = 'auto'){
        $client = self::get_client();
        if($fromLanguage=='auto'){
            $detect  = "Can you please provide a brief answer?\n";
            $detect .= "what are the ISO 639-1 code language of a text?\n";
            $detect .= "Text: " . $text;

            $result = $client->completions()->create([
                'model' => 'gpt-3.5-turbo-instruct',
                'prompt' => $detect,
                'temperature' => 0,
                'max_tokens' => 2000,
                'top_p' => 1,
                'frequency_penalty' => 0.5,
                'presence_penalty' => 0
            ]);
            $fromLanguage = 'auto';
            foreach ($result->choices as $line) {
                $ret_text  = trim($line->text, " \n\r");
                break;
            }
            if(!empty($ret_text)){
                if(preg_match('/is (?<code_language>\w\w) \([^\)]+\)/', $ret_text, $matches)) {
                    $fromLanguage = $matches['code_language'];
                } else {
                    $fromLanguage = 'auto';
                }
            }
            if($fromLanguage != 'auto'){
                $prompt = 'Translate these text from ['.$fromLanguage.'] to ['.$targetLanguage.']:
                    Text:'.$text.'
                Translation:';
            } else {
                $prompt = 'Translate these text to ['.$targetLanguage.']:
                Text:'.$text.'
                Translation:';
            }

        } else {
            $prompt = 'Translate these text from ['.$fromLanguage.'] to ['.$targetLanguage.']:
            Text:'.$text.'
            Translation:';
        }
        $result = $client->completions()->create([
            'model' => 'gpt-3.5-turbo-instruct',
            'prompt' => $prompt,
            'temperature' => 0,
            'max_tokens' => 2000,
            'top_p' => 1,
            'frequency_penalty' => 0.5,
            'presence_penalty' => 0
        ]);

        $babel_resource = new BabelRessource;
        $babel_resource->from_text = $text;
        $babel_resource->from_lang = $fromLanguage;
        $babel_resource->target_lang = $targetLanguage;
        $babel_resource->from_lang = $fromLanguage;
        $translated = $text;
        foreach ($result->choices as $line) {
            $translated = trim($line->text, " \n\r");
            break;
        }
        $babel_resource->target_text = $translated;
        $babel_resource->service_version = 'OpenAIV1 : ' . $result->getModelName();
        return $babel_resource;
    
    }

    public static function text_to_speech($fromText, $languageCode){
        throw new BabelNotImplemented(self::class);
    } 

    public static function speech_to_text($path_audio = null, $fromLanguage = 'fr'){
        $client = self::get_client();

        $result = $client->audio()->transcribe([
            'model' => 'whisper-1',
            'file' => $path_audio,
            'response_format' => 'verbose_json',
            'temperature' => 0,
            'max_tokens' => 2000,
            'top_p' => 1,
            'frequency_penalty' => 0.5,
            'presence_penalty' => 0
        ]);

        // $result->task; // 'transcribe'
        // $result->language; // 'english'
        // $result->duration; // 2.95
        // $result->text; // 'Hello, how are you?'

        // File uploads are currently limited to 25 MB and the following input file types are supported: mp3, mp4, mpeg, mpga, m4a, wav, and webm

        $path_audio = explode(DIRECTORY_SEPARATOR, $path_audio);
        $single_name = end($path_audio);

        $babel_resource = new BabelRessource;
        $babel_resource->from_text = $result->text;
        $babel_resource->from_lang = $fromLanguage;
        $babel_resource->target_lang = null;
        $babel_resource->target_text = null;
        $babel_resource->file_name = $single_name;
        $babel_resource->service_version = 'OpenAIV1:Whisper-1';

        return (array) $babel_resource;
    }

}
