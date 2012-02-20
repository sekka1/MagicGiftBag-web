<?php
// This class helps classifying a user's Facebook Likes, Profile info, etc to help determine
// what is this user's top interests are

/////////////////////////
// Weight Constants
/////////////////////////
define('ARTS_ENTERTAINMENT_NIGHTLIFE', '0.3');
define('ARTS_HUMANITIES', '1');
define('ACTOR_DIRECTOR', '1');
define('ALBUM', '1');
define('AMATEUR_SPORTS_TEAM', '1');
define('ANIMAL', '0.5');
define('APPLICATION', '0.1');
define('ARTIST', '1');
define('ATHLETE', '1.5');
define('ATTRACTIONS_THINGS_TO_DO', '1');
define('AUTHOR', '1');
define('AUTOMOBILES_AND_PARTS', '1');
define('BAGS_LUGGAGE', '1');
define('BAR', '0.1');
define('BOOK', '0.2');
define('BOOK_GENRE', '0.2');
define('CARS', '1');
define('CAUSE', '1');
define('CHEF', '2');
define('CHURCH_RELIGIOUS_ORGANIZATION', '0.5');
define('CITY', '0.3');
define('CLOTHING', '8');
define('CLUB', '0.1');
define('COMEDIAN', '1');
define('COMPANY', '1');
define('COMPUTERS_INTERNET', '1');
define('COMPUTERS_TECHNOLOGY', '1');
define('COMMUNITY', '1');
define('CONCERT_TOUR', '0.1');
define('CONCERT_VENUE', '0.1');
define('CONSULTING_BUSINESS_SERVICES', '1');
define('DANCER', '1');                                                                                                                          
define('EDITOR', '1');                                                                                                                          
define('EDUCATION', '0.5');                                                                                                                     
define('ENTERTAINMENT', '0.7');                                                                                                                 
define('ENTERTAINER', '0.7');                                                                                                                   
define('FICTIONAL_CHARACTER', '1');                                                                                                             
define('FIELD_OF_STUDY', '1');                                                                                                                  
define('FOOD_BEVERAGES', '0.2');                                                                                                                
define('FOOD_GROCERY', '0.2');                                                                                                                  
define('GAMES_TOYS', '5');                                                                                                                      
define('GOVERNMENT_OFFICIAL', '0.5');                                                                                                           
define('GOVERNMENT_ORGANIZATIONS', '0.5');                                                                                                      
define('HEALTH_BEAUTY', '1');                                                                                                                   
define('HEALTH_MEDICAL_PHARMACY', '1');                                                                                                         
define('HEALTH_WELLNESS', '1');                                                                                                                 
define('HOME_DECOR', '1');                                                                                                                      
define('HOME_GARDEN', '1');                                                                                                                     
define('HOSPITAL_CLINIC', '1');                                                                                                                 
define('HOTEL', '1');                                                                                                                           
define('INTEREST', '1');                                                                                                                        
define('INTERNET_SOFTWARE', '1'); 
define('JEWELRY_WATCHES', '8');                                                                                                                 
define('KITCHEN_COOKING', '1');                                                                                                                 
define('LANDMARK', '0.7');                                                                                                                      
define('LIBRARY', '0.7');                                                                                                                       
define('LOCAL_BUSINESS', '0.1');                                                                                                                
define('LOCAL_TRAVEL', '0.1');                                                                                                                  
define('MEDIA_NEWS_PUBLISHING', '0.7');                                                                                                         
define('MOVIE', '0.3');                                                                                                                         
define('MOVIE_GENRE', '0.3');                                                                                                                   
define('MOVIES_MUSIC', '0.3');                                                                                                                  
define('MUSEUM_ART_GALLERY', '1');                                                                                                              
define('MUSIC', '0.1');                                                                                                                         
define('MUSICIAN_BAND', '0.1');                                                                                                                 
define('MUSICAL_GENRE', '0.1');                                                                                                                 
define('NEWS_MEDIA', '1');                                                                                                                      
define('NON_PROFIT_ORGANIZATION', '1');                                                                                                         
define('OFFICE_SUPPLIES', '1');                                                                                                                 
define('ORGANIZATION', '0.1');                                                                                                                  
define('OUTDOOR_GEAR_SPORTING_GOODS', '6');                                                                                                     
define('PET_SERVICES', '0.5');                                                                                                                  
define('PET_SUPPLIES', '0.5');                                                                                                                  
define('PERSONAL_BLOG', '0.5');                                                                                                                 
define('PERSONAL_WEBSITE', '0.2');                                                                                                              
define('POLITICAL_ORGANIZATION', '0.5');                                                                                                        
define('POLITICIAN', '0.5');                                                                                                                    
define('PRODUCER', '1');                                                                                                                        
define('PRODUCT_SERVICE', '5');                                                                                                                 
define('PROFESSIONAL_SERVICES', '0.6');                                                                                                         
define('PROFESSIONAL_SPORTS_TEAM', '4');                                                                                                        
define('PUBLIC_FIGURE', '1');                                                                                                                   
define('PUBLIC_FIGURES', '1');                                                                                                                  
define('RADIO_STATION', '1');                                                                                                                   
define('REAL_ESTATE', '1');                                                                                                                     
define('RECORD_LABEL', '1');                                                                                                                    
define('RECREATION_SPORTS', '1');                                                                                                               
define('RESTAURANT_CAFE', '0.1');                                                                                                               
define('RETAIL_AND_CONSUMER_MERCHANDISE', '5');                                                                                                 
define('SCHOOL', '0.6');                                                                                                                        
define('SCHOOL_SPORTS_TEAM', '0.6');                                                                                                            
define('SHOPPING_RETAIL', '1');                                                                                                                 
define('SMALL_BUSINESS', '1');                                                                                                                  
define('SOCIETY_CULTURE', '1');                                                                                                                 
define('SOFTWARE', '1');                                                                                                                        
define('SONG', '1');                                                                                                                            
define('SPAS_BEAUTY_PERSONAL_CARE', '1');                                                                                                       
define('SPORT', '1');                                                                                                                           
define('SPORTS_LEAGUE', '1');                                                                                                                   
define('SPORTS_RECREATION_ACTIVITIES', '5');                                                                                                    
define('STUDIO', '1');                                                                                                                          
define('TEACHER', '1');                                                                                                                         
define('TEENS_KIDS', '1');                                                                                                                      
define('TELECOMMUNICATION', '0.2');                                                                                                             
define('TRAVEL_LEISURE', '1');                                                                                                                  
define('TOURS_SIGHTSEEING', '0.5');                                                                                                             
define('TV', '1');                                                                                                                              
define('TV_NETWORK', '1');                                                                                                                      
define('TV_SHOW', '0.3');         
define('UNIVERSITY', '1');                                                                                                                      
define('UNKNOWN', '0.5');                                                                                                                       
define('WEBSITE', '0.5');                                                                                                                       
define('WINE_SPIRITS', '1');                                                                                                                    
define('WRITER', '1');    

class FacebookLikes
{

    private $getXCategories = 10; // Get the top x category                                                                            
    private $getXForeachCategory = 4; // For each category get the top hits  

    private $debug;

    public function __construct(){

        $this->debug = false;
    }
    public function setDebugTrue(){

        $this->debug = true;
    }
    public function getFriendsList( $facebook_api ){

        // Get Friends List
        $user_friendslist = $facebook_api->api('/me/friends');

        return  $user_friendslist;

    }
    public function getFacebookLikeData( $facebook_api_session, $user_id ){
        // This fuction will get the various data that we want to use in making recommendations
        //
        // INPUT: $facebook_api_session = usually the $this->fbAuth->facebook variable that already has the FB session
        //        $user_id = FB user id of the user we want to pull on

        $isLessThanXLikes = false; // Only search the user profile if there are less than X likes
        $numberofLikes = 5;

        // Getting a list of this user's likes
        $users_likesList = $facebook_api_session->api('/'.$user_id.'/likes');

        if( count( $users_likesList['data'] ) < $numberofLikes ){
            $isLessThanXLikes = true;
        }

        if( $isLessThanXLikes ){
            $users_tv = $facebook_api_session->api('/' . $user_id . '/television');
            $users_movies = $facebook_api_session->api('/' . $user_id . '/movies');
            $users_books = $facebook_api_session->api('/' . $user_id . '/books');
            $users_music = $facebook_api_session->api('/' . $user_id . '/music');
            $users_activities = $facebook_api_session->api('/' . $user_id . '/activities');
        }

//        echo '<h3>Likes</h3>';
//        print_r( $users_likesList );
//        print_r( $users_tv );

        $main_list['data'] = array(); // An Arry holding all Likes items

        // Combining various interest lists
        if( $isLessThanXLikes ){
            $main_list = $this->combineFBInterestResponses( $main_list, $users_activities );
            $main_list = $this->combineFBInterestResponses( $main_list, $users_music );
            $main_list = $this->combineFBInterestResponses( $main_list, $users_tv );
            $main_list = $this->combineFBInterestResponses( $main_list, $users_movies );
            $main_list = $this->combineFBInterestResponses( $main_list, $users_books );
        }
        $main_list = $this->combineFBInterestResponses( $main_list, $users_likesList );

        return $main_list;
    }

    public function combineFBInterestResponses( $main_list, $users_response_list ){
        // This function combines the list of responses returned by a FB graph call to:
        // likes, television, etc
        //
        // All these responses comes in one format and it all can be combined in one big list
        //
        // Input: $main_list = an array where everything is going into
        //        $users_response_list = this is the new array list to add to the main list

        foreach( $users_response_list['data'] as $anItem ){

            array_push( $main_list['data'], $anItem );
        }

        return $main_list;

    }

    public function sumUpLikesCategories( $users_likesList ){
        // Organizes all the likes into Categories and the array spot that this category shows up in the user's Like list
        // Puts each Like's original array position into an associative array with the category name

        $categoryListNames = array(); // List of category names found
        $categoryArraySpot = array(); // List of which array spot each category is in the $users_likesList

        // Tally all the category and which array spot belongs in each category
        for( $i = 0; $i < count( $users_likesList['data'] ); $i++ ){

            //if( checkNotInOmitCategory( $users_likesList['data'][$i]['category'] ) ){

            if( in_array( $users_likesList['data'][$i]['category'], $categoryListNames ) ){
                // Already found it once.  Put it into the $categoryArraySpot

                array_push( $categoryArraySpot[$users_likesList['data'][$i]['category']], $i );
            } else {
                // First time seeing this category

                array_push( $categoryListNames, $users_likesList['data'][$i]['category'] );
                $categoryArraySpot[$users_likesList['data'][$i]['category']][0] = $i;

            }
            //}
        }

        //print_r( $categoryListNames );
        //print_r( $categoryArraySpot );

        return $categoryArraySpot;
    }

    public function sortTopCategories( $categoryArraySpot ){                                                                                      
        // Takes in the array output from sumUpLikesCategories() and it counts how many items are in each category.                        
        // It returns an associative array with the category name and a number for the count of how many likes were in that category       
        // It is also sorted with the highest first                                                                                        

        $categoryCountList = array();                                                                                                      

        // Count up how many times each category name shows up                                                                             
        foreach( $categoryArraySpot as $key => $aCategory ){                                                                               

            $categoryCountList[$key] = $count = count( $aCategory );                                                                       
        }                                                                                                                                  

        arsort( $categoryCountList );                                                                                                      
        //print_r( $categoryCountList );                                                                                                   

        return $categoryCountList;                                                                                                         

    }                                     

    public function getTopXCategorySearchTerms( $topCategorySorted, $likesCategorized, $users_likesList ){                                        
        // Given a the sorted list of top categories and the $likesCategorized of where each like is in the                                    
        // $users_likesList array this will                                                                                                    
        // Return an array with the top Likes and the "name"/value for each like                                                               

        $topCategoriesHits = array(); // Holds the category and the search name for the top X                                      
        $i=0;                                                                                                                      

        // Go through the top category list which holds the categorized list of likes                                              
        foreach( $topCategorySorted as $key=>$val ){                                                                               

            // Show the top 5 categories                                                                                           
            if( $i < $this->getXCategories ){                                                                                            
                $n = 0;                                                                                                            

                // Get the array spots that this category is in                                                                    
                foreach( $likesCategorized[$key] as $arraySpot ){                                                                  

                    // Only show the top 3 for each category                                                                       
                    if( $n < $this->getXForeachCategory ){                                                                               

                        array_push( $topCategoriesHits, $users_likesList['data'][$arraySpot] );                                    
                    }                                                                                                              

                    $n++;                                                                                                          
                }                                                                                                                  
            }                                                                                                                      

            $i++;                                                                                                                  
        }                                                                                                                          

        //print_r( $topCategoriesHits );                                                                                           

        return $topCategoriesHits;                                                                                                 
    }                                                           

    public function filter_weightCategory( $topCategorySorted ){                                                                                  
        // This filter takes in an output array from sortTopCategories() 
        // [Musician/band] => 86                                                                                                               
        // [Movie] => 23                                                                                                                       
        // For each category the value is just multiplied by some value to get a                                                               
        // new value.                                                                                                                          

        foreach( $topCategorySorted as $key=>$val ){                                                                                       

            $tmp_str = str_replace( ' ', '_', $key );                                                                                      
            $tmp_str = str_replace( '/', '_', $tmp_str );                                                                                  
            $tmp_str = str_replace( '-', '_', $tmp_str );                                                                                  
            $tmp_str = strtoupper( $tmp_str );                                                                                             

            if( $this->debug )
                $topCategorySorted[$key] = $val * constant( $tmp_str );                                                                        
            else
                @$topCategorySorted[$key] = $val * constant( $tmp_str );
                // Dont show errors if debug is not turned on
        }                                                                                                                                  

        // Re-sort the array before returning it.  The order might change                                                                  
        arsort( $topCategorySorted );                                                                                                      

        return $topCategorySorted;                                                                                                         

    }                          

    public function manipulate_augmentSearchTerm( $topCategoriesHits ){                                                                           
        // Input: getTopXCategorySearchTerms()
        //
        // Will look through the result set and for certain category a word will be added to the name of the like to narrow the search down more.
        //                                                                                                                                     
        // Takes in the return array from getTopXCategorySearchTerms                                                                           

        // Loop through each like and see if we need to augment the search term (aka 'name' field) value                                         
        // This is based on if the category is X then add this to the search term (aka 'name' field)
        for( $i = 0; $i < count( $topCategoriesHits ); $i++ ){                                                                             

            if( $topCategoriesHits[$i]['category'] == 'Record label' ){ $topCategoriesHits[$i]['name'] = 'music ' . $topCategoriesHits[$i]['name']; }    
            if( $topCategoriesHits[$i]['category'] == 'Movie' ){ $topCategoriesHits[$i]['name'] = 'movie ' . $topCategoriesHits[$i]['name']; }
        }

        // Change the search term (aka 'name' field in likes) to our pre-determined list
        $topCategoriesHits = $this->augmentLikesNameField( $topCategoriesHits );

        return $topCategoriesHits;                                                                                                         
    }                                   
    public function filter_hotWords( $allLikes ){
    // This function looks the entire Like list and looks for hot words.  These hot words we have determined that there is a 1:1 corelation to a
    // User liking this product.  So if we find a "hot word" in the list we will return a product for it.
    //
    // INPUT: $this->getFacebookLikeData()
    // OUTPUT: 

        $hotProducts = array();

        foreach( $allLikes['data'] as $aLike ){

            if( strtoupper( $aLike['name'] ) == 'SNOWBOARDING' ){ array_push( $hotProducts, 'product url or description or amazon result set' ); }
        }

        return $hotProducts;
    }
    public function augmentLikesNameField( $topCategoriesHits ){

        // Loop throug each like and see if there is a word manip for the search term (aka 'name' field) 
        Zend_Loader::loadClass('EngineTuning');

        $engineTuning = new EngineTuning();

        $listContent = json_decode( $engineTuning->getFile( 'augmentSearchTerm' ), 1 );

        for( $i = 0; $i < count( $topCategoriesHits ); $i++ ){

            foreach( $listContent as $aContent ){

                if( strtolower( $topCategoriesHits[$i]['name'] ) == strtolower( $aContent['orignal_text'] ) ){
                    // Found a match that needs to be replaces

                    $topCategoriesHits[$i]['name'] = $aContent['change_to'];
                }
            }
        }

        return $topCategoriesHits;
    } 
}

?>
