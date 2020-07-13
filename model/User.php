<?php

require_once('lib/db.php');
require_once('lib/Common.php');
require_once('lib/Settings.php');

class UserException extends Exception { }


class User{
    /*
    Created by Mads Peter Jensen
    Model for user operations 
    */

    private $writeDB;
    private $readDB;

    private $m_userid;
    private $m_email;
    private $m_emailverified;
    private $m_emailexist;
    private $m_phone;
    private $m_phoneverified;
    private $m_password;
    private $m_hashed_password;
    private $m_firstname;
    private $m_lastname;
    private $m_birthdate;

    function __construct(){
        try{
            $this->writeDB = DB::connectWriteDB();
            $this->readDB = DB::connectReadDB();
        }
        catch(PDOException $ex){
            error_log("DB connection error"); 
            throw new UserException("DB connection error");
        }
        $this->m_emailverified=false;
        $this->m_phoneverified=false;
        $this->m_userid=-1;
    }

    public function getUserid(){
        return $this->m_userid;
    }

    public function getEmail(){
        return $this->m_email;
    }

    public function setEmail($email){
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new UserException('Mail not well formed'); 
        }
        
        $retval = $this->checkEmailExist($email);

        if($this->m_userid === -1){
            $this->m_emailexist = $retval['emailexists'];
            $this->m_emailverified = $retval['emailverified'];
            $this->m_userid = $retval['id'];
        }else{
            if($retval['emailexists']&& $this->m_userid !== $retval['id']){
                throw new UserException('Cannot update mail, it exists in database'); 
            }
            if(strcasecmp($email, $this->m_email)!==0)
            {
                $this->m_emailverified = false;
            }
        }
        $this->m_email= $email;
    }

    public function setEmailverified($emailverified){
        $this->m_emailverified= $emailverified;
    }

    public function getEmailVerified(){
        return $this->m_emailverified;
    }

    public function getEmailExist(){
        return $this->m_emailexist;
    }

    public function getPhone(){
        return $this->m_phone;
    }

    public function setPhone($phone){
        if(strlen($phone) < 8 || strlen($phone) > 32){
            throw new UserException('Phone number must be 8 or more characters and less than 32 characters'); 
        }
        $this->m_phone = $phone;
        $this->m_phoneverified=false;
    }

    public function getPhoneVerified(){
        return $this->m_phoneverified;
    }

    public function setPassword($password){
        if(strlen($password) < 8 || strlen($password) > 255){
            throw new UserException('Password must be 8 or more characters and less than 255 characters'); 
        }
        $this->m_password = $password;
        $this->m_hashed_password = password_hash($password, PASSWORD_DEFAULT);
    }

    public function setFirstname($firstname){
        if(strlen($firstname)<2 || strlen($firstname)>255){
            throw new UserException('Firstname must be 2 or more characters and less than 255 characters'); 
        }
        $this->m_firstname = $firstname; 
    }

    public function getFirstname(){
        return $this->m_firstname;
    }

    public function setLastname($lastname){
        if(strlen($lastname)<2 || strlen($lastname)>255){
            throw new UserException('Lastname must be 2 or more characters and less than 255 characters'); 
        }
        $this->m_lastname = $lastname;
    }
    public function getLastname(){
        return $this->m_lastname;
    }

    public function setBirthdate($birthdate){

        if (DateTime::createFromFormat('Y-m-d', $birthdate) === FALSE) {
            throw new UserException('Birthdate is not valid'); 
        }
        
        $this->m_birthdate=date('Y-m-d H:i:s',strtotime($birthdate));
    }

    public function getBirthdate(){
        $bdate = new DateTime($this->m_birthdate);
        return $bdate->format('Y-m-d');
    }

    public function read($email){
        try{
        $query = $this->writeDB->prepare("SELECT id, email, emailverified
            , phone, phoneverified, password, firstname, lastname, birthdate FROM Users WHERE email=:email"); 
        $query->bindParam(':email', $this->m_email, PDO::PARAM_STR);
        }catch(PDOException $ex){
            throw new UserException('Error reading user from database');
        }
        $rowcount = $query->rowCount();
            
        if($rowcount !== 1){
            throw new UserException('User none existing');
        }
        $row = $query->fetch(PDO::FETCH_ASSOC);

        $this->m_id = $row['id'];
        $this->m_email = $row['email'];
        $this->m_emailverified = $row['emailverified'];
        $this->m_emailexist = true;
        $this->m_phone = $row['phone'];
        $this->m_phoneverified = $row['phoneverified'];
        $this->m_firstname = $row['firstname'];
        $this->m_lastname = $row['lastname'];
        $this->m_birthdate = $this->setBirthdate($row['birthdate']);
    }

    public function save(){
        
            if($this->m_emailexist === true){
                $this->Update();

            }else{
                $this->Insert();    
            }
            if(!$this->m_emailverified)
            {
               $this->CreateEmailVerification();           
            }      
    }

    private function Delete(){
        try{
            $query = $this->writeDB->prepare("DELETE FROM Users WHERE id = :id");
            $query->bindParam(':id', $this->m_userid, PDO::PARAM_INT);
                
            $query->execute();
    
            $rowcount = $query->rowCount();
                
            if($rowcount !== 1){
                throw new UsersException('User not deleted');
            }
            }catch(PDOException $ex){
                throw new UsersException('Error deleting user');
            }
    }

    private function Update(){
        try{
        $query = $this->writeDB->prepare("UPDATE Users SET 
            email = :email
            , emailverified = :emailverified
            , phone = :phone
            , phoneverified = :phoneverified
            , password = :password
            , firstname = :firstname, lastname = :lastname
            , birthdate = :birthdate WHERE id = :id");
        $query->bindParam(':id', $this->m_userid, PDO::PARAM_INT);
        $query->bindParam(':email', $this->m_email, PDO::PARAM_STR);
        $query->bindParam(':emailverified', $this->m_emailverified, PDO::PARAM_BOOL);
        $query->bindParam(':phone', $this->m_phone, PDO::PARAM_STR);
        $query->bindParam(':phoneverified', $this->m_phoneverified, PDO::PARAM_BOOL);
        $query->bindParam(':password', $this->m_hashed_password, PDO::PARAM_STR);
        $query->bindParam(':firstname', $this->m_firstname, PDO::PARAM_STR);
        $query->bindParam(':lastname', $this->m_lastname, PDO::PARAM_STR);
        $query->bindParam(':birthdate', $this->m_birthdate, PDO::PARAM_STR);
            
        $query->execute();

        $rowcount = $query->rowCount();
            
        if($rowcount !== 1){
            throw new UsersException('User not updated');
        }
        }catch(PDOException $ex){
            throw new UsersException('Error updating user');
        }
    }

    private function Insert(){
        try{
        $query = $this->writeDB->prepare("INSERT INTO Users  
            (email, emailverified, phone, phoneverified, password, firstname, lastname, birthdate)
            VALUES (:email,:emailverified, :phone, :phoneverified, :password, :firstname, :lastname, :birthdate)");
        $query->bindParam(':email', $this->m_email, PDO::PARAM_STR);
        $query->bindParam(':emailverified', $this->m_emailverified, PDO::PARAM_BOOL);
        $query->bindParam(':phone', $this->m_phone, PDO::PARAM_STR);
        $query->bindParam(':phoneverified', $this->m_phoneverified, PDO::PARAM_BOOL);
        $query->bindParam(':password', $this->m_hashed_password, PDO::PARAM_STR);
        $query->bindParam(':firstname', $this->m_firstname, PDO::PARAM_STR);
        $query->bindParam(':lastname', $this->m_lastname, PDO::PARAM_STR);
        $query->bindParam(':birthdate', $this->m_birthdate, PDO::PARAM_STR);
            
        $query->execute();
           
        if($query->rowCount() !== 1){
            throw new UserException('User not created');
        }
        }catch(PDOException $ex){
            throw new UserException('Error creating user');
        }
    }

    private function CreateEmailVerification(){

        $query = $this->writeDB->prepare("DELETE FROM EmailVerification WHERE email=:email");
        $query->bindParam(':email', $this->m_email, PDO::PARAM_STR);

        $query->execute();
        
        $guid = Common::GetGuid();

        $query = $this->writeDB->prepare("INSERT INTO EmailVerification  
        (email, verificationID)
        VALUES (:email,:verificationID)");
        $query->bindParam(':email', $this->m_email, PDO::PARAM_STR);
        $query->bindParam(':verificationID', $guid, PDO::PARAM_STR);

        $query->execute();

        $rowcount = $query->rowCount();
    
        if($rowcount !== 1){
            throw new UserException('Verification not created');
        }
        $this->SendEmailVerificationMail($guid);
    }

    public function SendEmailVerificationMail($guid){

        $maildata = array();
        $maildata['email'] = $this->getEmail();
        $maildata['headder'] = "Verify your email";
        $maildata['body'] = "You have created an account at MPJ-SOFT please verify email by clicking this link: <a href=\"" . Settings::VerifyMailSite . "?hash=". $guid ."&email=" . $this->getEmail() . "\">Verify email</a>";
        $maildata['password'] = Settings::EmailPassword;

        $json = json_encode($maildata);

        $ch = curl_init();                                                                      
        curl_setopt($ch, CURLOPT_URL, Settings::MailSite);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");                                                                     
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);                                                                  
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                                                      
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
            'Content-Type: application/json',                                                                                
            'Content-Length: ' . strlen($json))                                                                       
        );                                                                                                                   
                                                                                                                             
        $result = curl_exec($ch);

        $retval = json_decode($result);

        if($retval->success === false){
            throw new UserException('Verification email not send!');            
        }
    }

    private function checkEmailExist($email){
        
        try{
            
            $query = $this->writeDB->prepare('SELECT id, email, emailverified FROM Users WHERE email = :email');
            $query->bindParam(':email', $email, PDO::PARAM_STR);
            $query->execute();

            $rowcount = $query->rowCount();
            
            $retval = Array();

            if($rowcount === 1){
                $row = $query->fetch(PDO::FETCH_ASSOC);
                $retval['emailverified'] = $row["emailverified"]===1?true:false;
                $retval['emailexists'] = true;
                $retval['id'] = $row["id"];
            }else{
                $retval['emailverified'] = false;
                $retval['emailexists'] = false;
                $retval['id'] = -1;
            }
            return $retval;
        }
        catch(PDOException $ex){
            error_log("Error getting email from DB"); 
            throw new UserException("Error getting email from DB");
        }
    }
}

        