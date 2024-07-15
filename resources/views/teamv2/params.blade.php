@extends('teamv2.layout.layout')

@section('content')

<!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1><i class="nav-icon fas fa-sliders-h"></i> Paramètres</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="/dashboard">Home</a></li>
              <li class="breadcrumb-item active">Paramètres</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">

      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
            <!-- Default box -->
            <div class="card">
              <form method="POST" action="{{ route('team.save_params') }}" class="">
                        @csrf
              <div class="card-body">
                <?php 
                $f_class = '';
                // dd( $errors->first('call_interval_log') );
                if( $validated = session()->get('was_validated') ) {
                    $f_class = " was-validated";
                }
                ?>
                <div class="form-group">
                    <x-input-label for="commission_to_bb" :value="__('Commission à Beamble (en pourcentage)')" />
                    
                    <?php $class_este = $errors->first('commission_to_bb')!="" ? 'is-invalid' : ''; ?>
                    <x-text-input id="commission_to_bb" class="form-control {{ $class_este }}" type="text" name="commission_to_bb" :value="old_or_db('commission_to_bb', $fromdb)" required placeholder="0.00" min="0" step="0.01" />
                    <small class="form-text text-muted">Le client paie pour appeler et une commission à un télétransportation est de X pour cent</small>
                    <x-input-error :messages="$errors->get('commission_to_bb')" class="mt-2" />
                </div>


              </div>
              <!-- /.card-body -->
              <div class="card-footer">
              <div class="flex items-center">
                <x-primary-button class="btn-lg">
                    {{ __('Sauvegarder') }}
                </x-primary-button>
              </div>
              <!-- /.card-footer-->
              </form>
            </div>
            <!-- /.card -->
          </div>
        </div>
      </div>
    </section>

@endsection

@section('footer_scripts')
<script>
$(function () {
  
});
</script>

@endsection