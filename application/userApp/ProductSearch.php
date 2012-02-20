<?php
// This class manipulates the results into one array to return to the view for displaying

class ProductSearch {

    private $affiliateToSearch;  // Holds which Affiliate the user wants to search on
    private $amazonSearch;
    private $amazonFilters;
    private $standardizeProductResults;
    private $googleShoppingSearch;
    private $debug;

    public function __construct(){

        Zend_Loader::loadClass('AmazonSearch');
        $this->amazonSearch = new AmazonSearch();

        Zend_Loader::loadClass('AmazonFilters');
        $this->amazonFilters = new AmazonFilters();

        Zend_Loader::loadClass('StandardizeProductResults');
        $this->standardizeProductResults = new StandardizeProductResults();

        Zend_Loader::loadClass('GoogleShoppingSearch');
        $this->googleShoppingSearch = new GoogleShoppingSearch();
            
        $this->affiliateToSearch = 'amazon'; // Setting google to default search affiliate

        $this->debug = false;
    }
    public function setAffiliate( $affiliate ){
    // Sets which affiliate the caller wants to use
    //
    // $affiliate = 'amazon', 'google'

        $this->affiliateToSearch = $affiliate;
    }
    public function setDebugTrue(){

        $this->debug = true;

        // Turning on the debug for the classes we call in here
        $this->amazonSearch->setDebugTrue();
        $this->amazonFilters->setDebugTrue();
    }
/*
    public function getTopHitsDisplay( $topCategoriesHits, $numberofTopHitsPerSearchItem ){
        // Takes an input from $facebookLikes->getTopXCategorySearchTerms();
        // That is the Likes like output from the Facebook Graph API
        //
        // Searches amazon and returns the results
        // the $facebookLikes->getTopXCategorySearchTerms() gets back the top liked category
        // and the search terms
        //
        // $numberofTopHitsPerSearchItem = the number of items to return per search term
        //
        // For each $topCategoriesHits hits it will search amazon and return this ($numberofTopHitsPerSearchItem) many per search
        //
        // This is for layout 1.  Which is User select friend -> then it displays the 10 ten product immediately we think they will like

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
*/
    public function searchAndFilter( $searchTerm, $numberOfItems, $searchIndex, $priceRange, $debug = false ){
        // Search on the given string that is passed in and run the filter on it and return whatever is left after the filter
        //
        // INPUT: $searchTerm is just a text string
        //       $numberOfItems - number of items you want the product search to return
        //       $searchIndex - the starting point for the item to return.  Used for pagination
        //       $priceRange - price range 

        $allResponseItems = array();
        $returnStandardizedProductResultsSet = array(); // Holds the final product results set in our standardized format

        // Searching Using Amazon
        if( $this->affiliateToSearch == 'amazon' ){

            $aResponse = $this->amazonSearch->search( $searchTerm, 'All', 'Large' );

//            if( $debug == true || $this->debug == true )
//                echo '<h3>Raw Amazon Results</h3>'; print_r( $aResponse );

            // Put header Results info into array - info about the coming results like how many are in this set current item count, etc
            if( !isset( $aResponse['Items']['Request']['Errors']['Error'] ) ){

                if( isset( $aResponse['Items']['TotalResults'] ) ){                       

                    $temp['resultsInfo']['totalItems'] = $aResponse['Items']['TotalResults'];                                                                                  
//                    $temp['resultsInfo']['startIndex'] = $aResponse[''];                                                                                  
//                    $temp['resultsInfo']['itemsPerPage'] = $aResponse[''];                                                                              
//                    $temp['resultsInfo']['currentItemCount'] = $aResponse[''];                                                                      

                    array_push( $allResponseItems, $temp );                                                                                                         
                }                 
                
            }


            // Cycle through the $aResponse list to pick out items to put into the $allResponses array
 //           for( $i=0; $i < count( $aResponse['Items']['Item'] ); $i++){

            if( !isset( $aResponse['Items']['Request']['Errors']['Error'] ) ){
                // No error in this response, proceed

                for( $i=0; $i < count( $aResponse['Items']['Item'] ); $i++){

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

            $returnStandardizedProductResultsSet = $this->standardizeProductResults->amazon( $allResponseItems );
        }

        // Searching using Google Shopping
        if( $this->affiliateToSearch == 'google' ){

            if( ! is_numeric( $searchIndex ) )
                $searchIndex = 1;
            if( ( ! is_numeric( $numberOfItems ) ) )
                $numberOfItems = 1;

            $aResponse = $this->googleShoppingSearch->search( $searchTerm, $numberOfItems, $searchIndex, $priceRange );

            if( $debug == true || $this->debug == true ){
                echo '<h3> Raw Unfiltered Google Results</h3>';
                print_r( $aResponse );
            }

            // Put header Results info into array - info about the coming results like how many are in this set current item count, etc
            if( isset( $aResponse['totalItems'] ) &&
                isset( $aResponse['startIndex'] ) &&
                isset( $aResponse['itemsPerPage'] ) &&
                isset( $aResponse['currentItemCount'] ) ){
                $temp['resultsInfo']['totalItems'] = $aResponse['totalItems'];
                $temp['resultsInfo']['startIndex'] = $aResponse['startIndex'];
                $temp['resultsInfo']['itemsPerPage'] = $aResponse['itemsPerPage'];
                $temp['resultsInfo']['currentItemCount'] = $aResponse['currentItemCount'];

                array_push( $allResponseItems, $temp );
            }

            if( isset( $aResponse['items'] ) ){
            // Check if there are any items that were returned by the search

                // Cycle through the $aResponse list to pick out items to put into the $allResponses array
                for( $i=0; $i < count( $aResponse['items'] ); $i++ ){

                    // Not doing any filtering yet

                    array_push( $allResponseItems, $aResponse['items'][$i] );
                }

                if( $debug == true || $this->debug == true ){
                    // This will be the raw amazon results
                    echo '<h3>Only the Products with the extra serach info taken out</h3>';
                    print_r( $allResponseItems );
                }

                $returnStandardizedProductResultsSet = $this->standardizeProductResults->google( $allResponseItems );
            }
        }

        return $returnStandardizedProductResultsSet;
    }
    public function searchMobile( $request_vars ){
        // This is the mobile way of using the search.  It will take the query parameter in and does not need to be auth via FB

        $searchTerm = $request_vars->getParam( 'q' );
        $numberOfItems = $request_vars->getParam( 'numberOfItems' );
        $searchIndex = $request_vars->getParam( 'searchIndex' );
        $priceRange = $request_vars->getParam( 'priceRange' );
        $debug = $request_vars->getParam( 'debug' );

        $returnVar = '';

        // Save the feed to our database
        Zend_Loader::loadClass('SaveData');
        $saveData = new SaveData();
        $saveData->saveProductQueries( 'interest', '', $searchTerm );

        if( $searchTerm != '' &&
            is_numeric( $numberOfItems ) &&
            is_numeric( $searchIndex ) &&
            $priceRange != '' ){

            $searchResults = $this->searchAndFilter( $searchTerm, $numberOfItems, $searchIndex, $priceRange, $debug );

            if( $debug == 'true' )
                    print_r( $searchResults );

            $returnVar = json_encode( $searchResults );
        }

        return $returnVar;

    }

}


?>
