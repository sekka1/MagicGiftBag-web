<?php
// This class is used for administration of the Persona information

class Euclidean
{
    private $debug;
    public $critics;

    public function __construct(){

        $this->debug = false;

    
    }
    public function setDebugTrue(){

        $this->debug = true;
    }
    public function setFakeData(){
        
        $this->critics = '{"Lisa Rose":[{"Lady in the Water": 2.5, "Snakes on a Plane": 3.5,"Just My Luck": 3.0, "Superman Returns": 3.5, "You, Me and Dupree": 2.5,"The Night Listener": 3.0}],"Gene Seymour":[{"Lady in the Water": 3.0, "Snakes on a Plane": 3.5,"Just My Luck": 1.5, "Superman Returns": 5.0, "The Night Listener": 3.0,"You, Me and Dupree": 3.5}],"Michael Phillips":[{"Lady in the Water": 2.5, "Snakes on a Plane": 3.0,"Superman Returns": 3.5, "The Night Listener": 4.0}], "Claudia Puig":[{"Snakes on a Plane": 3.5, "Just My Luck": 3.0,"The Night Listener": 4.5, "Superman Returns": 4.0,"You, Me and Dupree": 2.5}], "Mick LaSalle":[{"Lady in the Water": 3.0, "Snakes on a Plane": 4.0,"Just My Luck": 2.0, "Superman Returns": 3.0, "The Night Listener": 3.0,"You, Me and Dupree": 2.0}], "Jack Matthews":[{"Lady in the Water": 3.0, "Snakes on a Plane": 4.0,"The Night Listener": 3.0, "Superman Returns": 5.0, "You, Me and Dupree": 3.5}], "Toby":[{"Snakes on a Plane":4.5,"You, Me and Dupree":1.0,"Superman Returns":4.0}]}';
 
        //print_r( json_decode( $this->critics, 1 ) );

        $this->critics = json_decode( $this->critics, 1 );
    }
    public function twoAxis( $user1_item1, $user1_item2, $user2_item1, $user2_item2 ){

        $sum_power_item1 = pow( $user1_item1-$user2_item1, 2);
        $sum_power_item2 = pow( $user1_item2-$user2_item2, 2);

        $result = 1/( 1 + sqrt( $sum_power_item1 + $sum_power_item2 ) );

        return $result;
    }
    public function pearson( $user1, $user2 ){

        // 1 - find items that both have in common
        $relatedItems = array();

        foreach( $this->critics[$user1][0] as $key=>$anItem ){

            if( array_key_exists( $key, $this->critics[$user2][0] ) )
                array_push( $relatedItems, $key );
        }

        //print_r( $relatedItems );

        // Number of items related between the two users
        $n = count( $relatedItems );

        //echo 'Number of related items: ' . $n . '<br/>';

        // 2 - sum up all the preference of each item the 2 user has in common
        $sum1 = 0;
        $sum2 = 0;
    
        foreach( $relatedItems as $anItem ){

            // User 1
            foreach( $this->critics[$user1][0] as $key=>$val ){
                if( $anItem == $key )
                    $sum1 += $val;
            }
            // User 2
            foreach( $this->critics[$user2][0] as $key=>$val ){
                if( $anItem == $key )
                    $sum2 += $val;
            }
        }

        //echo 'Sum1: ' . $sum1 . ' -- Sum2: ' . $sum2 . '<br/>';

        // 3 - sum up the squares of each item the user has in common
        $sum1Sq = 0;
        $sum2Sq = 0;

        foreach( $relatedItems as $anItem ){

            // User 1
            foreach( $this->critics[$user1][0] as $key=>$val ){
                if( $anItem == $key )
                    $sum1Sq += pow( $val, 2 );
            }
            // User 2
            foreach( $this->critics[$user2][0] as $key=>$val ){
                if( $anItem == $key )
                    $sum2Sq += pow( $val, 2 );
            }
        }

        //echo 'sum1Sq: ' . $sum1Sq . ' ---- sum2Sq: ' . $sum2Sq . '<br/>';

        // 4 - sum up the product of each item the user has in common
        $pSum = 0;

        foreach( $relatedItems as $anItem ){

            $pSum += $this->critics[$user1][0][$anItem] * $this->critics[$user2][0][$anItem];
        }

        //echo 'pSum: ' . $pSum . '<br/>';

        // 5 - calcultat pearson score
        $num = $pSum - ( $sum1 * $sum2 / $n );
        $den = sqrt( ( $sum1Sq - pow( $sum1, 2 ) / $n  ) * ( $sum2Sq - pow( $sum2, 2 ) / $n ) );

        $returnVar = 0;

        if( $den == 0 )
            $returnVar = 0;

        $returnVar = $num / $den;

        //echo 'Pearson score: ' . $returnVar . '<br/>';

        return $returnVar;
    }
}
