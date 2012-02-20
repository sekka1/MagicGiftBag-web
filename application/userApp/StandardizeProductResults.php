<?php
// This class takes in result outputs from various Shopping APIs such as Amazon and Google and puts it
// into our own format

class StandardizeProductResults {

    private $default_img_small = '';
    private $default_img_medium = '';
    
    public function __construct(){


    }

    public function amazon( $searchResults ){
    // Makes the amazon results into our own format
    // INPUT: An Amazon search result set

        $standarizedResults = array();

        $isFirstRow = true; // The first row holds meta data for the results set, so skip it

        if( !isset( $searchResult['Items']['Request']['Errors']['Error'] ) ){

            foreach( $searchResults as $aResult ){

                if( ! $isFirstRow ){
                    // Set main title of the product
                    $temp['title'] = $this->shortenString( $aResult['ItemAttributes']['Title'], 40 );

                    // Set price
                    $temp['price'] = 'Tap To See Price';

                    if( isset( $aResult['OfferSummary']['LowestNewPrice']['FormattedPrice'] ) )
                        $temp['price'] = $aResult['OfferSummary']['LowestNewPrice']['FormattedPrice'];
                    elseif( isset( $aResult['OfferSummary']['LowestCollectiblePrice']['FormattedPrice'] ) )
                        $temp['price'] = $aResult['OfferSummary']['LowestCollectiblePrice']['FormattedPrice'];
                    elseif( isset( $aResult['OfferSummary']['LowestUsedPrice']['FormattedPrice'] ) )
                        $temp['price'] = $aResult['OfferSummary']['LowestUsedPrice']['FormattedPrice'];
                    elseif( isset( $aResult['ItemAttributes']['ListPrice']['FormattedPrice'] ) )
                        $temp['price'] = $aResult['ItemAttributes']['ListPrice']['FormattedPrice'];


                    // Set image
                    $temp['img_small'] = $this->default_img_medium;
                    $temp['img_medium'] = $this->default_img_medium;
                    $temp['img_large'] = $this->default_img_medium;

                    // Small Images
                    if( isset( $aResult['SmallImage']['URL'] ) )
                        $temp['img_small'] = $aResult['SmallImage']['URL'];
                    elseif( isset( $aResult['ImageSets']['ImageSet'][0]['SmallImage']['URL'] ) )
                        $temp['img_small'] = $aResult['ImageSets']['ImageSet'][0]['SmallImage']['URL'];

                    // Medium Images
                    if( isset( $aResult['MediumImage']['URL'] ) )
                        $temp['img_medium'] = $aResult['MediumImage']['URL'];
                    elseif( isset( $aResult['ImageSets']['ImageSet'][0]['MediumImage']['URL'] ) )
                        $temp['img_medium'] = $aResult['ImageSets']['ImageSet'][0]['MediumImage']['URL'];

                    // Large Images
                    if( isset( $aResult['LargeImage']['URL'] ) )
                        $temp['img_large'] = $aResult['LargeImage']['URL'];
                    elseif( isset( $aResult['ImageSets']['ImageSet'][0]['LargeImage']['URL'] ) )
                        $temp['img_large'] = $aResult['ImageSets']['ImageSet'][0]['LargeImage']['URL'];

                    // Set the product url at amazon
                    $temp['item_url'] = $aResult['DetailPageURL'];


                    // Description of this item
                    $temp['description'] = '';

                    if( isset( $aResult['ItemAttributes']['Feature'] ) ){

                        if( is_array( $aResult['ItemAttributes']['Feature'] ) ){

                            foreach( $aResult['ItemAttributes']['Feature'] as $aDescription )
                                $temp['description'] .= $aDescription . '. ';
                        } else {
                            $temp['description'] = $aResult['ItemAttributes']['Feature'];
                        }
                    }

                    array_push( $standarizedResults, $temp );
                } else {                                                                                                                                    
                    // This row is the meta data row about the information like how many items are in the total
                    // result set.  Dont manip this.                                                                                                                                     
                    $isFirstRow = false;                                                                                                                    
                                                                                                                                                        
                    // Add the meta data to the return array                                                                                                
                    array_push( $standarizedResults, $aResult );                                                                                            
                }    
            }
        } else {
            // Error in the results
            $standarizedResults['error'] = $searchResult['Items']['Request']['Errors']['Error']['Message'];
        }

        return $standarizedResults;
    }
    public function google( $searchResults ){

        $standarizedResults = array();

        $isFirstRow = true; // The first row holds meta data for the results set, so skip it

        foreach( $searchResults as $aResult ){

            if( ! $isFirstRow ){

                // Set main title of the product
                $temp['title'] = $aResult['product']['title']; 

                // Set price
                $temp['price'] = $aResult['product']['inventories'][0]['price'];

                // Set image
                $temp['img_medium'] = $this->default_img_medium;

                if( isset( $aResult['product']['inventories'][0] ) )
                    $temp['img_medium'] = $aResult['product']['images'][0]['link'];

                // Set the product url at amazon                                                                                                                        
                $temp['item_url'] = $aResult['product']['link'];

                array_push( $standarizedResults, $temp );

            } else {
                // This row is the meta data row about the information like how many items are in the total                   
                // result set.  Dont manip this. 

                $isFirstRow = false;

                // Add the meta data to the return array
                array_push( $standarizedResults, $aResult );
            }
        }

        return $standarizedResults;

    }
    function shortenString( $string, $length ){

        $returnVar = '';

        if( strlen( $string ) > $length )
            $returnVar = substr( $string, 0 , $length ) . '...';
        else
            $returnVar = $string;

        return $returnVar;
    }
}
