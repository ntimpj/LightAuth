<?php

require_once('model/Response.php');

abstract class BaseController{
/*
  Created by Mads Peter Jensen
  Base structure for controllers
*/
    function __construct(){
        $this->m_haserror=false;
        $this->checkRequestMethode();
        $this->checkContentType();
        $this->checkData();
    }

    private $m_response;
    private $m_data;
    private $m_haserror;

    public function getHasError(){
        return $this->m_haserror;
    }

    public function setHasError($haserror){
        $this->m_haserror = $haserror;
    }

    public function getData(){
        return $this->m_data;
    }

    public function setData($data){
        $this->m_data = $data;
    }

    public function getResponseObject(){
        return $this->m_response;
    }

    public abstract function checkRequestMethode();
    
    public abstract function checkContentType();

    public abstract function checkData();

    public abstract function Execute();

    protected function setResponse($success, $httpStatusCode, $message){
        $this->m_response = new Response();
        $this->m_response->setSuccess($success);
        $this->m_response->setHttpStatusCode($httpStatusCode);
        $this->m_response->addMessage($message);
        return $this->m_response;
    }
}