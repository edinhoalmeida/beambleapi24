<?php
namespace Babel\Drivers;

use Babel\Contract\Contract as BabelContract;


use Babel\BabelRessource;


define('GOOGLE_CLOUD_PROJECT', 'braided-verve-374914');

$json_google_api = config_path('braided-verve-374914-c48203055ff0.json');

define('GOOGLE_APPLICATION_CREDENTIALS', $json_google_api);
putenv("GOOGLE_APPLICATION_CREDENTIALS=$json_google_api");

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

class Google implements BabelContract {

    public static function text_to_text($text, $targetLanguage = 'fr', $fromLanguage = 'auto'){
        // GOOGLE_CLOUD_PROJECT braided-verve-374914
        // GOOGLE_APPLICATION_CREDENTIALS - Path to JSON file
        // $translate = new TranslateClient([
        //     'key' => 'your_key'
        // ]);
        // dd(GOOGLE_APPLICATION_CREDENTIALS);
        $translate = new TranslationServiceClient();
        $optional = ['mimeType'=>'text/html'];
        if($fromLanguage!='auto'){
            $optional['sourceLanguageCode'] = $fromLanguage;
        }
        $result = $translate->translateText(
            [$text],
            $targetLanguage,
            TranslationServiceClient::locationName(GOOGLE_CLOUD_PROJECT, 'global'),
            $optional
        );
        $translated = $text;

        $babel_resource = new BabelRessource;
        $babel_resource->from_text = $text;
        $babel_resource->target_lang = $targetLanguage;
        foreach ($result->getTranslations() as $key => $translation) {
            $translated = $translation->getTranslatedText();
            $babel_resource->from_lang = $translation->getDetectedLanguageCode();
            break;
        }

        $babel_resource->target_text = $translated;
        $babel_resource->service_version = 'GoogleV3';
        return $babel_resource;
    }

    public static function text_to_speech($fromText, $languageCode){

        $textToSpeechClient = new TextToSpeechClient();

        $input = new SynthesisInput();
        $input->setText($fromText);
        $voice = new VoiceSelectionParams();
        $voice->setLanguageCode($languageCode);
        $audioConfig = new AudioConfig();
        $audioConfig->setAudioEncoding(AudioEncodingText2Speech::MP3);

        $resp = $textToSpeechClient->synthesizeSpeech($input, $voice, $audioConfig);

        $single_name = generateRandomString() . '.mp3';

        $storage_name = 'storage_tmp/' . $single_name;

        $audioResource = storage_path($storage_name);

        file_put_contents($audioResource, $resp->getAudioContent());

        $babel_resource = new BabelRessource;
        $babel_resource->from_text = $fromText;
        $babel_resource->from_lang = $languageCode;
        $babel_resource->target_lang = null;
        $babel_resource->target_text = null;
        $babel_resource->file_name = $single_name;
        $babel_resource->service_version = 'GoogleTextToSpeechV1';
        return (array) $babel_resource;
    } 

    public static function speech_to_text($path_audio = null, $fromLanguage = 'fr'){

        // $recognitionConfig = new RecognitionConfig();
        // // $recognitionConfig->setEncoding(AudioEncoding::FLAC);
        // // $recognitionConfig->setSampleRateHertz(44100);
        // $recognitionConfig->setLanguageCode('pt-BR');
        // $config = new RecognitionConfig();
        // $config->setConfig($recognitionConfig);

        // ffmpeg -i leo*

        // $audioResource = fopen(storage_path('storage_tmp/leo.mp3'), 'r');
        $audioResource = file_get_contents($path_audio);

        $audio = (new RecognitionAudio())
        ->setContent($audioResource);

        /**
         * The encoding of the audio data sent in the request.
         * All encodings support only 1 channel (mono) audio, unless the
         * `audio_channel_count` and `enable_separate_recognition_per_channel` fields
         * are set.
         * For best results, the audio source should be captured and transmitted using
         * a lossless encoding (`FLAC` or `LINEAR16`). The accuracy of the speech
         * recognition can be reduced if lossy codecs are used to capture or transmit
         * audio, particularly if background noise is present. Lossy codecs include
         * `MULAW`, `AMR`, `AMR_WB`, `OGG_OPUS`, `SPEEX_WITH_HEADER_BYTE`, `MP3`,
         * and `WEBM_OPUS`.
         * The `FLAC` and `WAV` audio file formats include a header that describes the
         * included audio content. You can request recognition for `WAV` files that
         * contain either `LINEAR16` or `MULAW` encoded audio.
         * If you send `FLAC` or `WAV` audio file format in
         * your request, you do not need to specify an `AudioEncoding`; the audio
         * encoding format is determined from the file header. If you specify
         * an `AudioEncoding` when you send  send `FLAC` or `WAV` audio, the
         * encoding configuration must match the encoding described in the audio
         * header; otherwise the request returns an
         * [google.rpc.Code.INVALID_ARGUMENT][google.rpc.Code.INVALID_ARGUMENT] error code.
         *
         * Protobuf type <code>google.cloud.speech.v1.RecognitionConfig.AudioEncoding</code>
         */
        $config = (new RecognitionConfig())
         ->setEncoding(AudioEncoding::FLAC)
        // ->setSampleRateHertz(44100)
        ->setAudioChannelCount(2)
        ->setLanguageCode($fromLanguage);

        $retorno = ['transcript'=>null, 'language_code'=> null];

        $speechClient = new SpeechClient();

        try {
            $response = $speechClient->recognize($config, $audio);
            // $response = $speechClient->longRunningRecognize($config, $audio);

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

        $path_audio = explode(DIRECTORY_SEPARATOR, $path_audio);
        $single_name = end($path_audio);

        $babel_resource = new BabelRessource;
        $babel_resource->from_text = $retorno['transcript'];
        $babel_resource->from_lang = $retorno['language_code'];
        $babel_resource->target_lang = null;
        $babel_resource->target_text = null;
        $babel_resource->file_name = $single_name;
        $babel_resource->service_version = 'CloudSpeechV1p1beta1';
        return (array) $babel_resource;
    }

}
