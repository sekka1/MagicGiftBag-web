<?php
// This class uses the Amazon ECS API to perform searches against amazon's product category
//
//  It also takes in the user's key and associate tags that will get credit for this click through and buy

// For Amazon API
defined('AWS_API_KEY') or define('AWS_API_KEY', 'AKIAJO6OOIFG3LCMZPGA');
defined('AWS_API_SECRET_KEY') or define('AWS_API_SECRET_KEY', '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');
defined('AWS_ASSOCIATE_TAG') or define('AWS_ASSOCIATE_TAG', 'wedvite-20');

require '/usr/local/zend/apache2/htdocs/magicgiftbag.com/application/userApp/AmazonECS.class.php';

class AmazonSearch {

    private $amazonEcs; // Amazon ECS search object
    private $debug;

    public function __construct(){

        $this->debug = false;

        try{
            // get a new object with your API Key and secret key. Lang is optional.                                              
            // if you leave lang blank it will be US.
            $this->amazonEcs = new AmazonECS(AWS_API_KEY, AWS_API_SECRET_KEY, 'com', AWS_ASSOCIATE_TAG);

            // from now on you want to have pure arrays as response                                                              
            $this->amazonEcs->returnType(AmazonECS::RETURN_TYPE_ARRAY); 
        }
        catch(Exception $e)
        {
            echo $e->getMessage();
        }
    }
    public function setDebugTrue(){
        
        $this->debug = true;
    }
    
    public function search( $searchTerm, $category, $responseGroup ){
        // Uses the Amazon search APi to search
        // Input: $searchTerm = text of something to search
        //        $category = All, or one of the categories avaialble
        //        $responseGroup = Large, Small, etc    


        $response;

        try{

            // Make querie to amazon for this search item
            $response = $this->amazonEcs->country('com')->category($category)->responseGroup($responseGroup)->search($searchTerm);
        }
        catch(Exception $e)
        {
            echo $e->getMessage();
        } 

        return $response;
    }


}


?>
