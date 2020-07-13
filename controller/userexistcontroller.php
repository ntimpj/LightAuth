<?php

require_once('model/Email.php');
require_once('basepostcontroller.php');


class UserExistController extends BasePostController{
  /*
  Created by Mads Peter Jensen
  Handles requests to see if user exist
  */
  function __construct(){
    parent::__construct();
  }

  function Execute(){
    try{

      $jsonData = $this->getData();

      if(!isset($jsonData->email)){
        $this->setResponse(false, 400, 'Email is mandatory and must be provided');
        $this->setError(true);
        exit;
      }

        $email = new Email();
        $email->setEmail($jsonData->email);
    
        $returndata = array(); 
        $returndata['email'] = $email->getEmail();
        $returndata['emailverified'] = $email->getEmailVerified();
        $returndata['emailexists'] = $email->getEmailExists();
      
        $msg;
        if($email->getEmailExists()){
          $msg = $email->getEmailVerified() ? 'User is existing and is verified':'User is existing but is not verified';
        }else{
          $msg = 'User is non existing';
        }

        $this->setResponse(true, 200, $msg);
        $this->getResponseObject()->setData($returndata);

    }catch(EmailException $ex){
      $this->setResponse(false, 400, $ex->getMessage());
      $this->setHasError(true);
    }catch(Exception $ex){
      $this->setResponse(false, 500, 'Server error occured');
      $this->setHasError(true);
    }
}
}