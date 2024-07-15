@extends('teamv2.layout.layout-login')

@section('content')

<div class="login-box">
  <div class="login-logo">
    <a href="/" class="bc bf"><img class="logo-login" src="{{ asset('/team/imgs/app_icon.png') }}" > <b>{{ __('team.backoffice_title') }}</b></a>
  </div>
  <!-- /.login-logo -->
  <div class="card">
    <div class="card-body login-card-body">
      <p class="login-box-msg">{{__('team.signin_text')}}</p>

      <form method="POST" id="quickForm" action="{{ route('team.authenticate') }}">
        @csrf
        <div class="input-group mb-3">

        <x-text-input id="InputEmail" class="form-control" type="email" name="email" :value="old('email')" autofocus placeholder="{{__('team.email')}}" :errorclass="class_if_error($errors, 'email')"  />
        <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-envelope"></span>
            </div>
          </div>

        <x-input-error :messages="$errors->get('email')" class="mt-2" id="InputEmail-error" />

        </div>
        <div class="input-group mb-3">
            <x-text-input id="password" class=""
                            type="password"
                            name="password"
                            current-password
                            placeholder="{{__('team.password')}}" :errorclass="class_if_error($errors, 'password')"  />
            <div class="input-group-append">
              <div class="input-group-text">
                <span class="fas fa-lock"></span>
              </div>
            </div>
          <x-input-error :messages="$errors->get('password')" class="mt-2" id="password-error" />
        </div>
        <div class="row">
          <div class="col-8">
            <div class="icheck-primary">
              <input type="checkbox" id="remember">
              <label for="remember">
              {{__('team.remember_me')}}
              </label>
            </div>
          </div>
          <!-- /.col -->
          <div class="col-4">
            <button type="submit" class="btn btn-primary btn-block">{{__('team.signin')}}</button>
          </div>
          <!-- /.col -->
        </div>
      </form>

      {{-- <p class="mb-1">
        <a href="forgot-password.html">{{__('team.forgotmypassword')}}</a>
      </p> --}}
    </div>
    <!-- /.login-card-body -->
  </div>
</div>
<!-- /.login-box -->

@endsection


@section('footer_scripts')

<script>
$(function () {
  $.validator.setDefaults({
    submitHandler: function(form) {
     $(form).ajaxSubmit();
    }
  });
  $('#quickForm').validate({
    rules: {
      email: {
        required: true,
        email: true,
      },
      password: {
        required: true,
        minlength: 5
      }
    },
    messages: {
      email: {
        required: "Veuillez saisir une adresse e-mail",
        email: "S'il vous plaît, mettez une adresse email valide"
      },
      password: {
        required: "Veuillez fournir un mot de passe",
        minlength: "Votre mot de passe doit comporter au moins 5 caractères"
      }
    },
    errorElement: 'span',
    errorPlacement: function (error, element) {
      error.addClass('invalid-feedback');
      element.closest('.form-group').append(error);
    },
    highlight: function (element, errorClass, validClass) {
      $(element).addClass('is-invalid');
    },
    unhighlight: function (element, errorClass, validClass) {
      $(element).removeClass('is-invalid');
    }
  });
});
</script>

@endsection
