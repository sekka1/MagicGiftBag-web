<?php
// This class gets the list of events or users that is invited to this event

class InvitedList
{

public function getFacebookInvitedList( $request_vars ){
// This function gets the events that the facebook user is invited to

	$facebook_user_uid = $request_vars->getParam( 'uid' );

	if( is_numeric( $facebook_user_uid ) ){

		Zend_Loader::loadClass('Generic');

		$generic_db = new Generic();

		// Get list of event_id_seq that the user is invited to
		$results = $generic_db->customQuery( 'facebook_invite_list', 'SELECT event.event_id_seq, event.user_id_seq, event.name, event.description FROM facebook_invite_list, event WHERE event.event_id_seq = facebook_invite_list.event_id_seq AND facebook_invite_list.invited_user_fb_uid = ' . $facebook_user_uid );

		return json_encode( $results );

	}
}
public function getMyEvents( $request_vars ){
// This function returns the events that this user_id_seq owns

	$facebook_user_uid = $request_vars->getParam( 'uid' );

	if( is_numeric( $facebook_user_uid ) ){

                Zend_Loader::loadClass('Generic');

                $generic_db = new Generic();

                // Get list of event_id_seq that the user is invited to
                $results = $generic_db->customQuery( 'event', 'SELECT event.event_id_seq, event.user_id_seq, event.name, event.description FROM event WHERE user_id_seq="' . $facebook_user_uid . '"' );

                return json_encode( $results );
        }
}
public function getLoginEventList( $request_vars ){
// This function combines the events that the user_id_seqs owns and is invited to.  Puts the ones that this users owns first

	$all_results = array();

	$invited_list = $this->getFacebookInvitedList( $request_vars );

	$my_events = $this->getMyEvents( $request_vars );

	$invited_list_array = json_decode( $invited_list, 1 );
	$my_events_array = json_decode( $my_events, 1 );

	if( count( $my_events_array ) > 0 ){
		foreach( $my_events_array as $anArray ){
			array_push( $all_results, $anArray );
		}
	}
	if( count( $invited_list_array ) > 0 ){
		foreach( $invited_list_array as $anArray ){
			array_push( $all_results, $anArray );
		}
	}

	return json_encode( $all_results );

}
public function getAllInvitedUsersToThisEvent( $request_vars ){
// Get a list of users that is invited to this event.  This is originally used for the event owner's facebook invite page.  So that it can display who is invited already

	$event_id_seq = $request_vars->getParam( 'id' );

	$returnVar = '';

	if( is_numeric( $event_id_seq ) ){

		Zend_Loader::loadClass('Generic');

                $generic_db = new Generic();

		$results = $generic_db->customQuery( 'facebook_invite_list', 'SELECT * FROM facebook_invite_list WHERE event_id_seq=' . $event_id_seq );

		$returnVar = $results;
	}

	return json_encode( $returnVar );
}

}

?>
