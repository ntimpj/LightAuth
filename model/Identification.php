<?php

require_once('lib/db.php');
require_once('lib/Common.php');
require_once('lib/Settings.php');

class IdentificationException extends Exception { }


class Identification{
/*
  Created by Mads Peter Jensen
  Class for handling Identification requests  
*/
    private $writeDB;
    private $readDB;

    private $m_loginOK;
    private $m_token;
    private $m_renewtoken;
    private $m_ID;
    private $m_email; 

    function __construct(){
        try{
            $this->writeDB = DB::connectWriteDB();
            $this->readDB = DB::connectReadDB();
        }
        catch(PDOException $ex){
            error_log("DB connection error"); 
            throw new IdentificationException("DB connection error");
        }

        //initialize local variables
        $this->m_loginOK = false;
        $this->m_token = "";
        $this->m_renewtoken = "";
        $this->m_ID = "";
        $this->m_email = "";
    }

    public function getLoginOK(){
        return $this->m_loginOK;
    }

    public function getToken(){
        return $this->m_token;
    }

    public function getRenewToken(){
        return $this->m_renewtoken;
    }

    public function getEmail(){
        return $this->m_email;
    }

    public function getID(){
        return $this->m_ID;
    }

    public function LoginEmailAndPassword($email, $password){
        
        try{
            $query = $this->writeDB->prepare('SELECT ID, email, emailverified, password FROM Users WHERE email = :email');
            $query->bindParam(':email', $email, PDO::PARAM_STR);
            $query->execute();

            $rowcount = $query->rowCount();
            
            if($rowcount !== 1){
                throw new IdentificationException("Account not found");
            }

            $row = $query->fetch(PDO::FETCH_ASSOC);

            if ($row["emailverified"]!==1){
                throw new IdentificationException('Account not verified');
            }

            if(!password_verify($password, $row['password'])){
                throw new IdentificationException('Password not correct'); 
            }   
            
            $this->m_ID = $row['ID'];
            $this->m_email = $row['email'];
            $this->m_loginOK = true;
            
        }
        catch(PDOException $ex){
            error_log("Error access user table"); 
            throw new IdentificationException("Error access useer table");
        }
    }

    public function ValidateTokenWithEmail($email, $token){
        try{
            $query = $this->writeDB->prepare('SELECT ID, email password FROM Users WHERE email = :email');
            $query->bindParam(':email', $email, PDO::PARAM_STR);
            $query->execute();

            $rowcount = $query->rowCount();
            
            if($rowcount !== 1){
                throw new IdentificationException("Account not found");
            }

            $row = $query->fetch(PDO::FETCH_ASSOC);
            
            $this->ValidateToken($row['ID'],$token);
        }
        catch(PDOException $ex){
            error_log("Error access user table"); 
            throw new IdentificationException("Error access useer table");
        }
    }

    public function ValidateToken($ID, $token){
        try{
            $this->m_ID = $ID;
            $this->m_email = $this->GetEmailFrom($ID);

            $query = $this->writeDB->prepare('SELECT token, tokendate FROM Login WHERE userID = :userID');
            $query->bindParam(':userID', $ID, PDO::PARAM_STR);
            $query->execute();

            $rowcount = $query->rowCount();
            
            if($rowcount !== 1){
                throw new IdentificationException("Error login not found");
            }
            $row = $query->fetch(PDO::FETCH_ASSOC);

            if($row['token'] !== $token ){
                throw new IdentificationException("Error login token not correct");
            }

            $expire = new DateTime($row['tokendate']);
            $expire->add(new DateInterval('PT' . 60 . 'M'));
            $now = new DateTime();

            if($now > $expire){
                throw new IdentificationException("Error login token expired");
            }
            $this->m_loginOK = true;

        }
        catch(PDOException $ex){
            error_log("Error access login table"); 
            throw new IdentificationException("Error access login table");
        }
    }

    public function RenewTokenWithEmail($email, $token){
        try{
            $query = $this->writeDB->prepare('SELECT id, email password FROM Users WHERE email = :email');
            $query->bindParam(':email', $email, PDO::PARAM_STR);
            $query->execute();

            $rowcount = $query->rowCount();
            
            if($rowcount !== 1){
                throw new IdentificationException("Account not found");
            }

            $row = $query->fetch(PDO::FETCH_ASSOC);
            
            $this->Renewtoken($row['id'],$token);
        }
        catch(PDOException $ex){
            error_log("Error access user table"); 
            throw new IdentificationException("Error access useer table");
        }
    }


    public function Renewtoken($ID, $renewtoken){
        $this->m_ID = $ID;
        $this->m_email = $this->GetEmailFrom($ID);

        try{
            $query = $this->writeDB->prepare('SELECT renewtoken, tokendate FROM Login WHERE userID = :userID');
            $query->bindParam(':userID', $ID, PDO::PARAM_STR);
            $query->execute();
    
            $rowcount = $query->rowCount();
                
            if($rowcount !== 1){
                throw new IdentificationException("Error login not found");
            }
            $row = $query->fetch(PDO::FETCH_ASSOC);

            if($row['renewtoken'] !== $renewtoken ){
                throw new IdentificationException("Error renewtoken not correct");
            }
            
            $expire = new DateTime($row['tokendate']);
            $expire->add(new DateInterval('P30D'));
            $now = new DateTime();

            if($now > $expire){
                throw new IdentificationException("Error renewtoken not valid");
            }

            $this->m_loginOK = true; 

        }catch(PDOException $ex){
            error_log("Error access login table"); 
            throw new IdentificationException("Error access login table");
        }

    } 

    public function CreateTokens(){

        if($this->m_loginOK){
            $token = Common::GetGuid();
            $renewtoken = Common::GetGuid();
            $loginExists = $this->LoginExists();

            if($loginExists){
                $this->UpdateLogin($token, $renewtoken);
            }else{
                $this->CreateLogin($token, $renewtoken);
            }
            $this->m_token = $token;
            $this->m_renewtoken = $renewtoken;
        }
    }

    private function CreateLogin($token, $renewtoken){
        try{
            $query = $this->writeDB->prepare('INSERT INTO Login (token, renewtoken, userID) VALUES (:token, :renewtoken, :userID)');
            $query->bindParam(':token', $token, PDO::PARAM_STR);
            $query->bindParam(':renewtoken', $renewtoken, PDO::PARAM_STR);
            $query->bindParam(':userID', $this->m_ID, PDO::PARAM_STR);
            $query->execute();

            $rowcount = $query->rowCount();
            
            if($rowcount !== 1){
                throw new IdentificationException("Tokens not created");
            }
        }catch(PDOException $ex){
            error_log("Error create tokens"); 
            throw new IdentificationException("Error create tokens");
        }
    }

    private function UpdateLogin($token, $renewtoken){
        try{
            $now = new DateTime();
            $snow = $now->format('Y-m-d H:i:s');
            $query = $this->writeDB->prepare('UPDATE Login SET token = :token, renewtoken = :renewtoken, tokendate = :tokendate WHERE userID = :userID');
            $query->bindParam(':token', $token, PDO::PARAM_STR);
            $query->bindParam(':renewtoken', $renewtoken, PDO::PARAM_STR);
            $query->bindParam(':tokendate', $snow, PDO::PARAM_STR);
            $query->bindParam(':userID', $this->m_ID, PDO::PARAM_STR);
            $query->execute();

            $rowcount = $query->rowCount();
        
            if($rowcount !== 1){
                throw new IdentificationException("Tokens not created");
            }

        }catch(PDOException $ex){
            error_log("Error update tokens"); 
            throw new IdentificationException("Error update tokens");
        }
    }

    private function GetEmailFrom($ID){
        try{
            $query = $this->writeDB->prepare('SELECT email FROM Users WHERE ID = :userID');
            $query->bindParam(':userID', $this->m_ID, PDO::PARAM_STR);
            $query->execute();

            $rowcount = $query->rowCount();
            
            if($rowcount !== 1){
                throw new IdentificationException("Error user account not found");
            }
            $row = $query->fetch(PDO::FETCH_ASSOC);
            return $row['email'];

        }catch(PDOException $ex){
            error_log("Error access user table"); 
            throw new IdentificationException("Error access user table");
        }
    }
        
    private function LoginExists(){
        try{
            $query = $this->writeDB->prepare('SELECT userID FROM Login WHERE userID = :userID');
            $query->bindParam(':userID', $this->m_ID, PDO::PARAM_STR);
            $query->execute();
    
            $rowcount = $query->rowCount();
                
            if($rowcount > 0){
                return true;
            }
   
            return false;
        }catch(PDOException $ex){
            error_log("Error access token table"); 
            throw new IdentificationException("Error access token table");
        }
    }
}

