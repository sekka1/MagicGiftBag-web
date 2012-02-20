<?php

class GiftController extends Zend_Controller_Action
{

    private $username;
    private $user_id_seq;
    private $fbAuth;

    public function init()
    {
        /* Initialize action controller here */

    }
     public function preDispatch(){

        // Authentication with FB
        Zend_Loader::loadClass('fbAuth');

        $this->fbAuth = new fbAuth();

        if( !$this->fbAuth->hasIdentity() ){
                $this->_redirect( '/login?f=' . $this->_request->getRequestUri() );
        } else {
                // User is valid and logged in
                $this->user_id_seq = $this->fbAuth->getUID();

                // Only allow these FB users to this controller
                $user_profile = $this->fbAuth->facebook->api('/me');

                $allowedUIDs = array( '566708666', '3420169', '30901818', '1133764889', '32701479' );
                //$allowedUIDs = array( 'zzzz', 'zz' );

                if( ! in_array( $user_profile['id'], $allowedUIDs ) )
                   $this->_redirect( '/' );
        }


     }
/*
    // This function will be called to handle any Actions that is not defined in the
    // controller file.  Its one way to catch all Actions 
    public function __call($method, $args)
    {
        if ('Action' == substr($method, -6)) {
            // If the action method was not found, render the error
            // template
            return $this->render('error');

	    // Forward to another page
	    //return $this->_forward('index');
        }
 
        // all other methods throw an exception
        throw new Exception('Invalid method "'
                            . $method
                            . '" called',
				500);

    }
*/
    public function indexAction()
    {
        // Pass the facebook object for the view to use
        $this->view->facebook = $this->fbAuth->facebook;
    }
    public function friendsAction()
    {
        // Pass the facebook object for the view to use
        $this->view->facebook = $this->fbAuth->facebook; 


    }
    public function oneAction()
    {
        // Pass the facebook object for the view to use
        $this->view->facebook = $this->fbAuth->facebook;

        // User - the friends ID that we want to look up
        $user_id = $this->_request->getParam( 'user' );

        $debug = $this->_request->getParam( 'debug' );

        $this->view->user_id = $user_id;

        Zend_Loader::loadClass('FacebookLikes');

        $facebookLikes = new facebookLikes();

////////

            $main_list = $facebookLikes->getFacebookLikeData( $this->fbAuth->facebook, $user_id );

            echo '<h3>main_list</h3>';
            print_r( $main_list );

            $likesCategorized = $facebookLikes->sumUpLikesCategories( $main_list );

            echo '<h3> likesCategorized</h3>';
            print_r( $likesCategorized );

            $topCategorySorted = $facebookLikes->sortTopCategories( $likesCategorized );

            echo '<h3>Top Categorys sorted</h3>';
            print_r( $topCategorySorted );

            echo '<h3>Top Categorys sorted and filter-weighted</h3>';
            $topCategorySorted = $facebookLikes->filter_weightCategory( $topCategorySorted );

            print_r( $topCategorySorted );

            echo '<h3>Top 5 Category Hits and 3 items in each category</h3>';
            $topCategoriesHits = $facebookLikes->getTopXCategorySearchTerms( $topCategorySorted, $likesCategorized, $main_list );

            // Put it through the augment maniplulation filter to augment the search terms if needed
            $topCategoriesHits = $facebookLikes->manipulate_augmentSearchTerm( $topCategoriesHits );

            print_r( $topCategoriesHits );
/////////



            // Seach Amazon and get display result array
            Zend_Loader::loadClass('AmazonDisplay');

            $amazonDisplay = new AmazonDisplay();

            if( $debug )
                $amazonDisplay->setDebugTrue();

            $displayResults = $amazonDisplay->getTopHitsDisplay( $topCategoriesHits, 3 ); 

            echo '<h3>amazonDisplay->getTopHitsDisplay</h3>';
            print_r( $displayResults );

            $this->view->displayResults = $displayResults;

    }
    public function twoAction()
    {
        // This is for the searchterm view

        // Pass the facebook object for the view to use
        $this->view->facebook = $this->fbAuth->facebook;

        // User - the friends ID that we want to look up
        $user_id = $this->_request->getParam( 'user' );
        
        $debug = $this->_request->getParam( 'debug' );

        $this->view->user_id = $user_id;

        Zend_Loader::loadClass('GiftEngine');

        $giftEngine = new GiftEngine( $this->fbAuth->facebook );

        if( $debug == 'true' )
            $giftEngine->setDebugTrue();

        $this->view->topCategoriesHits = $giftEngine->getTopCategoryList( $user_id );

        $giftEngine->getUserPersonaType( $user_id );

    }                                                                         
    public function threeAction(){
        // This is for the persona type view

        // User - the friends ID that we want to look up
        $user_id = $this->_request->getParam( 'user' );

        $debug = $this->_request->getParam( 'debug' );

        $this->view->user_id = $user_id;

        Zend_Loader::loadClass('GiftEngine');

        $giftEngine = new GiftEngine( $this->fbAuth->facebook );

        if( $debug == 'true' )
            $giftEngine->setDebugTrue();

        $this->view->personaTypes = $giftEngine->getUserPersonaType( $user_id );

    }
    public function gettopcategorylistAction(){
        // Returns a json array of the top categories for a user based on the FB user's id passed in
        //
        // App step 2: based on the friend that is picked get all the Likes and show a list of product
        //              search term list

        // Debug output param
        $debug = $this->_request->getParam( 'debug' );

        // Pass the facebook object for the view to use
        $this->view->facebook = $this->fbAuth->facebook;

        // User - the friends ID that we want to look up
        $user_id = $this->_request->getParam( 'user' );

        $this->view->user_id = $user_id;

        Zend_Loader::loadClass('GiftEngine');

        $giftEngine = new GiftEngine( $this->fbAuth->facebook );

        if( $debug == true )
            $giftEngine->setDebugTrue();

        $this->view->topCategoriesHits = json_encode( $giftEngine->getTopCategoryList( $user_id ) );
    }
    public function searchtestAction(){
        // Searches a product based on the search term that is passed in and displays it on the screen.  This
        // has html view associated with it.  Really used for debugging.

        $q = $this->_request->getParam( 'q' ); // The search term
        $searchIndex = $this->_request->getParam( 'searchIndex' ); // The starting point for the product number for pagination
        $numberOfItems = $this->_request->getParam( 'numberOfItems' ); // The number of items you want the search to return 
        $priceRange = $this->_request->getParam( 'priceRange' ); // Price range to search for

        $debug = $this->_request->getParam( 'debug' );

        // Seach Amazon and get display result array
        Zend_Loader::loadClass('ProductSearch');

        $productSearch = new ProductSearch();

        $productSearch->setAffiliate( 'amazon' );

        $productSearch->setDebugTrue();

        $searchResult = $productSearch->searchAndFilter( $q, $numberOfItems, $searchIndex, $priceRange );  

        $this->view->searchResult = $searchResult;

        //print_r( $searchResult );
        if( $debug == true ){
            echo '<h3>Results</h3>'; print_r( $searchResult );
        }
    }
    public function justsearchamazonAction(){
        // A raw dev search of amazon and return their raw results

        $q = $this->_request->getParam( 'q' ); // The search term

        Zend_Loader::loadClass('AmazonSearch');

        $amazonSearch = new AmazonSearch;

        $this->view->results = $amazonSearch->search( $q, 'All', 'Large' );
        
    }
    public function searchAction(){
        // Searches a product based on the search term that is passed in and displays it on the screen.  This
        // has html view associated with it.  Really used for debugging.
        //
        // App step 3: search on the search term that the user wants to see and return some results

        $q = $this->_request->getParam( 'q' ); // The search term
        $debug = $this->_request->getParam( 'debug' ); // debug switch
        $searchIndex = $this->_request->getParam( 'searchIndex' ); // The starting point for the product number for pagination
        $numberOfItems = $this->_request->getParam( 'numberOfItems' ); // The number of items you want the search to return
        $priceRange = $this->_request->getParam( 'priceRange' ); // Price range to search for

        // Seach Amazon and get display result array
        Zend_Loader::loadClass('ProductSearch');

        $productSearch = new ProductSearch();

        $productSearch->setAffiliate( 'amazon' );
    
        if( $debug )
            $productSearch->setDebugTrue();

        $searchResult = $productSearch->searchAndFilter( $q, $numberOfItems, $searchIndex, $priceRange, $debug );   

        $this->view->searchResult = json_encode( $searchResult );
 
        if( $debug == 'true' )
            print_r( $searchResult );

    }
    public function getfriendsAction(){
        // Returns a json only list of this currently logged in user's friends
        //         
        // App Step 1 - get friends list

        Zend_Loader::loadClass('FacebookLikes');

        $facebookLikes = new FacebookLikes();

        $friendsList  = $facebookLikes->getFriendsList( $this->fbAuth->facebook );

        $this->view->friends = json_encode( $friendsList );
    }
    public function euclideanAction(){

        Zend_Loader::loadClass('Euclidean');
        
        $euclidean = new Euclidean();

        $euclidean->setFakeData();

        echo 'Euclidean example 1: ';
        echo $euclidean->twoAxis( $euclidean->critics['Toby'][0]['Snakes on a Plane'], 
                                    $euclidean->critics['Toby'][0]['You, Me and Dupree'],
                                    $euclidean->critics['Mick LaSalle'][0]['Snakes on a Plane'],
                                    $euclidean->critics['Mick LaSalle'][0]['You, Me and Dupree']
                                );
        echo '<br/>';

        $euclidean->pearson( 'Lisa Rose', 'Gene Seymour' );

        foreach( $euclidean->critics as $key1=>$val1 ){

            foreach( $euclidean->critics as $key2=>$val2 ){

                echo 'Pearson score for: ' . $key1 . ' and ' . $key2 . ' -- ';

                echo $euclidean->pearson( $key1, $key2 );

                echo '<br/>';
            }
        }
    }
}
