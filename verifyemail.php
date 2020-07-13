<?php

require_once('controller/verifyemailcontroller.php');


$myController = new VerifyEmailController();

if($myController->getHasError()){
  $myController->getResponseObject()->send();
  exit;
}
$myController->Execute();
$myController->getResponseObject()->send();
