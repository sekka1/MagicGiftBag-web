<?php
// This class handles events from various sources

class Events 
{
    private $accessToken;
    private $fbAuth;
    private $generic;
    private $userId; // The user's facebook ID

    private $debug;

    public function __construct(){

        $this->debug = false;

        // This user is coming in un authenticated by facebook but has an auth token.                                
        // Using the auth token to grab user's likes for the gift engine to run
        Zend_Loader::loadClass('fbAuth');
        $this->fbAuth = new fbAuth(); 

        // Model for generic database table
        Zend_Loader::loadClass('Generic');
        $this->generic = new Generic();
    }
    public function setDebugTrue(){

        $this->debug = true;
    }

}
