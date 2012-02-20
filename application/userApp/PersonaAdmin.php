<?php
// This class is used for administration of the Persona information

class PersonaAdmin
{
    private $debug;
    private $generic;

    public function __construct(){

        $this->debug = false;

        Zend_Loader::loadClass('Generic');

        $this->generic = new Generic();
    
    }
    public function setDebugTrue(){

        $this->debug = true;
    }
    public function personaTypeAdd( $request_vars ){

        $data['name'] = $request_vars->getParam( 'name' );
        $data['datetime_created'] = 'NOW()';
        $data['datetime_modified'] = 'NOW()';

        $this->generic->save( 'Persona_Types', $data );
    }
    public function personatTypeGetAll(){

        $query = 'SELECT * FROM Persona_Types';

        $results = $this->generic->customQuery( 'Persona_Types', $query );

        return $results;
    }
    public function personaTypeDelete( $request_vars ){

        $a_id_seq = $request_vars->getParam( 'id_seq' );
        $id_seq_name = 'id_seq';

        // Delete persona type row
        $this->generic->remove_noauth( 'Persona_Types', $a_id_seq, $id_seq_name );

        // Delete all the words associated with this persona type also
        $this->generic->remove_noauth( 'Persona_Words', $a_id_seq, 'persona_type_id_seq' );

        // Delete all the products associated with this persona type
        $this->generic->remove_noauth( 'Persona_Type_Products', $a_id_seq, 'persona_type_id_seq' );
    }
    public function wordAdd( $request_vars ){
    
        $inputMethod = $request_vars->getParam( 'method' );

        $data['persona_type_id_seq'] = $request_vars->getParam( 'persona_type_id_seq' );                                 
        $word = $request_vars->getParam( 'word' );                                                               
        $data['weight'] = $request_vars->getParam( 'weight' );                                                           
        $data['datetime_created'] = 'NOW()';                                                                              
        $data['datetime_modified'] = 'NOW()';

        if( $inputMethod == 'one' ){
            // Adding only one word
    
            $data['word'] = $word;

            $this->generic->save( 'Persona_Words', $data );
        }
        if( $inputMethod == 'two' ){
            // Adding a comma separated list of words

            $wordList = preg_split( '/,/', $word );

            print_r( $wordList );

            foreach( $wordList as $aWord ){

                $data['word'] = $aWord;

                $this->generic->save( 'Persona_Words', $data );
            }
        }
    }
    public function wordGetAll( $request_vars ){
    
        $persona_type_id_seq = $request_vars->getParam( 'persona_type_id_seq' );

        $query = 'SELECT * FROM Persona_Words WHERE persona_type_id_seq = ' . $persona_type_id_seq;

        $results = $this->generic->customQuery( 'Persona_Words', $query );

        return $results;
    }
    public function wordDelete( $request_vars ){
    
        $a_id_seq = $request_vars->getParam( 'id_seq' );
        $id_seq_name = 'id_seq';

        $this->generic->remove_noauth( 'Persona_Words', $a_id_seq, $id_seq_name );
    }
    public function productAdd( $request_vars ){

        // Get product via the Amazon ASIN number
        Zend_Loader::loadClass('AmazonSearch');
        Zend_Loader::loadClass('StandardizeProductResults');

        $amazonSearch = new AmazonSearch();
        $standardizeProductResults = new StandardizeProductResults();

        $results = $amazonSearch->search( $request_vars->getParam( 'vendor_product_id' ), 'All', 'Large' );

        // Sending the Amazon through the standardization so that it picks out the correc values for what we want
        // This is useing the top x interest list so have to modify it a bit to fit into this classes structure
        $filteredResults[0] = array();
        $filteredResults[1] = $results['Items']['Item'];

        $standardizedResults = $standardizeProductResults->amazon( $filteredResults );

        $data['persona_type_id_seq'] = $request_vars->getParam( 'persona_type_id_seq' );
        $data['internal_name'] = $request_vars->getParam( 'internal_name' );
        $data['vendor_product_id'] = $request_vars->getParam( 'vendor_product_id' );
        $data['name'] = $standardizedResults[1]['title'];
        $data['price'] = $standardizedResults[1]['price'];
        $data['url'] = $standardizedResults[1]['item_url'];
        $data['description'] = $standardizedResults[1]['description'];
        $data['img_small'] = $standardizedResults[1]['img_small'];;
        $data['img_medium'] = $standardizedResults[1]['img_medium'];
        $data['img_large'] = $standardizedResults[1]['img_large'];;
        $data['is_active'] = $request_vars->getParam( 'is_active' );
        $data['datetime_created'] = 'NOW()';
        $data['datetime_modified'] = 'NOW()';

        $this->generic->save( 'Persona_Type_Products', $data );

    }
    public function productGetAll( $request_vars ){

        $persona_type_id_seq = $request_vars->getParam( 'persona_type_id_seq' );

        $query = 'SELECT * FROM Persona_Type_Products WHERE persona_type_id_seq = ' . $persona_type_id_seq;

        $results = $this->generic->customQuery( 'Persona_Type_Products', $query );

        return $results;
    }
    public function productDelete( $request_vars ){

        $a_id_seq = $request_vars->getParam( 'id_seq' );
        $id_seq_name = 'id_seq';

        $this->generic->remove_noauth( 'Persona_Type_Products', $a_id_seq, $id_seq_name );
    }
}

?>
