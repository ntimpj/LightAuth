<?php

require_once('lib/Settings.php');

class DB{

    private static $readDBConnection;
    private static $writeDBConnection;

    public static function connectWriteDB(){
        if(self::$writeDBConnection === null){
            self::$writeDBConnection = new PDO(Settings::WriteConnection, Settings::WriteUser, Settings::WritePW);
            self::$writeDBConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            self::$writeDBConnection->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        } 
        return self::$writeDBConnection;
    }

    public static function connectReadDB(){
        if(self::$readDBConnection === null){
            self::$readDBConnection = new PDO(Settings::ReadConnection, Settings::ReadUser, Settings::ReadPW);
            self::$readDBConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            self::$readDBConnection->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        } 
        return self::$readDBConnection;
    }

}