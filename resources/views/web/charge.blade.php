@extends('web.layout')

@section('content')
 
<div class="starter-template">
        
        @if (Session::has('success'))
            <div class="alert alert-success text-center">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
                <p>{{ Session::get('success') }}</p>
            </div>
        @endif

<div class="row">
<div class="col-md-6 col-md-offset-3">
<div class="panel-body">


        <h1>{{$title}}</h1>
        <form 
              role="form" 
              action="{{ $form_target }}" 
              method="post" 
              class="require-validation"
              data-cc-on-file="false"
              data-stripe-publishable-key="{{ env('STRIPE_KEY') }}"
              id="payment-form">
          
          @csrf

          <div class='form-group'>
              <div class=' required'>
                  <label>Name on Card</label> <input
                      class='form-control' size='' id="name_on_card"  name="name_on_card" type='text' required>
              </div>
          </div>

          <div class='form-group'>
              <div class='required'>
                  <label>{{__('beam.charge_cc_number')}}</label> <input
                      autocomplete='off' class='form-control card-number' size=''
                      type='text' id="number_card"  name="number_card">
              </div>
          </div>

          <div class='form-group row'>
              <div class='col-xs-12 col-md-4 form-group cvc required'>
                  <label >CVC</label> <input autocomplete='off'
                      class='form-control card-cvc' placeholder='ex. 311' size='4'
                      type='text' id="number_cvc"  name="number_cvc">
              </div>
              <div class='col-xs-12 col-md-4 form-group expiration required'>
                  <label>Expiration Month</label> <input
                      class='form-control card-expiry-month' placeholder='MM' size='2'
                      type='text' id="number_exp_month" name="number_exp_month">
              </div>
              <div class='col-xs-12 col-md-4 form-group expiration required'>
                  <label >Expiration Year</label> <input
                      class='form-control card-expiry-year' placeholder='YYYY' size='4'
                      type='text' id="number_exp_year" name="number_exp_year">
              </div>
          </div>

          <div class='form-group'>
              <div class='col-md-12 error form-group hide'>
                  <div class='alert-danger alert'>Please correct the errors and try
                      again.</div>
              </div>
          </div>
          <label>Amount</label>
          <div class="input-group mb-4">
            <input type="number"  type="text" inputmode="numeric" pattern="\d*" class="form-control"  id="card_amount" name="card_amount" aria-label="Amount" min="1" max="50" step="1">
            <div class="input-group-append">
              <span class="input-group-text">€</span>
            </div>
          </div>


          <div class="row">
              <div class="col-xs-12">
                  <button class="btn btn-primary btn-lg btn-block" type="submit">Charge now</button>
              </div>
          </div>
              
      </form>

</div>
</div>
</div>

      
</div>

@endsection

  

@push('after-scripts')
<script type="text/javascript" src="https://js.stripe.com/v2/"></script>  
<script type="text/javascript">
$(function() {
  
    /*------------------------------------------
    --------------------------------------------
    Stripe Payment Code
    --------------------------------------------
    --------------------------------------------*/
    
    var $form = $(".require-validation");
     
    $('form.require-validation').bind('submit', function(e) {
        var $form = $(".require-validation"),
        inputSelector = ['input[type=email]', 'input[type=password]',
                         'input[type=text]', 'input[type=file]',
                         'textarea'].join(', '),
        $inputs = $form.find('.required').find(inputSelector),
        $errorMessage = $form.find('div.error'),
        valid = true;
        $errorMessage.addClass('hide');
    
        $('.has-error').removeClass('has-error');
        $inputs.each(function(i, el) {
          var $input = $(el);
          if ($input.val() === '') {
            $input.parent().addClass('has-error');
            $errorMessage.removeClass('hide');
            e.preventDefault();
          }
        });
     
        if (!$form.data('cc-on-file')) {
          e.preventDefault();
          Stripe.setPublishableKey($form.data('stripe-publishable-key'));
          Stripe.createToken({
            number: $('.card-number').val(),
            cvc: $('.card-cvc').val(),
            exp_month: $('.card-expiry-month').val(),
            exp_year: $('.card-expiry-year').val()
          }, stripeResponseHandler);
        }
    
    });
      
    /*------------------------------------------
    --------------------------------------------
    Stripe Response Handler
    --------------------------------------------
    --------------------------------------------*/
    function stripeResponseHandler(status, response) {
        if (response.error) {
            $('.error')
                .removeClass('hide')
                .find('.alert')
                .text(response.error.message);
        } else {
            /* token contains id, last4, and card type */
            var token = response['id'];
                 
            $form.find('input[type=text]').empty();
            $form.append("<input type='hidden' name='stripeToken' value='" + token + "'/>");
            $form.get(0).submit();
        }
    }
     
});
</script>

@endpush