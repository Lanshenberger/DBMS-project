<?php
/**
 * This file is used to report specifics on errors, typically when dealing with insertions it would be useful to include
 * this file.
 * @author Landon Shenberger
 */

/**
 * This function should be used after a pg_send function is used. It reports whether or not there was an error as well as
 * some details on the error. It is tailored to be used upon insertion into the database.
 * @param $connection resource the connection to the database.
 * @return bool|string returns false if there are no errors, and a string explaining some specifics on the error if an error
 * occurred.
 */
function getError($connection){
    $state = pg_result_error_field(pg_get_result($connection), PGSQL_DIAG_SQLSTATE);
    if ($state == 0){ // 0 means there is no error, quick exit.
        return false;
    }
    switch ($state){
        case 23505:
            $err_detail = "Error: an id of the value you submitted already exists; you cannot have duplicate ids.";
            break;
        case 23503:
            $err_detail = "Error: you attempted to reference a member/advisor/club by an id that does not exist in the database table
            (i.e. the member/advisor/club does not exist).";
            break;
        case 23514:
            $err_detail = "Error: the values you inputted are not within the specified range.";
            break;
        default:
            $err_detail = "Unknown error.";
    }
    return $err_detail;
}
