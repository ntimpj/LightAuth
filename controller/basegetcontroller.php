<?php

require_once('basecontroller.php');

abstract class BaseGetController extends BaseController{
/*
  Created by Mads Peter Jensen
  Base structure for get requests  
*/
    function __construct(){
        parent::__construct();
    }

    public function checkRequestMethode(){
        if($_SERVER['REQUEST_METHOD']!== 'GET'){
            $this->setResponse(false, 405, 'Request method not allowed');
            $this->setHasError(true);
            return false;
        }
        return true;
    }

    public function checkContentType(){
        return true;
    }

    public function checkData(){
        return true;
    }


}