<?php

require_once('controller/userexistcontroller.php');


$myController = new UserExistController();

if($myController->getHasError()){
  $myController->getResponseObject()->send();
  exit;
}
$myController->Execute();
$myController->getResponseObject()->send();

