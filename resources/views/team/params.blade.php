@extends('team.layout')


@section('content')



<div class="starter-template starter-template-small text-to-left">
    <?php 
    $f_class = '';
    // dd( $errors->first('call_interval_log') );
    if( $validated = session()->get('was_validated') ) {
        $f_class = " was-validated";
    }
    ?>

    <form method="POST" action="{{ route('team.save_params') }}" class="">
        @csrf
    
        <div class="form-group">
            <x-input-label for="call_interval_log" :value="__('Interval do log during call (seconds)')" />
            <?php 

            // dd($errors->first('commission_to_bb'));
            $class_este = $errors->first('call_interval_log')!="" ? 'is-invalid' : ''; ?>
            <x-text-input id="call_interval_log" class="form-control {{ $class_este }}" type="number" name="call_interval_log" :value="old_or_db('call_interval_log', $fromdb)" required />
            <small class="form-text text-muted">Log the start time, end time and running logs in interval of this value.</small>
            <x-input-error :messages="$errors->get('call_interval_log')" class="mt-2" />
        </div>

        <div class="form-group">
            <x-input-label for="call_interval_online" :value="__('Interval do log during mode online (seconds)')" />
            <?php 
            $class_este = $errors->first('call_interval_online')!="" ? 'is-invalid' : ''; ?>
            <x-text-input id="call_interval_online" class="form-control {{ $class_este }}" type="number" name="call_interval_online" :value="old_or_db('call_interval_online', $fromdb)" required />
            <small class="form-text text-muted">Log the start time, end time and running logs in interval of this value.</small>
            <x-input-error :messages="$errors->get('call_interval_online')" class="mt-2" />
        </div>

        <div class="form-group">
            <x-input-label for="commission_to_bb" :value="__('Comission to Beamble (in percents)')" />
            
            <?php $class_este = $errors->first('commission_to_bb')!="" ? 'is-invalid' : ''; ?>
            <x-text-input id="commission_to_bb" class="form-control {{ $class_este }}" type="text" name="commission_to_bb" :value="old_or_db('commission_to_bb', $fromdb)" required placeholder="0.00" min="0" step="0.01" />
            <small class="form-text text-muted">Client pays to call and a comission to a beamer is a X percent</small>
            <x-input-error :messages="$errors->get('commission_to_bb')" class="mt-2" />
        </div>
        

        <div class="form-group">
            <x-input-label for="teste" :value="__('Variável de teste')" />
            
            <?php $class_este = $errors->first('teste')!="" ? 'is-invalid' : ''; ?>
            <x-text-input id="teste" class="form-control {{ $class_este }}" type="text" name="teste" :value="old_or_db('teste', $fromdb)" required placeholder="0.00" min="0" step="0.01" />
            <small class="form-text text-muted">Teste alguma coisa</small>
            <x-input-error :messages="$errors->get('teste')" class="mt-2" />
        </div>


        <div class="flex items-center justify-end mt-4">

            <x-primary-button class="ml-3 btn-info">
                {{ __('Save') }}
            </x-primary-button>
        </div>
    </form>

</div>

@endsection