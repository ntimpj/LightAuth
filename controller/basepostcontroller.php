<?php

require_once('basecontroller.php');

abstract class BasePostController extends BaseController{
/*
  Created by Mads Peter Jensen
  Base structure for post requests  
*/
    function __construct(){
        parent::__construct();
    }

    public function checkRequestMethode(){
        if($_SERVER['REQUEST_METHOD']!== 'POST'){
            $this->setResponse(false, 405, 'Request method not allowed');
            $this->setHasError(true);
            return false;
        }
        return true;
    }

    public function checkContentType(){
        if($_SERVER['CONTENT_TYPE'] !== 'application/json'){
            $this->setResponse(false, 400, 'Content type headder not set to JSON');
            $this->setHasError(true);
            return false;
        }
        return true;
    }

    public function checkData(){
        try{
            $rawPostData = file_get_contents('php://input');
        
            if(!$data = json_decode($rawPostData)){
                $this->setResponse(false, 400, 'Data not wellformed json');
                $this->setHasError(true);
                return false;
            }
      
            if(!$data ){
                $this->setResponse(false, 400, 'No data recieved');
                $this->setHasError(true);
                return false;
            }
            $this->setData($data); 
            return $data;

        }catch(Exception $Ex){
            $this->setResponse(false, 400, 'Error recieving data');
            $this->setHasError(true);
            return false;
        }
    }


}