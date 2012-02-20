<?php
// This class determines what persona type a user is based on data that is given it

class PersonaType
{
    private $personaCategory; // An array holding the various persona categories we define

    private $debug;

    public function __construct(){

        $this->debug = false;

        $this->personaCategory = $this->getPersonaWordLists();
    }
    public function setDebugTrue(){

        $this->debug = true;
    }
    private function getPersonaWordLists(){

        Zend_Loader::loadClass('Generic');

        $this->generic = new Generic();

        $query = 'SELECT Persona_Types.name as persona_name, Persona_Words.word, Persona_Types.id_seq as persona_types_id_seq
                    FROM Persona_Types, Persona_Words  
                    WHERE Persona_Types.id_seq = Persona_Words.persona_type_id_seq';

        $results = $this->generic->customQuery( 'Persona_Types', $query );


        $returnArray = $this->organizeWordList( $results );

        return $returnArray;
    }
    private function organizeWordList( $wordListArray ){
        // Organize the word list soo that the functions below can use it
        // Input is from the DB query for all the word lists

        $returnWordList = array();

        foreach( $wordListArray as $anItem ){

            if( array_key_exists( $anItem['persona_name'], $returnWordList ) ){
                // The persona_name is already in the array.  push word to it

                array_push( $returnWordList[$anItem['persona_name']], $anItem['word'] );
            } else {
                // The persona_name is not in the array create it and put this word in it

                $returnWordList[$anItem['persona_name']] = array();

                array_push( $returnWordList[$anItem['persona_name']], $anItem['word'] );
            }
        } 
        
        return $returnWordList;
    }
    public function getPersonaCounts( $feed ){

        $personaCount = array();

        if( $this->debug )
            print_r( $this->personaCategory );


        foreach( $this->personaCategory as $key => $aPersonaCategory ){
            // Loop through each of the persona category - foodie, sprots, tech, etc

            foreach( $aPersonaCategory as $aPersonaTerm ){
                // For each of the terms inside the category loop through them to see if that terms comes up in the user's
                // feed.  If it does then put it in the $personaCount list

                $count = preg_match_all( "/$aPersonaTerm/", $feed, $matches );

                if( $count > 0 ){
                    // Found this term in the users feed

                    if( $this->debug )
                        echo '<br/>persona term term found: ' . $aPersonaTerm . '<br/>';

                    // Add to the $personaCount
                    if( isset( $personaCount[$key] ) )
                        $personaCount[$key] += 1;
                    else
                        $personaCount[$key] = 1;
                    
                }
            }
        }

        // Sort array by highest first
        arsort( $personaCount );
        
        if( $this->debug )
            print_r( $personaCount );

        return $personaCount;
    }

}

?>
