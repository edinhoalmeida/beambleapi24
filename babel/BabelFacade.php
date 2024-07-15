<?php 
namespace Babel;

use Illuminate\Support\Facades\Facade as BaseFacade;

class BabelFacade extends BaseFacade {

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'babel'; }

}