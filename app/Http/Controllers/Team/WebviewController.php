<?php
namespace App\Http\Controllers\Team;

use App\Mail\ContactBeamer;
use App\Mail\ContactClient;
use App\Models\Robots\VideosBatch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

use Mail;
use Illuminate\Support\Facades\Validator;

use App\Http\Controllers\ControllerPublic;

use Payment;
use \Payment\Payment as PaymentClass;

use Illuminate\Support\Facades\DB;

use League\CommonMark\CommonMarkConverter;

use App\Models\Webview\Contacts;

class WebviewController extends ControllerPublic
{

    function __construct()
    {
        // session is not shared between app sanctum and webview request
        $this->middleware('user_type:isPublic');
    }

    public function onboardingtest(Request $request){
        $user_id = $request->get('user_id');
        $status = Payment::checkAccountUserById($user_id);

        if($status===PaymentClass::STATUS_USER_AND_ACCOUNT_ENABLED){
            // ops, the connect is working
            return redirect()->route('webview.onboarding-success');
        } elseif($status===PaymentClass::STATUS_USER_NOT_FOUND) {
            // ops, the user not found
            return back()
                ->withInput()
                ->withErrors(['user_id'=>'user not found']);
        } elseif($status===PaymentClass::STATUS_USER_ACCOUNT_PENDING){
            // connect pending rediret to connection
            $return_url = route('webview.onboarding-test',['accounttk'=>PaymentClass::$account_token]);
            $refresh_url = route('webview.onboarding-token',['accounttk'=>PaymentClass::$account_token]);
            $account_link_url = Payment::getAccountLink(
                $user_id,
                PaymentClass::$account_id_pending,
                $refresh_url,
                $return_url
            );
            return redirect()->away($account_link_url);
        }
        return view('webview.error');
    }

    public function onboarding(Request $request){

        $user_id = $request->get('user_id');
        $status = Payment::checkAccountUserById($user_id);
        if($status===PaymentClass::STATUS_USER_AND_ACCOUNT_ENABLED){
            // ops, the connect is working
            return redirect()->route('webview.onboarding-success');
        } elseif($status===PaymentClass::STATUS_USER_NOT_FOUND) {
            return view('webview.error');
        } elseif($status===PaymentClass::STATUS_USER_ACCOUNT_PENDING){
            // connect pending rediret to connection
            $return_url = route('webview.onboarding-test',['accounttk'=>PaymentClass::$account_token]);
            $refresh_url = route('webview.onboarding-token',['accounttk'=>PaymentClass::$account_token]);
            $account_link_url = Payment::getAccountLink(
                $user_id,
                PaymentClass::$account_id_pending,
                $refresh_url,
                $return_url
            );
            return redirect()->away($account_link_url);
        }
        return view('webview.error');
    }

    public function onboardingByToken(Request $request, $accounttk){
        $user_id = Payment::getUserIdByAccountToken($accounttk);

        if($user_id==PaymentClass::STATUS_USER_NOT_FOUND){
            return view('webview.error');
        }

        $status = Payment::checkAccountUserById($user_id);
        if($status===PaymentClass::STATUS_USER_AND_ACCOUNT_ENABLED){
            // ops, the connect is working
            return redirect()->route('webview.onboarding-success');
        } elseif($status===PaymentClass::STATUS_USER_NOT_FOUND) {
            // ops, the user not found
            return view('webview.error');
        } elseif($status===PaymentClass::STATUS_USER_ACCOUNT_PENDING){
            // connect pending rediret to connection
            $return_url = route('webview.onboarding-test',['accounttk'=>PaymentClass::$account_token]);
            $refresh_url = route('webview.onboarding-token',['accounttk'=>PaymentClass::$account_token]);
            $account_link_url = Payment::getAccountLink(
                $user_id,
                PaymentClass::$account_id_pending,
                $refresh_url,
                $return_url
            );
            return redirect()->away($account_link_url);
        }
        return view('webview.error');
    }

    public function onboardingTestByToken(Request $request, $accounttk){

        $user_id = Payment::getUserIdByAccountToken($accounttk);

        if($user_id==PaymentClass::STATUS_USER_NOT_FOUND){
            return view('webview.error');
        }

        $account_id = Payment::getAccountIdByToken($accounttk);
        if($account_id==PaymentClass::STATUS_USER_NOT_FOUND){
            return view('webview.error');
        }
        $status = Payment::accountStatus($user_id, $account_id);
        if($status === true){
            // ops, the connect is completed
            return redirect()->route('webview.onboarding-success');
        }
        return view('webview.onboarding-pending', ['user_id'=>$user_id]);
    }

    public function onboardingSuccess(){
        return view('webview.onboarding-success');
    }

    public function test(){
        $data = [
            'users' => DB::select('select id, email, name, surname from users')
        ];
        return view('webview.test', $data);
    }

    public function page(Request $request, $page_slug){
        $page = DB::table('wv_texts')->where('slug',$page_slug)->whereNull('deleted_at')->first();
        if(empty($page)){
            if( true || $request->ajax() ){
                $response = [
                    'success' => false,
                    'data'    => [],
                    'message' => 'Page not found',
                ];
                return response()->json($response, 200);
            }
            return view('webview.error');
        }
        $converter = new CommonMarkConverter();

        $dados = [
            'title'=>$page->title,
            'body'=>$converter->convertToHtml($page->body_txt)
        ];

        if( true || $request->ajax() ){
            $response = [
                'success' => true,
                'data'    => $dados,
                'message' => 'OK',
            ];
            return response()->json($response, 200);
        }

        return view('webview.pages', $dados);
    }

    public function contact(){
        return view('webview.contact');
    }




/**
  * @OA\Post(
    *  path="/contact-save",
    *  tags={"site beamble.com"},
    *  description="Forms on site beamble.com",
  *     @OA\RequestBody(
  *          @OA\MediaType(
  *              mediaType="multipart/form-data",
  *              @OA\Schema(
  *                     @OA\Property(
  *                      property="email",
  *                      type="string",
  *                     ),
  *                     @OA\Property(
  *                      property="message",
  *                      type="string",
  *                     ),
    *                     @OA\Property(
  *                      property="name",
  *                      type="string",
  *                     ),
    *                     @OA\Property(
  *                      property="surname",
  *                      type="string",
  *                     ),
    *                     @OA\Property(
  *                      property="profile_type",
  *                      type="string",
  *                     ),
  *               ),
  *          )
  *     ),
    *  @OA\Response(
    *     response="200",
    * description="success",
    *     @OA\JsonContent(
    *        @OA\Property(
    *          property="success",
    *          type="boolean",
    *       )
    *     )
    *  ),
    *  @OA\Response(
    *     response="400",
    * description="error",
    *     @OA\JsonContent(
    *        @OA\Property(
    *          property="success",
    *          type="boolean",
    *       ),
    *       @OA\Property(
    *          property="errors",
    *          type="array",
    *          @OA\Items(),
    *       )
    *     )
    *  )
    *)
*/
    public function contactSave(Request $request){

        $data1 = $data2 = $request->all();

        $validator1 = validator($data1, [
                'email' => 'required|email',
                'message' => 'required'
            ]);

        if($validator1->passes()){
            $input = $request->only(['email', 'message']);
            Contacts::create($input);
            $response = [
                'success' => true,
                'errors'    => [],
                'message' => 'OK',
            ];
            return response()->json($response, 200);
        }

        $validator2 = validator($data2, [
            'name' => 'required:email',
            'surname' => 'required:name',
            'email' => 'required|email',
            'profile_type' => 'required'
            ]);

        if($validator2->passes()){
            $input = $request->only(['name', 'surname', 'email', 'profile_type']);
            $profile_type = $input['profile_type'];
            $user = Contacts::create($input);

            if($profile_type=='client'){
                $mailJob = (new ContactClient($user))->onQueue('sistema');
                $m = Mail::to($user)
                    ->bcc('sistema@soaba.com.br')
                    ->later(1, $mailJob);
            } else {
                $mailJob2 = (new ContactBeamer($user))->onQueue('sistema');
                $m2 = Mail::to($user)
                    ->bcc('sistema@soaba.com.br')
                    ->later(1, $mailJob2);
            }
            $response = [
                'success' => true,
                'errors'    => [],
                'message' => 'OK',
            ];
            return response()->json($response, 200);
        }

        $response = [
            'success' => false,
            'errors'    => "all fields are required",
            'message' => 'ERROR',
        ];
        return response()->json($response, 400);

        // return view('webview.contact-success');
    }

    public function video_batch_debug(Request $request){

        $source = $request->input('source');
        $message = $request->input('message');

        if(!is_string($message)){
            $message = json_encode($message);
        }
        VideosBatch::create(['source'=>$source,'message'=>$message]);
        return response()->json(['success'=>true], 200);

    }
}
