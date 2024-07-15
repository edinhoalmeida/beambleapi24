@extends('teamv2.layout.layout')

@section('content')

<!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1><i class="nav-icon fas fa-sliders-h"></i> Profile</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="/dashboard">Home</a></li>
              <li class="breadcrumb-item active">Profile</li>
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
              <form method="POST" action="{{ route('team.save_profile') }}" class="">
                        @csrf
              <div class="card-body">
                <?php 
                $f_class = '';
                if( $validated = session()->get('was_validated') ) {
                    $f_class = " was-validated";
                }
                ?>
                <div class="form-group">
                    <x-input-label for="password" :value="__('Password')" />
                    <?php $class_este = $errors->first('password')!="" ? 'is-invalid' : ''; ?>
                    <x-text-input id="password" class="form-control {{ $class_este }}" type="text" name="password" required />
                     <small class="form-text text-muted">With at least 8 characters.</small>
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>
                <div class="form-group">
                    <x-input-label for="c_password" :value="__('Repeat password')" />
                    <?php $class_este = $errors->first('c_password')!="" ? 'is-invalid' : ''; ?>
                    <x-text-input id="c_password" class="form-control {{ $class_este }}" type="text" name="c_password" required />
                    <x-input-error :messages="$errors->get('c_password')" class="mt-2" />
                </div>


              </div>
              <!-- /.card-body -->
              <div class="card-footer">
              <div class="flex items-center">
                <x-primary-button class="btn-lg">
                    {{ __('Change password') }}
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