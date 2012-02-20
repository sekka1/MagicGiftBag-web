<?php

class Crud 
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
    public function create( $request_vars ){

        $returnVal = '';

        if( $this->isAuthorized( $request_vars ) ){

            $tableName = $request_vars->getParam( 'tableName' );

            if( $tableName != '' ){

                $data = $this->parseForKeyValuesPairs( $request_vars );

                if( $this->debug )
                    print_r( $data );

                $returnVal = $this->generic->save( $tableName, $data );
            }
        }

        return $returnVal;
    }
    public function read( $request_vars ){

        $returnVar = '';

        if( $this->isAuthorized( $request_vars ) ){
            $tableName = $request_vars->getParam( 'tableName' );
            $whereKey = $request_vars->getParam( 'whereKey' );
            $whereValue = $request_vars->getParam( 'whereValue' );

            if( $tableName != '' && $whereKey != '' && $whereValue != '' ){

                $query = 'SELECT * FROM ' . $tableName . ' WHERE ' . $whereKey . ' = ' . $whereValue;

                $returnVar = $this->generic->customQuery( $tableName, $query );
            }
        }

        return json_encode( $returnVar );
    }
    public function update( $request_vars ){

        $returnVar = '';

        if( $this->isAuthorized( $request_vars ) ){
            $tableName = $request_vars->getParam( 'tableName' );
            $whereKey = $request_vars->getParam( 'whereKey' );
            $whereValue = $request_vars->getParam( 'whereValue' );

            if( $tableName != '' && $whereKey != '' && $whereValue != '' ){

                $data = $this->parseForKeyValuesPairs( $request_vars );

                $returnVar = $this->generic->edit_noauth( $tableName, $whereValue, $data, $whereKey );
            }
        }
        return $returnVar;
    }
    public function delete( $request_vars ){

        $returnVar = '';

        if( $this->isAuthorized( $request_vars ) ){
            $tableName = $request_vars->getParam( 'tableName' );
            $whereKey = $request_vars->getParam( 'whereKey' );
            $whereValue = $request_vars->getParam( 'whereValue' );

            if( $tableName != '' && $whereKey != '' && $whereValue != '' ){

                $returnVar = $this->generic->remove_noauth( $tableName, $whereValue, $whereKey );
            }
        }

        return $returnVar;
    }
    public function parseForKeyValuesPairs( $request_vars ){
    // Goes through the request var and returns all the key and values in a structure that the model will take in
    //
    // all key name starts with "key_"

        $data;

        $tempData = $request_vars->getParams();

        foreach( $tempData as $key=>$val ){

            if( preg_match( "/^key_/", $key ) > 0 )
                $data[str_replace( "key_", "", $key)] = $val;
        }

        return $data;
    }
    public function isAuthorized( $request_vars ){
        // Check whether this user has an active session via the user passing in it's Facebook
        // auth token
        
        $isAuthorized = false;

        $userInfo = $this->fbAuth->setAccessToken( $request_vars->getParam( 'accessToken' ) );

//        print_r( $userInfo );
        
        $this->userId = $userInfo['id'];

        if( is_array( $userInfo ) ) 
            $isAuthorized = true;
        else
            echo 'not autorized';

        return $isAuthorized;
    }
}

?>
