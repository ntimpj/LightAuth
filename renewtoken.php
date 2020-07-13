<?php

require_once('controller/renewtokencontroller.php');


$myController = new RenewTokenController();

if($myController->getHasError()){
  $myController->getResponseObject()->send();
  exit;
}
$myController->Execute();
$myController->getResponseObject()->send();

