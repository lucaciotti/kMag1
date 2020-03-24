<?php

class CONFIG {
    // public static $SERVER_IP = '172.16.2.9'; //DEV
    public static $SERVER_IP = '172.16.2.102';
    public static $SERVER_PORT = '3019'; //  ITALIA
    // public static $SERVER_PORT = '3020'; //  FRANCIA
    // public static $SERVER_PORT = '3018'; //  SPAGNA

    public static $BASE_URL = 'http://172.16.2.102/bc/ita/'; // ITALIA
    // public static $BASE_URL = 'http://172.17.0.203/kMag1/src'; // FRANCIA
    // public static $BASE_URL = 'http://172.26.0.101/kMag1/src'; // SPAGNA
    
    public static $API_VERSION = 'v1';

    public static $DEFAULT_MAG = '00001';
    // public static $DEFAULT_MAG = '00002'; // SPAGNA

    public static $PRINTERS = array(
      0 => "2",
      1 => "0",
      2 => "5",
      3 => "6_PL",
      4 => "1_PL_GROSSE",
      5 => "6_PL_GROSSE",
      6 => "5_36x88",
    ); // ITALIA
    // public static $PRINTERS = array(
    //   0 => "Francia-Zebra"
    // ); // FRANCIA
    // public static $PRINTERS = array(		
		// 0 => "Spagna_4_PL",
		// 1 => "Spagna_PL_piccole_SATO"
    // ); // SPAGNA

    public static $BANC_PRT = '';

    public static $IS_FILIALE = false;

}