<?php
   
namespace App\Http\Controllers\API;
   
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;

use App\Libs\Translate;
use App\Models\VideocallAudio;
use App\Models\VideocallMessages;

use Validator;
use DB;

use Babel;
   
class LangController extends BaseController
{

    function __construct()
    {
        // $this->middleware('permission:user-list|user-create|user-edit|user-delete', ['only' => ['index','show']]);
        // $this->middleware('permission:user-create', ['only' => ['create','store']]);
        // $this->middleware('permission:user-edit', ['only' => ['edit','update']]);
        // $this->middleware('permission:user-delete', ['only' => ['destroy']]);
    }

    public function text2text(Request $request)
    {
        $userid = auth()->user()->id;
        $from_text = $request->get('from_text');
        $from_lang = $request->get('from_lang', 'auto');
        $call_id = $request->get('call_id');
        $target_lang = $request->get('target_lang', 'fr');
        if(!is_numeric($call_id)){
            $call = DB::table('videocalls')->where('meeting_id', $call_id)->first();
            if(empty($call)){
                return $this->sendError(__('beam.validation_error'));
            }
            $call_id = $call->id;
        } else {
            $call = DB::table('videocalls')->where('id', $call_id)->first();
        }

        $dados = [
            'videocall_id' => $call_id,
            'user_id' => $userid,
            'from_lang' => $from_lang,
            'target_lang' => $target_lang,
            'from_text'  => $from_text,
            'target_text'    => $from_text,
            'status' => 'new'
        ];
        
        if($from_lang==$target_lang){
            // rating
            $dados['service_version']='None';
        } else {

    // videocall_id, user_id
    // status, 
    // from_text, from_lang, 
    // target_text, target_lang
    // file_name
    // service_version

            $babel_resource = Babel::text_to_text($from_text, $target_lang, $from_lang);
            
            $dados['service_version']=  $babel_resource->service_version;
            $dados['from_lang']=  $babel_resource->from_lang;
            $dados['target_text']=  $babel_resource->target_text;
        }
        VideocallMessages::create($dados);

        return $this->sendResponse($dados, __('beam.language_translate_txt') );
    }

    public function text2speech(Request $request)
    {
        $userid = auth()->user()->id;

        $fromText = $request->get('from_text', 'Bonjour!');
        $fromLanguage = $request->get('from_lang', 'fr');
        $call_id = $request->get('call_id');
        if(!is_numeric($call_id)){
            $call = DB::table('videocalls')->where('meeting_id', $call_id)->first();
            if(empty($call)){
                return $this->sendError(__('beam.validation_error'));
            }
            $call_id = $call->id;
        } else {
            $call = DB::table('videocalls')->where('id', $call_id)->first();
        }

        // $targetLanguage = $request->get('targetLanguage', 'fr');
        $babel_resource = Babel::text_to_speech($fromText, $fromLanguage);

        $dados = [
            'videocall_id' => $call_id,
            'user_id' => $userid,
            'from_text'  => $fromText,
            'from_lang' => $fromLanguage,
            'target_lang' => null, // in text to audio don1t translate
            'target_text' => null, // in text to audio don1t translate
            'status' => 'new',
            'file_name' => $babel_resource['file_name'],
            'service_version' => $babel_resource['service_version']
        ];
        VideocallMessages::create($dados);
        $dados['file_url_to_download'] = route('url_audio', $babel_resource['file_name']);

        $dados1 = [
            'call_id' => $call_id,
            'from_text' => $fromText,
            'file_name'  => $babel_resource['file_name'],
            'side'  => $call->client_id==$userid?'client':'beamer',
            'language_code'=>$fromLanguage
        ];
        VideocallAudio::create($dados1);

        return $this->sendResponse($dados, __('beam.language_txt2audio') );
    }

    public function speech2text(Request $request)
    {
        $userid = auth()->user()->id;


        $input = $request->all();

        $validator = Validator::make($input, [
            'from_lang' => 'required',
            'call_id' => 'required',
            'audio_file' => 'required|mimes:mp3,mp4,mpeg,mpga,m4a,wav,webm,flac|max:20971520',
        ]);

        if($validator->fails()){
            return $this->sendError(__('beam.validation_error'), $validator->errors());
        }
        
        $call_id = $request->get('call_id');
        if(!is_numeric($call_id)){
            $call = DB::table('videocalls')->where('meeting_id', $call_id)->first();
            if(empty($call)){
                return $this->sendError(__('beam.validation_error'));
            }
            $call_id = $call->id;
        } else {
            $call = DB::table('videocalls')->where('id', $call_id)->first();
        }

        $fromLanguage = $request->get('from_lang', 'fr');

        $audio_file = $request->file('audio_file');

        $st_audio_file = generateRandomString() . '.' . $audio_file->extension();

        $storage_name = 'storage_tmp/' . $st_audio_file;

        $destinationPath = storage_path('storage_tmp');
        $audio_path = $audio_file->move($destinationPath, $st_audio_file);

        $babel_resource = Babel::speech_to_text($audio_path, $fromLanguage);

        $dados = [
            'videocall_id' => $call_id,
            'user_id' => $userid,
            'from_text'  => $babel_resource['from_text'],
            'from_lang' => $fromLanguage,
            'target_lang' => null, // in text to audio don1t translate
            'target_text' => null, // in text to audio don1t translate
            'status' => 'new',
            'file_name' => $babel_resource['file_name'],
            'service_version' => $babel_resource['service_version']
        ];
        VideocallMessages::create($dados);
        $dados['file_url_to_download'] = route('url_audio', $babel_resource['file_name']);

        $dados1 = [
            'call_id' => $call_id,
            'from_text' => $babel_resource['from_text'],
            'file_name'  => $babel_resource['file_name'],
            'side'  => $call->client_id==$userid?'client':'beamer',
            'language_code'=>$fromLanguage
        ];
        VideocallAudio::create($dados1);

        $dados1 = [
            'call_id' => $call_id,
            'from_text' => null,
            'file_name'  => $babel_resource['file_name'],
            'side'  => $call->client_id==$userid?'client':'beamer',
            'language_code'=>$fromLanguage
        ];
        VideocallAudio::create($dados1);
        return $this->sendResponse($dados, __('beam.language_audio2txt') );
    }

}
