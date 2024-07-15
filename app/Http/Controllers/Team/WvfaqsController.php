<?php

namespace App\Http\Controllers\Team;

use App\Http\Controllers\Controller;
use App\Models\Webview\Faqs;
use Illuminate\Http\Request;

use League\CommonMark\CommonMarkConverter;

class WvfaqsController extends TeamController
{

    public $view_data = [
        'icon'=>'fa-question-circle',
        'page_title'=>'Faq (GPT)'
    ];

    public function index()
    {
        $this->view_data['faqs'] = Faqs::latest()->paginate(20);
        return view('teamv2.faqs.index',$this->view_data)
            ->with('i', (request()->input('page', 1) - 1) * 20);
    }

    public function create()
    {
        return view('teamv2.faqs.create',$this->view_data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'body_txt' => 'required',
        ]);
    
        Faqs::create($request->all());
     
        return redirect()->route('wvfaqs.index')->with('flash_success','Faq créé avec succès.');
    }

    public function show(Faqs $faq)
    {
        $converter = new CommonMarkConverter();
        $this->view_data['faq'] = $faq;
        $this->view_data['faq_html'] = $converter->convertToHtml($faq->body_txt);
        return view('teamv2.faqs.show', $this->view_data);
    }

    public function edit(Faqs $faq)
    {
        $this->view_data['faq'] = $faq;
        return view('teamv2.faqs.edit', $this->view_data);
    }

    public function update(Request $request, Faqs $faq)
    {
        $request->validate([
            'slug' => 'required',
            'title' => 'required',
        ]);
    
        $faq->delete();

        $faq->create($request->except('id'));
    
        return redirect()->route('wvfaqs.index')
                        ->with('flash_success','FAQ mis à jour avec succès');
    }

    public function destroy(Faqs $faq)
    {
        $faq->delete();
        return redirect()->route('wvfaqs.index')
                        ->with('flash_success','FAQ supprimé avec succès');
    }
}
