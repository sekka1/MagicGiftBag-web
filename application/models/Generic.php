<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4 foldmethod=marker: */

/*
* This is a generic class to insert, update, delete, etc from any table in the data base
* in a generic method.
*/

class Generic extends Zend_Db_Table_Abstract
{

	protected $_name = 'clients';//table name.  Have to init to a real table

//{{{ Save
    public function save( $tablename, $data ){
    //this is a generic save option into a table.  The table name has to be given
    //saves one row.

    //$data array format
    //Array ( [campaign_name] => 'some name', [campaign_method] => 'some method' ..... )
    //the array can hold any names that is in the table

        $this->_name = $tablename;

        // Fix datetime_created and datetime_modified so that it has the current time
        $data = $this->changeNOWtoCurrentTime( $data );

        $a_id_seq = $this->insert( $data );

        return $a_id_seq;
    }
//}}}
//{{{ edit
    public function edit( $tablename, $a_id_seq, $user_id_seq, $data, $id_seq_name ){
    //this is a generic update option for a table.
    //tablename = the name of the table you want to edit
    //$a_id_seq = the id seq of the row that you want to update
    //$data = in the format same as the save
    //$id_seq_name = the name of the id_seq beint used.  This name will be in the where clause to make sure it matches

        $this->_name = $tablename;

        // Fix datetime_created and datetime_modified so that it has the current time
        $data = $this->changeNOWtoCurrentTime( $data );

        $where = $id_seq_name . ' = ' . $a_id_seq . ' AND user_id_seq = ' . $user_id_seq;

        $return_id_seq = $this->update( $data, $where );

        return $return_id_seq;
    }
//}}}
//{{{ edit_noauth
    public function edit_noauth( $tablename, $a_id_seq, $data, $id_seq_name ){
    // This is the same generic update function but without the client_id_seq and user_id_seq

        $this->_name = $tablename;

       $where = $id_seq_name . ' = ' . $a_id_seq;

        $return_id_seq = $this->update( $data, $where );

        return $return_id_seq; 
    }
//}}}
//{{{ delete
    public function remove( $tablename, $a_id_seq, $user_id_seq, $id_seq_name ){

        $this->_name = $tablename;

        $where = ' user_id_seq = ' . $user_id_seq . ' AND ' . $id_seq_name . ' = ' . $a_id_seq;

        $return_id_seq = $this->delete( $where );

        return $return_id_seq;
    }
//}}}
//{{{ delete_noauth
    public function remove_noauth( $tablename, $a_id_seq, $id_seq_name ){

        $this->_name = $tablename;

        $where = $id_seq_name . ' = ' . $a_id_seq;

        $return_id_seq = $this->delete( $where );

        return $return_id_seq;
    }
//}}}
//{{{ getOne
    public function getOne( $tablename, $client_id_seq, $user_id_seq, $id_seq_name, $a_id_seq ){

        $this->_name = $tablename;

        $this->_setupMetadata();//reloads the table def after the name changed.

        $where = 'client_id_seq = ' . $client_id_seq . ' AND user_id_seq = ' . $user_id_seq . ' AND ' . $id_seq_name . ' = ' . $a_id_seq;

        $allRecords = $this->fetchAll( $where );
///print_r( $allRecords );
        $outputArray = array();

        if( count( $allRecords ) ){

            foreach( $this->_cols as $col ){

                $outputArray[$col] = $allRecords->current()->$col;
            }
        }

        return $outputArray;
    }
//}}}
//{{{ getAll
    public function getAll( $tablename, $client_id_seq, $user_id_seq, $dir = null, $limit = null, $sortOn = null, $start = null ){
    //does pagination and sorting
    
    //Pulls out all the records in the given table for the records that matches the client_id_seq and user_id_seq

        $this->_name = $tablename;

        $this->_setupMetadata();//reloads the table def after the name changed.

        //$where = 'client_id_seq = ' . $client_id_seq . ' AND '. $id_seq_name.' =' . $a_id_seq;
        $where = 'client_id_seq = ' . $client_id_seq . ' AND user_id_seq = ' . $user_id_seq;

        if( $sortOn != null )
                        $order = $sortOn . ' ' . $dir;
                else
                        $order = '';

                if( $limit != null )
                        $count = $limit;
                else
                        $count = '';

                if( $start != null )
                        $offset = $start;
                else
                        $offset = '';

        $allRecords = $this->fetchAll( $where, $order, $count, $offset );

        $outputArray = array();

        //$this->_cols is an array holding all the column names

        //loop through the returned data set and put it into an array for the return value
        foreach( $allRecords as $val ){

            $temp = array();

            foreach( $this->_cols as $col ){

                $temp[$col] = $val->$col;
            }    

            array_push( $outputArray, $temp );
        }

        return $outputArray;

    }
//}}}
//{{{ customQuery
    public function customQuery( $tablename, $query ){

        $this->_name = $tablename;
 
        $this->_setupMetadata();//reloads the table def after the name changed.

        $stmt = $this->getAdapter()->query( $query );

        $allRecords = $stmt->fetchAll();

        return $allRecords;
    }
//}}}
//{{{ changeNOWtoCurrentTime
    public function changeNOWtoCurrentTime( $data ){
    // Fix datetime_created and datetime_modified so that it has the current time
        // if the user puts in NOW() for these columns change it to the zend version so 
        // that it would actually put in the correct time
    
        foreach( $data as $key => $val ){

            if( $key == 'datetime_created' || $key == 'datetime_modified' ){

                if( $val == 'NOW()' || $val == 'now()' )
                    $data[$key] = new Zend_Db_Expr( 'NOW()' );  
            }
        }

        return $data;
    }

//}}}
}
