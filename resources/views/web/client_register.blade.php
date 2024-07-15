@extends('web.layout')


@section('content')

    <div class="starter-template starter-template-small text-to-left">
    	
    	<h1>Register as Client</h1>

        <form method="POST" action="{{ route('api.register_all') }}">
            @csrf
            <input type="hidden" name="type" value="client">
            
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

            @include('web.partials.address_complete', ['address_type'=>'shipping', 'show_after_scripts'=>true])
            
            <div class="form-group">
                <x-input-label for="phone" :value="__('Phone')" />
                <x-text-input id="phone" class="form-control " type="text" name="phone" :value="old('phone')" required autofocus />
                <x-input-error :messages="$errors->get('phone')" class="mt-2" />
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


