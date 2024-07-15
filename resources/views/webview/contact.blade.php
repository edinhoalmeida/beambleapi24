@extends('webview.layout.layout')

@section('content')
<div class="container">
    <img src="{{ asset('team/imgs/app_icon.png') }}" class="wv-logo">
    <h2>Contact</h2>
    <div>
        <form id="feedback-form" method="POST" action="{{ route('webview.contact-save') }}">
            @csrf
            
            <input type="hidden" name="ui" value="webview">

            <div class="form-group">
                <x-input-label for="name" :value="__('Name')" />
                <x-text-input id="name" class="form-control " type="text" name="name" :value="old('name')" required autofocus />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <div class="form-group">
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input id="email" class="form-control" type="email" name="email" :value="old('email')" required autofocus />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>
            
            <!-- Password -->
            <div class="form-group">
                <x-input-label for="message" :value="__('Message')" />
                <x-textarea-input id="message" class="form-control"
                                :value="old('message')"
                                :value_default="old('message')"
                                name="message"
                                required />

                <x-input-error :messages="$errors->get('message')" class="mt-2" />
            </div>

            <!-- Inclusão de valor na wallet -->
            <div class="flex items-center justify-end mt-4">
                <x-primary-button class="ml-3 btn-info">
                    {{ __('team.envoyer') }}
                </x-primary-button>
            </div>
        </form>
    </div>
</div>
@endsection