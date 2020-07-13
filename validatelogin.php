<?php

require_once('controller/validatelogincontroller.php');

$myController = new ValidateLoginController();

if($myController->getHasError()){
  $myController->getResponseObject()->send();
  exit;
}
$myController->Execute();
$myController->getResponseObject()->send();


