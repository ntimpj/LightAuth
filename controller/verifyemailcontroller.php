<?php

require_once('model/Email.php');
require_once('basegetcontroller.php');

class VerifyEmailController extends BaseGetController{
  /*
  Created by Mads Peter Jensen
  Handles set verification of email
  */
  function __construct(){
    parent::__construct();
  }

  function Execute(){
    try{
      if(!array_key_exists('hash', $_GET) ){
        $this->setResponse(false, 400, 'hash key does not exist');
        $this->setHasError(true);
        exit;
      }
      
      if(!array_key_exists('email', $_GET) ){
        $this->setResponse(false, 400, 'email key does not exist');
        $this->setHasError(true);
        exit;
      }
      
      $email = new Email();
      $email->setEmail($_GET['email']);
    
      if(!$email->getEmailExists()){
        $this->setResponse(false, 400, 'email doesnot exists in db');
        $this->setHasError(true);
        exit;
      }
      
      $email->VerifyEmail($_GET['hash']);
    
      if(!$email->getEmailVerified()){
        $this->setResponse(false, 400, 'email not verified');
        $this->setHasError(true);
        exit;
      }
      
      $this->setResponse(false, 200, 'email verified, your ready to login');
    }
    catch(EmailException $ex){
      $this->setResponse(false, 400, $ex->getMessage());
      $this->setHasError(true);
    }
    catch(Exception $ex){
      $this->setResponse(false, 500, 'Server error occured');
      $this->setHasError(true);
    }
  }

}

  
