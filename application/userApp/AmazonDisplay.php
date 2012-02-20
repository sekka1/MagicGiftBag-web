<?php
// This class manipulates the results into one array to return to the view for displaying

class AmazonDisplay {

    private $amazonSearch;
    private $amazonFilters;
    private $standardizeProductResults;
    private $debug;

    public function __construct(){

        Zend_Loader::loadClass('AmazonSearch');
        $this->amazonSearch = new AmazonSearch();

        Zend_Loader::loadClass('AmazonFilters');
        $this->amazonFilters = new AmazonFilters();

        Zend_Loader::loadClass('StandardizeProductResults');
        $this->standardizeProductResults = new StandardizeProductResults();
        
        $this->debug = false;
    }
    public function setDebugTrue(){

        $this->debug = true;

        // Turning on the debug for the classes we call in here
        $this->amazonSearch->setDebugTrue();
        $this->amazonFilters->setDebugTrue();
    }

    public function getTopHitsDisplay( $topCategoriesHits, $numberofTopHitsPerSearchItem ){
        // Takes an input from $facebookLikes->getTopXCategorySearchTerms();
        // Searches amazon and prints out the results
        // the $facebookLikes->getTopXCategorySearchTerms() gets back the top liked category
        // and the search terms
        //
        // $numberofTopHitsPerSearchItem = the number of items to return per search term
        //
        // For each $topCategoriesHits hits it will search amazon and return this ($numberofTopHitsPerSearchItem) many per search

        $allResponseItems = array();

        // Perform an Amazon search on all the items provided to this function
        foreach( $topCategoriesHits as $aSearchHit ){

            $numberOfItemsUsed = 0;

            $aResponse = $this->amazonSearch->search( $aSearchHit['name'], 'All', 'Large' );

            // Cycle through the $aResponse list to pick out items to put into the $allResponses array
            for( $i=0; $i < count( $aResponse ); $i++){
    
                if( !isset( $aResponse['Items']['Request']['Errors']['Error'] ) ){
                    // No error in this response, proceed

                    if( isset( $aResponse['Items']['Item'][$i] ) ){
                        // This item number exist

                        // Only display items that we have not thrown out
                        if( $this->amazonFilters->filter_checkDisplayItem( $aResponse['Items']['Item'][$i] ) ){
                        
                            // Only put X number of items that the user wants back
                            if( $numberOfItemsUsed < $numberofTopHitsPerSearchItem ){
            
                                $numberOfItemsUsed++;

                                array_push( $allResponseItems, $aResponse['Items']['Item'][$i] );                 
           
                            }

                        }

                    }

                }
            } 
        }

        //print_r( $allResponseItems );

        return $allResponseItems; 

    }
    public function searchAndFilter( $searchTerm, $debug = false ){
        // Search on the given string that is passed in and run the filter on it and return the amazon result set
        //
        // INPUT: $searchTerm is just a text string

        $allResponseItems = array();

        $aResponse = $this->amazonSearch->search( $searchTerm, 'All', 'Large' );

        // Cycle through the $aResponse list to pick out items to put into the $allResponses array
            for( $i=0; $i < count( $aResponse['Items']['Item'] ); $i++){

                if( !isset( $aResponse['Items']['Request']['Errors']['Error'] ) ){
                    // No error in this response, proceed

                    if( isset( $aResponse['Items']['Item'][$i] ) ){
                        // This item number exist

                        // Only display items that we have not thrown out
                        if( $this->amazonFilters->filter_checkDisplayItem( $aResponse['Items']['Item'][$i], $debug ) ){

                            array_push( $allResponseItems, $aResponse['Items']['Item'][$i] );
                        }

                    }                                                                                                                      

                }
            } 

        if( $debug == true || $this->debug == true ){
            // This will be the raw amazon results
            print_r( $allResponseItems );
        }

        return $this->standardizeProductResults->amazon( $allResponseItems );
    }
    public function searchMobile( $request_vars ){
        // This is the mobile way of using the search.  It will take the query parameter in and does not need to be auth via FB

        $searchResults = $this->searchAndFilter( $request_vars->getParam( 'q' ) );

        if( $request_vars->getParam( 'debug' ) == 'true' )
                print_r( $searchResults );

        return json_encode( $searchResults );

    }

}


?>
