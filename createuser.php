<?php

require_once('controller/usercontroller.php');


$myController = new UserController();

if($myController->getHasError()){
  $myController->getResponseObject()->send();
  exit;
}
$myController->Execute();
$myController->getResponseObject()->send();

