<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\User;

class Teste extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'teste:api {urlport}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Testa as requisições da api';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $urlport = $this->argument('urlport');

        $urlport = preg_replace('@http(s)?://@','',$urlport);

        if(empty($urlport)){
            $urlport = '127.0.0.1:80';
        }
        $this->info('Url: ' . $urlport);
        $this->newLine(3);

        // raiz da api

        try {
            $response = Http::get($urlport);
            if($response->ok()){
                $this->info('Raiz ok ');
            } else {
                $this->error('Raiz não acessível');
            }
        } catch (ConnectException $e) {
            $this->error('Fora do Ar.....');
        } catch (Throwable $t) {
            $this->error('Fora do Ar.....');
        }


        \DB::table('users')->where('email', 'edinhoalmeida@gmail.com')->delete();

        // registro com erro
        // /api/register
        $response = Http::asForm()->post('http://' . $urlport . '/api/register', [
            'name' => 'edinho',
            'email' => 'edinhoalmeida@gmail.com',
            'password' => '123456',
            'c_password' => '123455',
        ]);
        if($response->status()==404){
            $this->info('Registro com erro esperado: Erro ok');
            $this->info( $response->body() );
        } elseif($response->ok()){
            $this->warn('Registro com erro esperado vei com SUCESSO!');
        } else {
            $this->error('Falha:' .  $response->status());
            $this->warn( $response->body() );
        }

        // registro com email
        // /api/register
        $response = Http::asForm()->post('http://' . $urlport . '/api/register', [
            'name' => 'edinho',
            'email' => 'edinhoalmeida@gmail.com',
            'password' => '123456',
            'c_password' => '123456',
        ]);

        $user_id = $token = null;

        if($response->status()==404){
            $this->warn('Registro com erro esperado: Erro ok');
            $this->warn( $response->body() );
        } elseif($response->ok()){
            $this->info('Registro inserido com SUCESSO!');
            $user_id = $response->json()['data']['user_id'];
            $token = $response->json()['data']['token'];
            $this->info('user_id :' . $user_id );
            $this->info('token :' . $token );
        } else {
            $this->warn('Falha:' .  $response->status());
            $this->warn( $response->body() );
        }


        // send email esqueci a senha
        $response = Http::asForm()->post('http://' . $urlport . '/api/register/passwordreset', [
            'email' => 'edinhoalmeida@gmail.com'
        ]);

        if($response->ok()){
            $this->info('Email enviado com SUCESSO!');
            $this->info('email : edinhoalmeida@gmail.com');
        } else {
            $this->error('Falha:' .  $response->body());
        }


        return 0;
    }
}
