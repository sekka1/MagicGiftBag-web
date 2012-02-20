<?php
// This class takes in an Amazon product search response and it performs some filtering that we want
//
//  Filters can be taking out a product result or re-ordering the results set that comes back

class AmazonFilters {

    private $debug;

    public function __construct(){
        
        $this->debug = false;
    }
    public function setDebugTrue(){
        $this->debug = true;
    }
    function filter_checkDisplayItem( $aItem, $debug = false ){
        // This filter is here for us to throw out items and not display them
        //
        // True = display item; False = dont display item
        // Takes in an "Item" from the amazon search response

        $displayItem = true;

        if( isset( $aItem['ItemAttributes']['ProductGroup'] ) ){
            if( $aItem['ItemAttributes']['ProductGroup'] == 'Book' ){                                                        
                $displayItem = false;                                                                                        
    
                if( $this->debug )
                    echo 'Threw out a book:'.$aItem['ItemAttributes']['Title'].'<br/>';                                          
            }                                                                                                                
        }                                                                                                                    

        return $displayItem;                                                                                                 
    }                     

}


?>
