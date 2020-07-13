<?php

require_once('model/Identification.php');
require_once('basepostcontroller.php');


class AuthenticateController extends BasePostController{
  /*
  Created by Mads Peter Jensen
  Handles requests logon  
  */
  function __construct(){
    parent::__construct();
  }

  function Execute(){
    try{
      $jsonData = $this->getData();

      if(!isset($jsonData->email)){
        $this->setResponse(false, 400, 'email key does not exist');
        $this->setHasError(true);
        exit;
      }
      
      if(!isset($jsonData->password)){
        $this->setResponse(false, 400, 'password key does not exist');
        $this->setHasError(true);
        exit;
      }

      $identification = new Identification();  
      $identification->LoginEmailAndPassword($jsonData->email, $jsonData->password);

      if(!$identification->getLoginOK()){
        $this->setResponse(false, 400, 'authentication failed');
        $this->setHasError(true);
        exit;
      }

      $identification->CreateTokens(); 

      $returndata = array();
      $returndata['email'] = $jsonData->email;
      $returndata['ID'] = $identification->getID();
      $returndata['token'] = $identification->getToken();
      $returndata['renewtoken'] = $identification->getRenewToken();

      $this->setResponse(true, 200, 'authentication tokens');
      $this->getResponseObject()->setData($returndata);
      
    }catch(IdentificationException $ex){
      $this->setResponse(true, 400, $ex->getMessage());
      $this->setHasError(true);
    }catch(Exception $ex){
      $this->setResponse(true, 500, 'Server error occured');
      $this->setHasError(true);
    }
  }
}