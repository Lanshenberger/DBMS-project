<?php
/**
 * connect_db.php
 * This file contains various utility functions to deal with connections to the database.
 * @author Landon Shenberger
 */


    @session_start();
    // connection string is stored in this session variable.
    $_SESSION['DBconnection'] = "host=127.0.0.1 dbname=Ski_Club user=admin password=admin port=5432";



/**
 * This function creates the connection to the database according to the session variable named 'DBconnection'
 * @return false|resource the connection to the database, or false on failure to connect.
 */
function createConnection(){
    return @pg_connect ($_SESSION['DBconnection']);
}


/**
 * A function used to close a postgesql connection.
 * @param $connection resource the connection to the database.
 * @return bool true on successful close, false on the failure to close the connection.
 */
function closeConnection($connection){
    return @pg_close($connection); // return in case connection close bool is needed
}

/**
 * A very simple function to test whether a connection is valid or not.
 * @param $connection resource the connection to the database.
 * @return bool true if the connection is valid; false if the connection is invalid
 */
function isValid($connection){
    if ($connection){ return true;}
    else {return false;}
}
