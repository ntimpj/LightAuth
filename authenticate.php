<?php

require_once('controller/authenticatecontroller.php');


$myController = new AuthenticateController();

if($myController->getHasError()){
  $myController->getResponseObject()->send();
  exit;
}
$myController->Execute();
$myController->getResponseObject()->send();

