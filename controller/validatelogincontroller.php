<?php

require_once('model/Identification.php');
require_once('basepostcontroller.php');

class ValidateLoginController extends BasePostController{
/*
  Created by Mads Peter Jensen
  Handles 
*/
  function __construct(){
    parent::__construct();
  }

  function Execute(){
    try{

      $jsonData = $this->getData();

      if(!(isset($jsonData->id)||isset($jsonData->email))){
        $this->setResponse(false, 400, 'id or email key does not exist');
        $this->setHasError(true);
        exit;
      }
      
      if(!isset($jsonData->token)){
        $this->setResponse(false, 400, 'token key does not exist');
        $this->setHasError(true);
        exit;
      }

      $identification = new Identification(); 

      if(isset($jsonData->id)){
        $identification->ValidateToken($jsonData->id, $jsonData->token);
      }else if(isset($jsonData->email)){
        $identification->ValidateTokenWithEmail($jsonData->email, $jsonData->token);
      }
    
      if(!$identification->getLoginOK()){
        $this->setResponse(false, 400, 'authentication not valid');
        $this->setHasError(true);
        exit;
      }
    
      $returndata = array();
      $returndata['email'] = $identification->getEmail();
      $returndata['id'] = $identification->getID();
      
      $this->setResponse(true, 200, 'authentication valid');
      $this->getResponseObject()->setData($returndata);
    
    }
    catch(IdentificationException $ex){
      $this->setResponse(false, 400, $ex->getMessage());
      $this->setHasError(true);
    }
    catch(Exception $ex){
      $this->setResponse(false, 500, 'Server error occured');
      $this->setHasError(true);
    }
  }
}