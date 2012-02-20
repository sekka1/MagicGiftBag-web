<?php

class Ratings
{
    private $accessToken;
    private $fbAuth;
    private $generic;
    private $userId; // The user's facebook ID
    private $isAuthorized;
    private $hasCheckAuthorization; //boolean to not keep on rechecking auth

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

        // Keep track the state of authorization
        $this->isAuthorized = false;
        $this->hasCheckAuthorization = false;
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
    public function rate( $request_vars ){
        // Rate an item.  First check if we should update the rating of a current item or
        // insert a new row.

        if( $this->isAuthorized( $request_vars ) ){

            if( $this->itemIsRated( $request_vars ) ){
                    // Update the rating for this item

                    $item_id_seq = $request_vars->getParam( 'id_seq' );
                    $rating = $request_vars->getParam( 'rate' );

                    $query = 'UPDATE Ratings SET rating = '.$rating.' WHERE user_id = '.$this->userId.' AND items_id_seq = '.$item_id_seq;

                    $returnVal = $this->generic->customQuery( 'Ratings', $query );

            } else {
                    // Insert a new row for this item rating

                    $data['items_id_seq'] = $request_vars->getParam( 'id_seq' );
                    $data['user_id'] = $this->userId;
                    $data['type'] = 'star';
                    $data['rating'] = $request_vars->getParam( 'rate' );
                    $data['datetime_created'] = 'now()';
                    $data['datetime_modified'] = 'now()';

                    $this->generic->save( 'Ratings', $data );
            }
        }
    }
    public function getARating( $request_vars ){

        $id_seq = $request_vars->getParam( 'id_seq' );

        $results = $this->generic->customQuery( 'Ratings', 'SELECT * FROM Ratings WHERE user_id = '.$this->userId.' AND items_id_seq =  '.$id_seq );

        return json_encode( $results );
    }
    public function itemIsRated( $request_vars ){
        // Returns true or false given an Item id_seq to see if the current user has rated this item already

        $isRated = false;

        $results = $this->getARating( $request_vars );

        $results_decoded = json_decode( $results, 1 );

        if( count( $results_decoded ) > 0 )
            $isRated = true;

        return $isRated;
    }
    public function isAuthorized( $request_vars ){
        // Check whether this user has an active session via the user passing in it's Facebook
        // auth token

        $isAuthorized = false;

        if( ! $this->hasCheckAuthorization ){        
            $userInfo = $this->fbAuth->setAccessToken( $request_vars->getParam( 'accessToken' ) );

            //print_r( $userInfo );

            $this->userId = $userInfo['id'];

            if( is_array( $userInfo ) )
                $isAuthorized = true;

            $this->hasCheckAuthorization = true;
        } else {
            // Re-use the previously checked auth value

            $isAuthorized = $this->isAuthorized;
        }

        return $isAuthorized;
    }
    public function getRatings( $request_vars ){

        // Set some random key for a little security
        $key_match = 'dkeir739U3jdudj3HHH839Wejcuemkcushekd893284';

        // some key to verify it is a legit call
        $key = $request_vars->getParam( 'key' );

        $data = '';

        if( $key_match == $key ){

            // Make query
            $results = $this->generic->customQuery( 'Ratings', 'SELECT * FROM Ratings WHERE rating IS NOT NULL' );

            // Put into csv format: user_id, item_id, rating
            foreach( $results as $anItem ){

                $data .= $anItem['user_id'].",".$anItem['items_id_seq'].",".$anItem['rating']."\n";
            }

        }

        return $data;
    }

}

?>
