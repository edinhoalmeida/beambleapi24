<?php

namespace App\Http\Controllers\Team;

use App\Http\Controllers\Controller;
use App\Models\Webview\Contacts;
use Illuminate\Http\Request;

use League\CommonMark\CommonMarkConverter;

class WvcontactsController extends TeamController
{

    public $view_data = [
        'icon'=>'fa-comments',
        'page_title'=>'Site contact'
    ];

    public function index()
    {
        $this->view_data['contacts'] = Contacts::whereNotNull('message')->get();
        return view('teamv2.contacts.index',$this->view_data);
    }

    public function newusers()
    {

        $this->view_data['icon'] = 'fa-user-circle';
        $this->view_data['page_title'] = 'Site register';

        $this->view_data['contacts'] = Contacts::whereNotNull('profile_type')->get();
        return view('teamv2.contacts.newusers',$this->view_data);
    }

}
