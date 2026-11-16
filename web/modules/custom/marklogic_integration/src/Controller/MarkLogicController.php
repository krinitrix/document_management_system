<?php

namespace Drupal/marklogic_integration/Controller;
use Drupal/Core/Controller/ControllerBase;

class MarkLogicIntegration extends ControllerBase{

    public function status(){
        return [
            '#type'=>'markup',
            '#markup'=>'MarkLogic Integration module is working !!! '
        ];
    }

}


?>