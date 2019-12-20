<?php
/**
 * login.php
 * Utilized when the user attempts to log in on the home page (i.e. home_page.php). It utilizes the PHP function
 * password_verify() after retrieving the username entered into the form from the database (if it exists) and the
 * associated hashed password that was set via the password_hash() function. Accordingly, information is sent back to
 * the home page if an error occurs, but on a successful login a session variable is set denoting the user that is
 * logged in.
 * @author Landon Shenberger
 */
$error_message = ""; //holds error messages to be displayed at home page
session_start(); // start the session so we can save the user to it, and retrieve the user from it.
// first check if they attempted to log in
if (isset($_POST['login_btn'])){
    if (!empty($_SESSION['user']) && isset($_SESSION['user'])){
        // the user is already logged in, quick exit by calling redirect.
        $error_message = "You are already logged in as user ".$_SESSION['user'].". You must log out before you can log in as another user.";
        redirect($error_message);
    }
    $connection = pg_connect("host=127.0.0.1 dbname=Ski_Club user=admin password=admin port=5432"); // get the connection to the database
    $username = $_POST['username'];
    $password = $_POST['psw'];
    pg_prepare($connection, "find_user", 'SELECT * FROM users WHERE username=$1');
    $result = pg_execute($connection, "find_user", array($username));
    if ($result && pg_num_rows($result) > 0) {
        // user was found, extract the password
        $result_ary = pg_fetch_row($result, 0);
        $hashed_password = $result_ary[2];
        // check the given password with the hashed password
        if (password_verify($password, $hashed_password)) {
            // successfully logged in
            $_SESSION['user'] = $username;
        } else {
            //incorrect password, but the user does exist
            $error_message = "Incorrect password for user " . $username;
        }
    } else {
        //user does not exist (i.e. the username)
        $error_message = "User " . $username . " does not exist.";
    }
    redirect($error_message);
}

/**
 * Use this function when the program is done testing for password validity. In the case that an error occurred, this
 * function will automatically submit a hidden form via post method to send back the error message.
 * @param $error_message string the message denoting an error in attempting to log into the database. If the string is
 * left empty, a simple redirection will revert the user back to the home page.
 */
function redirect($error_message){
    // before we redirect we must ensure that the error message is sent if needed
    if ($error_message != ""){
        echo <<<ERROR
        <form name="error_form" action="../home_page.php" method="post">
            <input type="hidden" name="error" value="$error_message">
            <input type="submit">
        </form> 
        <script type="text/javascript">document.error_form.submit();</script>
        ERROR;
    }
    else{
        header('Location: ../home_page.php');
    }

}




