<?php
// This class retrieves products for a given persona type

class PersonaProducts
{
    private $debug;

    private $personaProducts;

    public function __construct(){

        $this->debug = false;

    }
    public function setDebugTrue(){

        $this->debug = true;
    }
    public function search( $request_vars ){
        // This is really not a search but a get.  Just naming it search for public facing reasons
        // This gets the persona type passed in and returns the products for it

        $returnVar = array();

        $persona = $request_vars->getParam( 'persona' );

        // Save the feed to our database
        Zend_Loader::loadClass('SaveData');
        $saveData = new SaveData();
        $saveData->saveProductQueries( 'persona', '', $persona );

        $this->personaProducts = $this->getPersonaProducts( $persona ); 

        //print_r( $this->personaProducts );

        if( count( $this->personaProducts ) > 0 )
            $returnVar = json_encode( $this->personaProducts );

        return $returnVar;
    }
    private function getPersonaProducts( $personaType ){

        Zend_Loader::loadClass('Generic');

        $this->generic = new Generic();

        //$personaType = mysql_real_escape_string( $personaType );

        if( $personaType == '' )
           $personaType = 'Foodie';

        $query = 'SELECT Persona_Type_Products.name as title, 
                    Persona_Type_Products.price, 
                    Persona_Type_Products.url as item_url,
                    Persona_Type_Products.img_small, 
                    Persona_Type_Products.img_medium, 
                    Persona_Type_Products.img_large, 
                    Persona_Type_Products.description 
                FROM Persona_Type_Products, 
                    Persona_Types 
                WHERE Persona_Types.id_seq = Persona_Type_Products.persona_type_id_seq 
                    AND Persona_Type_Products.is_active = 1 
                    AND Persona_Types.name = "' . $personaType . '"';

        $results = $this->generic->customQuery( 'Persona_Type_Products', $query );

        // Temporary - Pushing another spot to the top of array b/c the app starts the for loop at 1 instead of 0
        if( count( $results ) > 0 )
            array_unshift( $results, $results[0] );

        return $results;
    }
}

?>
