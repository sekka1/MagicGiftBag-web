<?php
// This is a generic class that goes out and gets Events from various sources and populates it into the DB


// Various constants
defined('YAHOO_UPCOMMING_API_KEY') or define('YAHOO_UPCOMMING_API_KEY', '3746b0aeb6');
defined('EVENTFUL_API_KEY') or define('EVENTFUL_API_KEY', 'kcvcRpcZQFWxqw5p');

defined('WEAK_KEY') or define('WEAK_KEY', 'keidkcUejd837JeudmWIC39485');

class PopulateEvents
{
    private $apiKey;
    private $generic;
    private $source;
    private $totalResultsCount;
    private $resultsPerPage;

    private $weakAuthKey; // Used so that not just anyone can run this

    private $debug;

    public function __construct(){

        $this->source = '';
        $this->totalResultsCount = 0;
        $this->resultsPerPage = 100;

        $this->weakAuthKey = 'keidkcUejd837JeudmWIC39485';

        $this->debug = false;

        // This user is coming in un authenticated by facebook but has an auth token.                                
        // Using the auth token to grab user's likes for the gift engine to run
        Zend_Loader::loadClass('fbAuth');
        $this->fbAuth = new fbAuth(); 

        // Model for generic database table
        Zend_Loader::loadClass('Generic');
        $this->generic = new Generic();
    }
    public function setDebugTrue(){

        $this->debug = true;
    }
    public function populateDB( $request_vars ){
        // Populates our DB with the most recent stuff

        $this->source = $request_vars->getParam( 'source' );
        $getMoreEvents = true;
        $currentPage = 1;

        if( WEAK_KEY == $request_vars->getParam( 'auth' ) ){

            while( $getMoreEvents ){
                // Keep on paging through the results as long as necessary

                if( $currentPage == 1 || $this->getAnotherPage( $currentPage - 1 ) ){

                    $events = $this->getEventList( $currentPage );
                    $this->getTotalResultCount( $events );

                    foreach( $this->getEventRoot( $events ) as $aEvent ){

                        if( ! $this->isEventInDB( $aEvent ) ){
                            // If it is not in the db, insert

                            $this->addToDB( $aEvent );
                        } else {
                            // Update the entry, dont bother checking just update it all

                            // Need to implement
                        }
                    }

                    $currentPage++;
                }
            }
        }
    }
    public function getAnotherPage( $currentPage ){
        // Checks to see if it should get another page
        // returns boolean

        $getAnotherPage = false;

        if( ( $this->totalResultsCount - ( $currentPage * $this->resultsPerPage ) ) > 0 ){
            $getAnotherPage = true;
        }

        return $getAnotherPage;
    }
    public function getEventList( $page ){

        $url = '';

        if( $this->source == 'upcomming' )
            $url = 'http://upcoming.yahooapis.com/services/rest/?api_key='.YAHOO_UPCOMMING_API_KEY.'&method=event.search&metro_id=2&format=json&page='.$page.'&quick_date=next_60_days';
        if( $this->source == 'eventful' ){
            // Get Next Month
            $d = new DateTime( date('Y-m-d') );
            $d->modify( 'next month' );
            $nextMonth = $d->format( 'F' );

            $url = 'http://api.eventful.com/json/events/search?app_key='.EVENTFUL_API_KEY.'&page_size=1000&page_number='.$page.'&location=san%20francisco&within=1&unit=km&date='.$nextMonth;
        }

        $events = file_get_contents( $url ); 

        return json_decode( $events, 1 );
    }
    public function isEventInDB( $aEvent ){
        // Checks if this event is already in the DB or not
        // returns a boolean

        $isInDB = false;

        $source_uid = '';

        // Check via the source and source_uid
        if( $this->source == 'upcomming' )
            $source_uid = $aEvent['id']; 
        if( $this->source == 'eventful' )
            $source_uid = $aEvent['id'];

        $sql = 'SELECT * FROM Events WHERE source_uid = "' . $source_uid . '"';
echo $sql;
        $results = $this->generic->customQuery( 'Events', $sql );

        if( count( $results ) > 0 ){

            $isInDB = true;
        } 

        return $isInDB;
    }
    public function getTotalResultCount( $events ){

        if( $this->source == 'upcomming' )
            $this->totalResultsCount = $events['rsp']['resultcount'];
        if( $this->source == 'eventful' )
            $this->totalResultsCount = $events['total_items'];
    }
    public function getEventRoot( $events ){
        // Returns the location of the root node so that it can be itterated there to do stuff to it

        $rootNode;

        if( $this->source == 'upcomming' )
            $rootNode = $events['rsp']['event'];
        if( $this->source == 'eventful' )
            $rootNode = $events['events']['event'];

        return $rootNode;
    }
    public function addToDB( $aEvent ){
        // Adds this event into the DB

        $data;

        if( $this->source == 'upcomming' )
            $data = $this->setYahooUpcommingData( $aEvent );
        if( $this->source == 'eventful' )
            $data = $this->setEventfulData( $aEvent );

        $this->generic->save( 'Events', $data );
    }
    public function setYahooUpcommingData( $aEvent ){

        $data['source'] = $this->source;                                                                                                                                            
        $data['source_uid'] = $aEvent['id'];                                                                                                                                        
        $data['venue_id'] = $aEvent['venue_id'];                                                                                                                                    
        $data['title'] = $aEvent['name'];                                                                                                                                           
        $data['venue_name'] = $aEvent['venue_name'];                                                                                                                                
        $data['venue_url'] = '';                                                                                                                                                    
        $data['all_day'] = '';                                                                                                                                                      
        $data['url'] = $aEvent['url'];                                                                                                                                              
        $data['latitude'] = $aEvent['latitude'];                                                                                                                                    
        $data['longitude'] = $aEvent['longitude'];                                                                                                                                  
        $data['address'] = $aEvent['venue_address'];                                                                                                                                
        $data['city_name'] = $aEvent['venue_city'];                                                                                                                                 
        $data['state_name'] = $aEvent['venue_state_name'];                                                                                                                          
        $data['state_code'] = $aEvent['venue_state_code'];                                                                                                                          
        $data['zip'] = $aEvent['venue_zip'];                                                                                                                                        
        $data['country_name'] = $aEvent['venue_country_name'];                                                                                                                      
        $data['country_code'] = $aEvent['venue_country_code'];                                                                                                                      
        $data['phone'] = '';                                                                                                                                                        
        $data['start_time'] = $aEvent['start_date'] . ' ' . $aEvent['start_time'];                                                                                                  
        $data['end_time'] = $aEvent['end_date'] . ' ' . $aEvent['end_time'];                                                                                                        
        $data['date_posted'] = $aEvent['date_posted'];                                                                                                                              
        $data['description'] = $aEvent['description'];                                                                                                                              
        $data['category_id'] = $aEvent['category_id'];                                                                                                                              
        $data['performer_id'] = '';                                                                                                                                                 
        $data['image'] = $aEvent['photo_url'];                                                                                                                                      
        $data['ticket_url'] = $aEvent['ticket_url'];                                                                                                                                
        $data['ticket_price'] = $aEvent['ticket_price'];                                                                                                                            
        $data['ticket_free'] = $aEvent['ticket_free'];                                                                                                                              
        $data['photo_url'] = $aEvent['photo_url'];                                                                                                                                  
        $data['datetime_created'] = 'now()';                                                                                                                                        
        $data['datetime_modified'] = 'now()';                               

        return $data;
    }
    public function setEventfulData( $aEvent ){

        $data['source'] = $this->source;
        $data['source_uid'] = $aEvent['id'];
        $data['venue_id'] = $aEvent['venue_id'];
        $data['title'] = $aEvent['title'];
        $data['venue_name'] = $aEvent['venue_name'];
        $data['venue_url'] = $aEvent['venue_url'];
        $data['all_day'] = $aEvent['all_day'];
        $data['url'] = $aEvent['url'];
        $data['latitude'] = $aEvent['latitude'];
        $data['longitude'] = $aEvent['longitude'];
        $data['address'] = $aEvent['venue_address'];
        $data['city_name'] = $aEvent['city_name'];
        $data['state_name'] = $aEvent['region_name'];
        $data['state_code'] = $aEvent['region_abbr'];
        $data['zip'] = $aEvent['postal_code'];
        $data['country_name'] = $aEvent['country_name'];
        $data['country_code'] = $aEvent['country_abbr'];
        $data['phone'] = '';
        $data['start_time'] = $aEvent['start_time'];
        $data['end_time'] = $aEvent['stop_time'];
        $data['date_posted'] = $aEvent['created'];
        $data['description'] = $aEvent['description'];
        $data['category_id'] = '';
        $data['performer_id'] = '';
        $data['image'] = '';
        $data['ticket_url'] = '';
        $data['ticket_price'] = '';
        $data['ticket_free'] = '';
        $data['photo_url'] = '';
        $data['datetime_created'] = 'now()';
        $data['datetime_modified'] = 'now()';

        return $data;
    }

}
