<?php 
namespace Shipping;

use Illuminate\Support\Facades\Facade as BaseFacade;

class ShippingFacade extends BaseFacade {

    protected static function getFacadeAccessor() { return 'shipping'; }

}