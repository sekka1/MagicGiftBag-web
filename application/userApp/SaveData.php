<?php

class SaveData {

    private $debug;
    private $generic;

    public function __construct(){

        $this->debug = false;

        Zend_Loader::loadClass('Generic');
        $this->generic = new Generic();

    }
    public function setDebugTrue(){

        $this->debug = true;

    }
    public function saveFacebookFeed( $feed, $user_id, $authToken ){
        // This saves the facebook feed of what is pulled from the user queried.  It also tries to save
        // the auth token so we can possibly do something with it on the side

        $data['user_id'] = $user_id;
        $data['authToken'] = $authToken;
        $data['feed'] = $feed;
        $data['datetime_created'] = 'now()';
        $data['datetime_modified'] = 'now()';
    
        $this->generic->save( 'Saved_Feeds', $data );        
    }
    public function saveProductQueries( $type, $user_id, $product ){
        // Saves the string that the system queries either the interest or persona type
        // $type = interest, persona

        $data['type'] = $type;
        $data['user_id'] = $user_id;
        $data['product'] = $product;
        $data['datetime_created'] = 'now()';
        $data['datetime_modified'] = 'now()';

        $this->generic->save( 'Saved_Product_Queries', $data );
        
    }
    public function saveFacebookLikes( $likesArray, $user_id, $authToken ){
        // This saves the facebook likes of what is pulled from the user queried.  It also tries to save
        // the auth token so we can possibly do something with it on the side
        // 
        // $likesArray = an array

        $data['user_id'] = $user_id;
        $data['authToken'] = $authToken;
        $data['likes'] = json_encode( $likesArray );
        $data['datetime_created'] = 'now()';
        $data['datetime_modified'] = 'now()';

        $this->generic->save( 'Saved_Likes', $data );
    }
}
