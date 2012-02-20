<?php
// This class searches the Google Shopping API to return products

// For Google Shopping API
defined('GOOGLE_API_KEY') or define('GOOGLE_API_KEY', 'AIzaSyAbjTNnzztRaER1HJA-sADRDlAIbj63hNc');

class GoogleShoppingSearch {

    private $debug;

    public function __construct(){

        $this->debug = false;
    }
    public function setDebugTrue(){

        $this->debug = true;
    }
    public function search( $searchTerm, $numberOfItems, $startindex, $priceRange ){


        $response;

        try{

            // Clean up the search term to be URL safe
            $searchTerm = urlencode( $searchTerm );

            $key = 'key=' . GOOGLE_API_KEY;
            $country = 'country=US';
            $alt = 'alt=json';
            $q = 'q='. $searchTerm; 
            $crowdingRule = 'crowdBy=accountId:3,brand:2';
            $restrictBy = 'restrictBy=price='.$priceRange;

            $url = 'https://www.googleapis.com/shopping/search/v1/public/products?'.$key.'&'.$country.'&'.$alt.'&'.$q.'&'.$crowdingRule.'&startIndex='.$startindex.'&maxResults='.$numberOfItems.'&';

            $response = file_get_contents( $url );

        }
        catch( Exception $e)                                                                                                        
        {                                                                                                                          
            echo $e->getMessage();                                                                                                 
        }
        
        if( $this->debug ){
            print_r( json_decode( $response, true ) );
        }

        return json_decode( $response, true );
    } 


}


?>
