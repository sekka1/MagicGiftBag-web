<?php
// This class gets the various data from a user's feed area

class FacebookFeeds
{

    private $facebook_api_session;
    private $useMobile; // True/False.  For if the user wants to just pass in an access token for FB queries
    private $accessToken;
    private $fbAuth;

    private $debug;

    public function __construct( $facebook_api_session ){

        $this->facebook_api_session = $facebook_api_session;

        $this->useMobile = false;

        $this->debug = false;

        // This user is coming in un authenticated by facebook but has an auth token.                                
        // Using the auth token to grab user's likes for the gift engine to run
        Zend_Loader::loadClass('fbAuth');
                                                                                                                         
        $this->fbAuth = new fbAuth(); 
    }
    public function setDebugTrue(){

        $this->debug = true;
    }
    public function useMobile( $accessToken ){

        $this->useMobile = true;

        $this->accessToken = $accessToken;
    }
    public function getFacebookFeedData( $user_id ){
        // Retrieves this user's facebook feed
        //
        // INPUT: $user_id = FB user id of the user we want to pull on

        // Getting a list of this user's likes

        $users_feed;

        if( $this->useMobile ){
            // The user is going to pass in an auth token to make the querie

            $results = $this->fbAuth->queryGraphAPI( $this->accessToken, $user_id.'/feed&limit=50' );

            $users_feed = json_decode( $results, 1 );

        } else {
            // The user has logged into the web page and use that auth to make the query

            $users_feed = $this->facebook_api_session->api('/'.$user_id.'/feed&limit=50');
        }

        if( $this->debug )
            print_r( $users_feed );

        return $users_feed;
    }
    public function getUserPersonaProfile( $user_id ){

        $feedData = $this->getFacebookFeedData( $user_id );

        $feedString = $this->stringAfyFeedData( $feedData ); 

        return $feedString;
        
    }
    public function stringAfyFeedData( $feedData ){
        // Takes in a Facebook feed data structure and returns all relevant data that we want in one
        // long string
        //
        // INPUT: $feedData = FB data structure of the feed from the graph API
        //
        // OUTPUT: One long string of all data we want

        $finalString = '';

        if( isset( $feedData['data'] ) ){
            foreach( $feedData['data'] as $aFeed ){

                // Get each message and put it in the finalString
                if( isset( $aFeed['message'] ) )
                    $finalString .= $aFeed['message'] . ' '; 

                // Get the comments and put it into the finalString
                if( $aFeed['comments']['count'] > 0 ){

                    if( isset( $aFeed['comments']['data'] ) ){

                        foreach( $aFeed['comments']['data'] as $aComment ){

                            $finalString .= $aComment['message'] . ' ';
                        }
                    }
                }

            }
        }

        return $finalString;
    }
    public function getAllFriendsBirthday(){

        $results = $this->fbAuth->queryGraphAPI( $this->accessToken, 'fql?q={"query1":"SELECT uid2 FROM friend WHERE uid1 = me()","query2":"SELECT uid, name, birthday, birthday_date FROM user WHERE uid IN (SELECT uid2 FROM #query1)"}' ); 

        return $results;

    }
}

?>
