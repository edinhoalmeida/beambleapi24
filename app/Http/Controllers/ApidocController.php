<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;

class ApidocController extends Controller
{
    public function index() {

        $api = env('APP_URL', 'beamble20.local').'';

        $setores = config('apidoc');

        return view('apidoc.index', ['setores'=>$setores, 'api'=>$api]);
    }
}
