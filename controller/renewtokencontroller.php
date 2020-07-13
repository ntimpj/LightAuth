<?php

require_once('model/Identification.php');
require_once('basepostcontroller.php');


class RenewTokenController extends BasePostController{
  /*
  Created by Mads Peter Jensen
  Handles requests for renew logon token 
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
      
      if(!isset($jsonData->renewtoken)){
        $this->setResponse(false, 400, 'renewtoken key does not exist');
        $this->setHasError(true);
        exit;
      }

      $identification = new Identification(); 

      if(isset($jsonData->id)){
        $identification->Renewtoken($jsonData->id, $jsonData->renewtoken);
      } else if($jsonData->email){
        $identification->RenewTokenWithEmail($jsonData->email, $jsonData->renewtoken);
      }
  
      if(!$identification->getLoginOK()){
        $this->setResponse(false, 400, 'Authentication not valid');
        $this->setHasError(true);
        exit;
      }

      $identification->CreateTokens();

      $returndata = array();
      $returndata['email'] = $jsonData->email;
      $returndata['ID'] = $identification->getID();
      $returndata['token'] = $identification->getToken();
      $returndata['renewtoken'] = $identification->getRenewToken();

      $this->setResponse(true, 200, 'Authentication tokens');
      $this->getResponseObject()->setData($returndata);

    }catch(IdentificationException $ex){
      $this->setResponse(false, 400, $ex->getMessage());
      $this->setHasError(true);
    }catch(Exception $ex){
      $this->setResponse(false, 500, 'Server error occured');
      $this->setHasError(true);
    }
  }
}