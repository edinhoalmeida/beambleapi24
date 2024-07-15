<?php 
namespace Payment;

use Illuminate\Support\Facades\Facade as BaseFacade;

class PaymentFacade extends BaseFacade {

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'payment'; }

}