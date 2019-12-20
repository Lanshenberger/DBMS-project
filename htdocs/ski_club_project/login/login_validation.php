<?php
/**
 * login_validation.php
 * May be included when the program needs to check the current user that is logged in.
 * @author Landon Shenberger
 */


/**
 * A simple function used to retrieve the current user that is logged in.
 * @return bool|mixed returns false if there is no user logged in during this session; otherwise, it returns the user.
 */
function getLoggedIn(){
    @session_start();
    if (isset($_SESSION['user']) && !empty($_SESSION['user'])){
        // user is logged in
        return $_SESSION['user'];
    }
    else{
        return false;
    }
}


