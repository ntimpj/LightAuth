<?php

class Response{
/*
  Created by Mads Peter Jensen
  HTTP Response for sending  
*/
    private $m_success;
    private $m_httpStatusCode;
    private $m_messages = array();
    private $m_data;
    private $m_toCache = false;
    private $m_responseData = array();

    public function setSuccess($success){
        $this->m_success = $success;
    }

    public function setHttpStatusCode($httpStatusCode){
        $this->m_httpStatusCode = $httpStatusCode;
    }

    public function addMessage($message){
        $this->m_messages[]=$message;
    }

    public function setData($data){
        $this->m_data = $data;
    }

    public function setToCache($toCache){
        $this->m_toCache = $toCache;
    }

    public function send(){
        header('Content-type: application/json;charset=utf-8');

        if($this->m_toCache === true){
            header('Cache-control: max-age=60');
        }
        else{
            header('Cache-control: no-cache, no-store');
        }

        if($this->m_success !== false && $this->m_success !== true && !is_numeric($this->m_httpStatusCode)){
            http_response_code(500);
            $this->m_responseData['statusCode'] = 500;
            $this->m_responseData['success'] = false;
            $this->addMessage('Error creating response'); 
            $this->m_responseData['mesages'] = $this->m_messages;
        }
        else{
            http_response_code($this->m_httpStatusCode);
            $this->m_responseData['statusCode'] = $this->m_httpStatusCode;
            $this->m_responseData['success'] = $this->m_success;
            $this->m_responseData['messages'] = $this->m_messages;
            $this->m_responseData['data'] = $this->m_data;
        }

        echo json_encode($this->m_responseData);
    }

    public function sendWithValues($success, $httpStatusCode, $message){
        $this->setSuccess($success);
        $this->setHttpStatusCode($httpStatusCode);
        $this->addMessage($message);
        $this->send();
    }



}