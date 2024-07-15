<?php
namespace App\Libs;

// define('GOOGLE_CLOUD_PROJECT', 'braided-verve-374914');

// use Illuminate\Contracts\Console\Kernel;

// if (isset($app)) {
//     $json_google_api = config_path('braided-verve-374914-c48203055ff0.json');
// } else {
//     $app = require __DIR__.'/../../bootstrap/app.php';
//     $app->make(Kernel::class)->bootstrap();
//     $json_google_api = $app->configPath('braided-verve-374914-c48203055ff0.json');
// }
// define('GOOGLE_APPLICATION_CREDENTIALS', $json_google_api);
// putenv("GOOGLE_APPLICATION_CREDENTIALS=$json_google_api");

// Google Core API
use Google\ApiCore\ApiException as GoogleException;

// Google Translation Text-to-Text 
use Google\Cloud\Translate\V3\TranslationServiceClient;

// Google Speech-to-text
use Google\Cloud\Speech\V1\SpeechClient;
use Google\Cloud\Speech\V1\RecognitionAudio;
use Google\Cloud\Speech\V1\RecognitionConfig;
use Google\Cloud\Speech\V1\RecognitionConfig\AudioEncoding;

// Google Text-to-Speech
use Google\Cloud\TextToSpeech\V1\AudioConfig;
use Google\Cloud\TextToSpeech\V1\AudioEncoding as AudioEncodingText2Speech;
use Google\Cloud\TextToSpeech\V1\SynthesisInput;
use Google\Cloud\TextToSpeech\V1\TextToSpeechClient;
use Google\Cloud\TextToSpeech\V1\VoiceSelectionParams;

class Translate {


    public static function transAudio($audio = null, $fromLanguage = 'fr'){

        // $recognitionConfig = new RecognitionConfig();
        // // $recognitionConfig->setEncoding(AudioEncoding::FLAC);
        // // $recognitionConfig->setSampleRateHertz(44100);
        // $recognitionConfig->setLanguageCode('pt-BR');
        // $config = new RecognitionConfig();
        // $config->setConfig($recognitionConfig);

        // ffmpeg -i leo*

        // $audioResource = fopen(storage_path('storage_tmp/leo.mp3'), 'r');
        $audioResource = file_get_contents(storage_path('storage_tmp/leo.flac'));

        $audio = (new RecognitionAudio())
        ->setContent($audioResource);

        $config = (new RecognitionConfig())
        ->setEncoding(AudioEncoding::FLAC)
        ->setSampleRateHertz(44100)
        ->setAudioChannelCount(2)
        ->setLanguageCode($fromLanguage);

        $retorno = ['transcript'=>null, 'language_code'=> null, 'serviceBy'=>'Google'];

        $speechClient = new SpeechClient();

        try {
            $response = $speechClient->recognize($config, $audio);
            $results = $response->getResults();
            // dd($results[0]);
            foreach ($results as $result) {
                $alternatives = $result->getAlternatives();
                $mostLikely = $alternatives[0];
                $retorno['transcript'] = $mostLikely->getTranscript();
                $confidence = $mostLikely->getConfidence();
                $retorno['language_code'] = $results[0]->getLanguageCode();
                break;
                // printf('Transcript: %s' . PHP_EOL, $transcript);
                // printf('Confidence: %s' . PHP_EOL, $confidence);
            }
        } catch(GoogleException $e){
            dd($e);
        } finally {
            $speechClient->close();
        }

        return $retorno;
    }

}
