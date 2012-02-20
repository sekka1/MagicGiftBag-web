<?php
// Make related calls and manipulations to various data sources to return relevant gift ideas

class GiftEngine{

    private $facebookSession;
    private $personaType;
    private $facebookFeeds;
    private $debug;
    private $main_list;
    private $didSetMainList;
    private $useMobile;
    private $accessToken;
    private $userID;
    
    public function __construct( $facebookSession=null ){

        $this->facebookSession = $facebookSession;

        Zend_Loader::loadClass('PersonaType');
        $this->personaType = new PersonaType();

        Zend_Loader::loadClass('FacebookFeeds');
        $this->facebookFeeds = new facebookFeeds( $this->facebookSession );

        $this->didSetMainList = false;
        $this->useMobile = false;
        $this->debug = false;
    }      
    public function setDebugTrue(){

        $this->debug = true;
    }
    public function setMainList( $main_list ){
        
        $this->main_list = $main_list;
        $this->didSetMainList = true;
    }
    public function getTopCategoryList( $user_id=null ){
        // INPUT: facebook user id
        //
        // This function will use a facebook id and get all the likes and profile info and manipulate the data to return
        // and an array of their top Likes
        //
        // Functionality has also been added so that the fb id does not need to be passed in.  In this case it will not
        // go and collect data from fb and is expected that it is set prior to being called.
        //
        // RETURN: A facebook Like array but filter/sorted/and manipulated with the best likes on top

        $main_list;

        Zend_Loader::loadClass('FacebookLikes');

        $facebookLikes = new facebookLikes();
    
        if( $this->debug )
            $facebookLikes->setDebugTrue();
        
        // Can be set out side or this function will go and get the main_list of various likes for this user
        if( ! $this->didSetMainList ){
            $main_list = $facebookLikes->getFacebookLikeData( $this->facebookSession, $user_id );
        }else{
            $main_list = $this->main_list;
        }

        if( $this->debug ){
            echo '<h3>main_list</h3>';
            print_r( $main_list );
        }
        
        // Save the likes to our database
        Zend_Loader::loadClass('SaveData');
        $saveData = new SaveData();
        $saveData->saveFacebookLikes( $main_list, $this->userID, $this->accessToken );            

        $likesCategorized = $facebookLikes->sumUpLikesCategories( $main_list );

        if( $this->debug ){
            echo '<h3> likesCategorized</h3>';
            print_r( $likesCategorized );
        }

        $topCategorySorted = $facebookLikes->sortTopCategories( $likesCategorized );

        if( $this->debug ){
            echo '<h3>Top Categorys sorted</h3>';
            print_r( $topCategorySorted );
            echo '<h3>Top Categorys sorted and filter-weighted</h3>';
        }

        $topCategorySorted = $facebookLikes->filter_weightCategory( $topCategorySorted );

        if( $this->debug ){
            print_r( $topCategorySorted );
    
            echo '<h3>Top 5 Category Hits and 3 items in each category</h3>';
        }

        $topCategoriesHits = $facebookLikes->getTopXCategorySearchTerms( $topCategorySorted, $likesCategorized, $main_list );

        // Put it through the augment maniplulation filter to augment the search terms if needed                                       
        $topCategoriesHits = $facebookLikes->manipulate_augmentSearchTerm( $topCategoriesHits );

        if( $this->debug ){
            print_r( $topCategoriesHits );
        }

        $this->view->topCategoriesHits = $topCategoriesHits;

        return $topCategoriesHits;

    }
    public function getTopCategoryListMobile_old( $request_vars ){
        // This is the mobiles way of using this engine to get the top like search terms.
        // INPUT in the request var: main_list = is a FB like json.  Formmated in their way also
        // OUTPUT: the json of the top search term based on the Likes

        $this->setMainList( json_decode( $request_vars->getParam( 'main_list' ), 1 ) );

        return json_encode( $this->getTopCategoryList() ); 
    }
    public function getTopCategoryListMobile( $request_vars ){

        $accessToken = $request_vars->getParam( 'accessToken' );
        $userID = $request_vars->getParam( 'userID' );

        $this->userID = $userID;
        $this->accessToken = $accessToken;

        $returnVar = '';

        if( is_numeric( $userID ) ){

            if( isset( $accessToken ) ){

                // This user is coming in un authenticated by facebook but has an auth token. 
                // Using the auth token to grab user's likes for the gift engine to run
                Zend_Loader::loadClass('fbAuth');

                $fbAuth = new fbAuth();

                $results = $fbAuth->queryGraphAPI( $accessToken, $userID.'/likes' );

                $this->setMainList( json_decode( $results, 1 ) );

                $returnVar = json_encode( $this->getTopCategoryList() );

            }
        }

        return $returnVar;
        
    }
    public function getUserPersonaType( $user_id ){
        // This function will grab the user's facebook feed and other information.  Then run it
        // through the PersonaType functions to see what type of personality this users is

        if( $this->debug )
            $this->personaType->setDebugTrue();

        // Get user's feed setting from Facebook
        $feed = $this->getUserPersonaProfile( $user_id );

        // Save the feed to our database
        Zend_Loader::loadClass('SaveData');
        $saveData = new SaveData();
        $saveData->saveFacebookFeed( $feed, $user_id, $this->accessToken );

        // Match user to a persona type
        $personaCounts = $this->personaType->getPersonaCounts( $feed );

        return $personaCounts;
    }
    public function getUserPersonaTypeMobile( $request_vars ){
        // This function is here for the mobile query where the user is not logged into our system an is going to
        // pass in an auth token to make the query


        $accessToken = $request_vars->getParam( 'accessToken' );
        $userID = $request_vars->getParam( 'userID' );

        $returnVar = '';

        if( is_numeric( $userID ) ){

            if( isset( $accessToken ) ){

                $this->useMobile = true;

                $this->accessToken = $accessToken;

                $personaReturn = $this->getUserPersonaType( $userID );

                $returnVar = array();

                // Put it into a more friendly array
                foreach( $personaReturn as $key=>$val ){

                    $temp['name'] = $key;
                    $temp['value'] = $val;

                    array_push( $returnVar, $temp );
                } 
            }
        }

        return json_encode( $returnVar ); 
    } 
    public function getUserPersonaProfile( $user_id ){
        // This function will get this user's facebook feed data 

        if( $this->debug )
            $this->facebookFeeds->setDebugTrue();


        if( $this->useMobile ){

            $this->facebookFeeds->useMobile( $this->accessToken );

        } 

        $feed = $this->facebookFeeds->getUserPersonaProfile( $user_id );
        

        if( $this->debug )
            echo '<br/><br/><h3>Users Feed Data</h3>' . $feed;

        return $feed;
    }
    public function getInterestsData( $request_vars ){
        // This function gets all the data for the Interest page
        // The Like list, Persona type

        //$getTopCategoryListMobile['interests'] = array();//json_decode( $this->getTopCategoryListMobile( $request_vars ), true );
        $getTopCategoryListMobile['interests'] = json_decode( $this->getTopCategoryListMobile( $request_vars ), true );
        $getUserPersonaTypeMobile['persona'] = json_decode( $this->getUserPersonaTypeMobile( $request_vars ), true ); 

        $aggregatedArray = array();

        array_push( $aggregatedArray, $getTopCategoryListMobile );
        array_push( $aggregatedArray, $getUserPersonaTypeMobile );
//print_r( $aggregatedArray ); 
        return json_encode( $aggregatedArray );
    }
}

?>
