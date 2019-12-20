<?php
/**
 * test_script_user_reg.php
 * Conceptually speaking, the club database would only need to be used by the people that run the clubs. Hence,
 * there is no registration page; however, running this script will register a user with all permissions, storing
 * their credentials in the database. Each user can be inserted manually by changing the $username and $password variables.
 * Of course, the password is not stored in raw form: it is hashed before it is inserted into the "detached" users table.
 * @author Landon Shenberger
 */
// The website does not contain a register page as the users that could access this would be added by the company itself
// Thus, this script can be ran to insert a hashed password into the user table in order to test the login function,
// which is needed to use the website
$username = ""; // create username (change this to insert a new user)
$password = ""; // create actual password (change this to insert a new user)
// this script will only work once unless the username and password are changed because there cannot be duplicate username
$connection = pg_connect("host=127.0.0.1 dbname=Ski_Club user=admin password=admin port=5432"); // get the connection to the database
if (!$connection){
    echo 'failed connection';
}
pg_prepare($connection, "add_user", 'INSERT INTO users (username, password) VALUES ($1, $2)');
// add the username and the HASHED password to the users table if the fields are not blank
if (!empty($username) && !empty($password)) {
    if (@pg_execute($connection, "add_user", array($username, password_hash($password, PASSWORD_DEFAULT)))){
        echo 'successfully added user '.$username.' to the system';
    }
    else{
        echo 'Failed to add user '.$username.' to the system <br>';
        echo pg_last_error($connection);
    }
}
else{
    echo 'Fill out the username and password to add it to the database';
}

