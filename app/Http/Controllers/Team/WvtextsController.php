<?php

namespace App\Http\Controllers\Team;

use App\Http\Controllers\Controller;
use App\Models\Webview\Texts;
use Illuminate\Http\Request;

use League\CommonMark\CommonMarkConverter;

class WvtextsController extends TeamController
{

    public $view_data = [
        'icon'=>'fa-edit',
        'page_title'=>'Des textes'
    ];

    public function index()
    {
        $this->view_data['texts'] = Texts::latest()->paginate(5);
        return view('teamv2.texts.index',$this->view_data)
            ->with('i', (request()->input('page', 1) - 1) * 5);
    }

    public function create()
    {
        return view('teamv2.texts.create',$this->view_data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'slug' => 'required|unique:wv_texts',
            'title' => 'required',
        ]);
    
        Texts::create($request->all());
     
        return redirect()->route('wvtexts.index')->with('flash_success','Texte créé avec succès.');
    }

    public function show(Texts $text)
    {
        $converter = new CommonMarkConverter();
        $this->view_data['text'] = $text;
        $this->view_data['text_html'] = $converter->convertToHtml($text->body_txt);
        return view('teamv2.texts.show', $this->view_data);
    }

    public function edit(Texts $text)
    {
        $this->view_data['text'] = $text;
        return view('teamv2.texts.edit', $this->view_data);
    }

    public function update(Request $request, Texts $text)
    {
        $request->validate([
            'slug' => 'required',
            'title' => 'required',
        ]);
    
        $text->myDelete();

        $text->create($request->except('id'));
    
        return redirect()->route('wvtexts.index')
                        ->with('flash_success','Texte mis à jour avec succès');
    }

    public function destroy(Texts $text)
    {
        $text->myDelete();
        return redirect()->route('wvtexts.index')
                        ->with('flash_success','Texte supprimé avec succès');
    }
}
