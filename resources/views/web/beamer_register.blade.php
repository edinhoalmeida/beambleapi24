@extends('web.layout')

<?php 
/*
Nome
Sobrenome
Masculino/feminino
email
Localização para serviço (input endereço)
Endereço pessoal, cidade, país
Telefone
Número de identidade
Foto
Conexão stripe ID
Idioma utilizado para comunicação na experiência


Retorno do Google Maps

https://developers.google.com/maps/documentation/geocoding/requests-geocoding#Types
*/
?>

@section('content')

<div class="starter-template starter-template-small text-to-left">
	
	<h1>Register as Beamer</h1>

    <form method="POST" class="registerform" action="{{ route('api.register_beamer') }}">
        @csrf
        <input type="hidden" name="type" value="beamer">
        <input type="hidden" name="ui" value="web">

        <div class="form-group">
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="form-control " type="text" name="name" :value="old('name')" required autofocus />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div class="form-group">
            <x-input-label for="surname" :value="__('Surname')" />
            <x-text-input id="surname" class="form-control " type="text" name="surname" :value="old('surname')" required autofocus />
            <x-input-error :messages="$errors->get('surname')" class="mt-2" />
        </div>

        <?php  
        $gender_list = ['m'=>'Feminine', 'f'=>'Masculine'];
        ?>
        <div class="form-group">
            <x-input-label for="gender" :value="__('Gender')" />
            <x-select-input id="gender" class="form-control " name="gender" :value="old('gender')" :select_options="$gender_list" />
            <x-input-error :messages="$errors->get('gender')" class="mt-2" />
        </div>

        @include('web.partials.address_complete', ['address_type'=>'beaming', 'show_after_scripts'=>true])

        <div class="form-group">
            <x-input-label for="contact_address" :value="__('Contact Address')" />
            <x-text-input id="contact_address" class="form-control"
                            type="text"
                            name="contact_address"
                            required />
            <x-input-error :messages="$errors->get('contact_address')" class="mt-2" />
        </div>
        <div class="form-group">
            <x-input-label for="contact_city" :value="__('City')" />
            <x-text-input id="contact_city" class="form-control"
                            type="text"
                            name="contact_city"
                            required />
            <x-input-error :messages="$errors->get('contact_city')" class="mt-2" />
        </div>
        <div class="form-group">
            <x-input-label for="contact_country" :value="__('Country')" />
            <x-text-input id="contact_country" class="form-control"
                            type="text"
                            name="contact_country"
                            required />
            <x-input-error :messages="$errors->get('contact_country')" class="mt-2" />
        </div>

        <div class="form-group">
            <x-input-label for="phone" :value="__('Phone')" />
            <x-text-input id="phone" class="form-control " type="text" name="phone" :value="old('phone')" required autofocus />
            <x-input-error :messages="$errors->get('phone')" class="mt-2" />
        </div>

        <div class="form-group">
            <x-input-label for="doc_id" :value="__('Document')" />
            <x-text-input id="doc_id" class="form-control " type="text" name="doc_id" :value="old('doc_id')" required autofocus />
            <x-input-error :messages="$errors->get('doc_id')" class="mt-2" />
        </div>

        <div class="form-group">
            <x-input-label for="my_language" :value="__('My language')" />
            <x-select-input id="my_language" class="form-control " name="my_language" :value="old('my_language')" :select_options="$languages_to_form" />
            <x-input-error :messages="$errors->get('my_language')" class="mt-2" />
        </div>

        <div class="form-group">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="form-control" type="email" name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>
        
        <!-- Password -->
        <div class="form-group">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="form-control"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Inclusão de valor na wallet -->
        <div class="flex items-center justify-end mt-4">
            <x-primary-button class="ml-3 btn-info">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>


</div>

@endsection


@push('after-scripts')
    <script src="{{ asset('assets/js/registerform.js') }}"></script>
@endpush
