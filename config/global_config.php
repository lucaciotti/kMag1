<?php

class CONFIG {
    private static $initialized;
    //Preconfigurato su SPAGNA
    // public static $SERVER_IP = '172.16.2.9'; //Backup
    public static $SERVER_IP = '172.16.2.102';
    // public static $SERVER_PORT = '3018'; //  SPAGNA
    // public static $SERVER_PORT = '3019'; //  ITALIA
    public static $SERVER_PORT = '3020'; //  FRANCIA
    public static $API_VERSION = 'v1';

    public static $DEFAULT_MAG = '00001';
    // public static $DEFAULT_MAG = '00002'; // SPAGNA
    
    public static $PRINTERS = array(		
		0 => "Spagna_4_PL",
		1 => "Spagna_PL_piccole_SATO"
    );

    public static $BANC_PRT = '';

    public static $IS_FILIALE = true;
    // private static function initialize(){
    //     if (self::$initialized) return;
        
    //     self::$SERVER_IP = '172.16.2.9';
    //     self::$SERVER_PORT = '3018';
    //     self::$API_VERSION = 'v1';
    //     self::$DEFAULT_MAG = '00002';
    //     self::$PRINTERS = array(		
    //       0 => "Spagna_4_PL",
    //       1 => "Spagna_PL_piccole_SATO"
    //     );
    //     self::$BANC_PRT = '';
    //     self::$initialized = true;
    // }
}