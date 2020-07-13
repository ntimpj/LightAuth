<?php

require_once('model/User.php');
require_once('basepostcontroller.php');


class UserController extends BasePostController{
  /*
  Created by Mads Peter Jensen
  Handles requests that create new user 
  */
  function __construct(){
    parent::__construct();
  }

  function Execute(){
    try{

      $jsonData = $this->getData();
      
      $user = new User();
      $user->setEmail($jsonData->email);
      $user->setFirstname($jsonData->firstname);
      $user->setLastname($jsonData->lastname);
      $user->setPassword($jsonData->password); 
      $user->setPhone($jsonData->phone);
      $user->setBirthdate($jsonData->birthdate);

      $user->save();
  
      $returndata = array();
      $returndata['email'] = $user->getEmail();
      $returndata['emailvarified'] = $user->getEmailVerified();
      $returndata['phone'] = $user->getPhone();
      $returndata['phoneverified'] = $user->getPhoneVerified();
      $returndata['firstname'] = $user->getFirstname();
      $returndata['lastname'] = $user->getLastname();
      $returndata['birthdate'] = $user->getBirthdate();
  
      $this->setResponse(true, 200, "User is created");
      $this->getResponseObject()->setData($returndata);

    }catch(UserException $ex){
      $this->setResponse(false, 400, $ex->getMessage());
      $this->setHasError(true);
    }catch(Exception $ex){
      $this->setResponse(false, 500, 'Server error occured');
      $this->setHasError(true);
    }
  }
}