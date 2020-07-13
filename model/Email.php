<?php

require_once('lib/db.php');

class EmailException extends Exception { }


class Email{
/*
  Created by Mads Peter Jensen
  Class for handling email requests 
*/
    private $writeDB;
    private $readDB;

    private $m_email;
    private $m_emailverified;
    private $m_emailexists;

    function __construct(){
        try{
            $this->writeDB = DB::connectWriteDB();
            $this->readDB = DB::connectReadDB();
        }
        catch(PDOException $ex){
            error_log("DB connection error"); 
            throw new EmailException("DB connection error");
        }
        $this->m_emailverified=false;
    }


    public function getEmail(){
        return $this->m_email;
    }

    public function setEmail($email){
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new EmailException('Mail not well formed'); 
        }
        
        try{
            $query = $this->writeDB->prepare('SELECT email, emailverified FROM Users WHERE email = :email');
            $query->bindParam(':email', $email, PDO::PARAM_STR);
            $query->execute();

            $rowcount = $query->rowCount();
            
            if($rowcount === 1){
                $this->m_emailexists = true;  
                $row = $query->fetch(PDO::FETCH_ASSOC);
                $this->m_email= $row['email'];
                if ($row["emailverified"]===1){
                    $this->m_emailverified = true; 
                }
            }else{
                $this->m_emailexists = false;
                $this->m_emailverified = false;
            }

        }
        catch(PDOException $ex){
            error_log("Error getting email from DB"); 
            throw new EmailException("Error getting email from DB");
        }
        
    }

    public function VerifyEmail($guid){

        try{
            
            
            $row = $this->GetVerification();

            if($row['verificationID'] !== $guid )
            {
                throw new EmailException("Verification code not correct");    
            }

            //give the user 15 minutes to verify email
            $expire = new DateTime($row['issuedate']);
            $expire->add(new DateInterval('PT' . 15 . 'M'));
            $now = new DateTime();

            if(!$expire > $now )
            {
                throw new EmailException("Verification is too old please reregistrer");    
            }

            $this->SetVerification();

            $this->DeleteEmailVarification();
            $this->m_emailverified = true;
        }
        catch(PDOException $ex){
            error_log("Error getting email from DB"); 
            throw new EmailException("Error getting email from DB");
        }
    }
    
    private function SetVerification(){
        $query = $this->writeDB->prepare("UPDATE Users SET emailverified = 1 WHERE email = :email");
        $query->bindParam(':email', $this->m_email, PDO::PARAM_STR);
        $query->execute();

        $rowcount = $query->rowCount();
        
        if($rowcount !== 1){
            throw new EmailException("Email not verified, an error occured");  
            return false;
        }
        return true;
    }

    private function GetVerification(){

        $query = $this->writeDB->prepare('SELECT email, verificationID, issuedate FROM EmailVerification WHERE email = :email ORDER BY issuedate');
        $query->bindParam(':email', $this->m_email, PDO::PARAM_STR);
        $query->execute();

        $rowcount = $query->rowCount();
        
        if($rowcount !== 1){
            throw new EmailException("Verification not found");    
        }
        
        return $query->fetch(PDO::FETCH_ASSOC);

    }


    private function DeleteEmailVarification(){
        $query = $this->writeDB->prepare("DELETE FROM EmailVerification WHERE email=:email");
        $query->bindParam(':email', $this->m_email, PDO::PARAM_STR);

        $query->execute();
    }


    public function getEmailVerified(){
        return $this->m_emailverified;
    }

    public function getEmailExists(){
        return $this->m_emailexists;
    }

}