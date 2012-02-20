<?php
////////////////////////////////////////////
// This class helps facilitates the manipulation of the word augmentors
//
////////////////////////////////////////////

// Files to manip
defined('AUGMENT_SEARCH_TERM') or define('AUGMENT_SEARCH_TERM', '/usr/local/zend/apache2/htdocs/magicgiftbag.com/application/configs/augmentSearchTerms.txt');
defined('PERSONA_TYPE') or define('PERSONA_TYPE', '/usr/local/zend/apache2/htdocs/magicgiftbag.com/application/configs/personaType.txt');
defined('PERSONA_PRODUCTS') or define('PERSONA_PRODUCT', '/usr/local/zend/apache2/htdocs/magicgiftbag.com/application/configs/personaProducts.txt');

class EngineTuning
{
    public $listContent;

    public function __construct( ){

        $this->listContent = '';
    }
    public function getFile( $tuneFile ){

        $filename = '';

        // Selete a file
        if( $tuneFile == 'augmentSearchTerm' )
            $filename = AUGMENT_SEARCH_TERM;
        if( $tuneFile == 'personaType' )
            $filename = PERSONA_TYPE;
        if( $tuneFile == 'personaProducts' )
            $filename = PERSONA_PRODUCT;

        // Open and return file content
        if( $filename != '' ){

            $handle = fopen($filename, "rb");

            $this->listContent = fread($handle, filesize($filename));

            fclose($handle);
        }

        return $this->listContent;
    }
    public function putFile( $tuneFile, $content_array ){

        $filename = '';

        // Selete a file
        if( $tuneFile == 'augmentSearchTerm' )
            $filename = AUGMENT_SEARCH_TERM;
        if( $tuneFile == 'personaType' )
            $filename = PERSONA_TYPE;

        // Open and return file content
        if( $filename != '' ){

            if( is_writable( $filename ) ){

                $handle = fopen($filename, 'w');

                array_multisort( $content_array );

                $content = json_encode( $content_array );

                fwrite( $handle, $content );

                fclose( $handle );
            } 
        }
        
    }
    public function addWordManip( $manip, $original_text, $manipulated_text ){

        if( $manip == 'augmentSearchTerm' ){
            // This manip replace the search term

            $content = $this->getFile( $manip );

            $content_array = json_decode( $content, 1 );

            $temp['orignal_text'] = $original_text;
            $temp['change_to'] = $manipulated_text;

            array_push( $content_array, $temp );

            $this->putFile( 'augmentSearchTerm', $content_array );     
        }
        if( $manip == 'category' ){
            // This manip adds to the search term


        }
    }
    public function deleteWordManip( $manip, $spotToDelete ){

        if( $manip == 'augmentSearchTerm' ){

            $content = $this->getFile( $manip );

            $content_array = json_decode( $content, 1 );

            $new_content_array = array();

            for( $i=0; $i < count( $content_array ); $i++ ){

                if( $i != $spotToDelete ){

                    array_push( $new_content_array, $content_array[$i] );
                }
            }

            $this->putFile( $manip, $new_content_array );
        }
    }

}

?>
