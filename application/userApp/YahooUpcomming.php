<?php
// Handles the Yahoo Upcomming events

class YahooUpcomming 
{
    private $apiKey;
    private $generic;
    private $source;
    private $totalResultsCount;
    private $resultsPerPage;

    private $weakAuthKey; // Used so that not just anyone can run this

    private $debug;

    public function __construct(){

        $this->apiKey = '3746b0aeb6';
        $this->source = 'upcomming';
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

        $getMoreEvents = true;
        $currentPage = 1;

        if( $this->weakAuthKey == $request_vars->getParam( 'auth' ) ){

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

        $url = 'http://upcoming.yahooapis.com/services/rest/?api_key='.$this->apiKey.'&method=event.search&metro_id=2&format=json&page='.$page.'&quick_date=next_60_days';
        
        $events = file_get_contents( $url ); 

        return json_decode( $events, 1 );
    }
    public function isEventInDB( $aEvent ){
        // Checks if this event is already in the DB or not
        // returns a boolean

        $isInDB = false;

        // Check via the source and source_uid
        $source_uid = $aEvent['id']; 

        $sql = 'SELECT * FROM Events WHERE source_uid = ' . $source_uid;

        $results = $this->generic->customQuery( 'Events', $sql );

        if( count( $results ) > 0 ){

            $isInDB = true;
        } 

        return $isInDB;
    }
    public function getTotalResultCount( $events ){

        $this->totalResultsCount = $events['rsp']['resultcount'];
    }
    public function getEventRoot( $events ){
        // Returns the location of the root node so that it can be itterated there to do stuff to it

        // For Yahoo Upcomming
        return $events['rsp']['event'];

    }
    public function addToDB( $aEvent ){
        // Adds this event into the DB

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

        $this->generic->save( 'Events', $data );
    }

}
