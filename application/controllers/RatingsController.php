<?php
class RatingsController extends Zend_Controller_Action
{
    private $fbAuth;

     public function init()
     {
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
     public function indexAction(){

        // Pass the facebook object for the view to use                                                     
        $this->view->facebook = $this->fbAuth->facebook;
     }
     public function rateAction(){

        // Pass the facebook object for the view to use                                                     
        $this->view->facebook = $this->fbAuth->facebook;

        $this->view->parentId = $this->_request->getParam( 'parentID' );
        
        $this->view->currentId = $this->_request->getParam( 'currentID' );

     }
     public function addAction(){

        // Pass the facebook object for the view to use                                                     
        $this->view->facebook = $this->fbAuth->facebook;

        $this->view->parent_id_seq = $this->_request->getParam( 'parent_id_seq' );

    }
    public function geteventsAction(){

        // Yahoo Venues
        //$yahoo_venues = file_get_contents( "http://upcoming.yahooapis.com/services/rest/?api_key=3746b0aeb6&method=venue.getList&metro_id=2&format=json" );
        //$this->view->yahoo_venues = json_decode( $yahoo_venues, 1 );

        // Eventful Venues
        //$eventful_venues = file_get_contents( "http://api.eventful.com/json/venues/search?app_key=kcvcRpcZQFWxqw5p&page_size=1000&page_number=1&location=san%20francisco,%20ca&within=1&unit=km&sort_order=relevance" );
        //$eventful_venues =  file_get_contents( "http://api.eventful.com/json/venues/search?app_key=kcvcRpcZQFWxqw5p&page_size=1000&page_number=1&location=san%20francisco,%20ca&keywords=dna" );
        //$this->view->eventful_venues = json_decode( $eventful_venues, 1 );


        // Yahoo Events
        //$yahoo_events = file_get_contents( "http://upcoming.yahooapis.com/services/rest/?api_key=3746b0aeb6&method=event.search&metro_id=2&format=json&quick_date=next_60_days" );
        //$this->view->yahoo_events = json_decode( $yahoo_events, 1 );

        // Eventful Events
        //$eventful_events = file_get_contents( "http://api.eventful.com/json/events/search?app_key=kcvcRpcZQFWxqw5p&page_size=1000&page_number=1&location=san%20francisco&within=1&unit=km&date=january" );
        //$this->view->eventful_events = json_decode( $eventful_events, 1 );

    }
}
