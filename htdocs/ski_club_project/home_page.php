<html>
    <style>
        input[type=submit] {
            background-color: #4CAF50;
            color: white;
            padding: 14px 20px;
            margin: 8px 0;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .db
        {
            display:inline;
            margin:0px;
            padding:10px;
        }
        h1 
        {
            color: Black;
        }
        body{
            background-color : LightSteelBlue;
        }
        /* Button used to open the contact form - fixed at the bottom of the page */
        .open-button {
            padding: 16px 20px;
            border: none;
            cursor: pointer;
            position: fixed;
            bottom: 23px;
            right: 28px;
            width: 280px;
        }


        /* The popup form - hidden by default */
        .form-popup {
            display: none;
            position: fixed;
            bottom: 0;
            right: 15px;
            border: 3px solid #f1f1f1;
            z-index: 9;
        }

        /* Add styles to the form container */
        .form-container {
            max-width: 300px;
            padding: 10px;
            background-color: lightgrey;
        }

        /* Full-width input fields */
        .form-container input[type=text], .form-container input[type=password] {
            width: 100%;
            padding: 15px;
            margin: 5px 0 22px 0;
            border: none;
            background: #f1f1f1;
        }

        /* When the inputs get focus, do something */
        .form-container input[type=text]:focus, .form-container input[type=password]:focus {
            background-color: #ddd;
            outline: none;
        }

        /* Set a style for the submit/login button */
        .form-container .btn {
            background-color: #4CAF50;
            color: white;
            padding: 16px 20px;
            border: none;
            cursor: pointer;
            width: 100%;
            margin-bottom:10px;
            opacity: 0.8;
        }

        /* Add a red background color to the cancel button */
        .form-container .cancel {
            background-color: red;
        }

        /* Add hover for buttons inside popup form */
        .form-container  input[type=submit]:hover{
            opacity: 65%;
        }

        /* Add hover for buttons not inside pop up form */
        .db input[type=submit]:hover, .open-button:hover {
            background-color: #45a049;
        }

    </style>
<head>
  <title>Home Page</title>
</head>
    <script>
        // for opening the login
        function openForm() {
            document.getElementById("login_form").style.display = "block";
        }

        // for closing the login
        function closeForm() {
            document.getElementById("login_form").style.display = "none";
        }
    </script>
    <script src="Utilities/ResultScroll.js"></script>


    <span style="float: right">
        <?php
            include 'login/login_validation.php';
            $log_out_btn = '<form action="" method="post" class="db">   
                                <input type="submit" name="log_out" value="Log Out"/>
                            </form>';
            if (isset($_POST['log_out'])){
                // the user wants to log out
                session_start();
                $user_to_log_out = $_SESSION['user'];
                $_SESSION['user'] = "";
                echo <<<LOGOUT
                    <h3>Logged out user $user_to_log_out</h3>
                    LOGOUT;
            }
            else{
                //display the login status
                if (isset($_POST['error'])){
                    //display the error message on login attempt
                    $error_message = $_POST['error'];
                    echo <<<ERRORLOGIN
                    <h3 style="display: inline">$error_message</h3>
                    ERRORLOGIN;
                    // display the log out button if the user attempted to log in while they were already logged in.
                    if (getLoggedIn()){
                        echo <<<LOGGEDIN
                            $log_out_btn
                        LOGGEDIN;
                    }
                }
                else{
                    // either the user has not attempted to log in or is logged in
                    if ($user = getLoggedIn()){
                        // user is logged in
                        echo <<<LOGGEDIN
                            <h3 style="display: inline">Logged in as $user</h3>
                            $log_out_btn
                        LOGGEDIN;
                    }
                    else{
                        // Not logged in
                        echo <<<NOTLOGGEDIN
                    <h3>Not logged in</h3>
                    NOTLOGGEDIN;
                    }
                }
            }
        ?>
    </span>

<body>

<input type='submit' class="open-button" onclick="openForm()" value="Login"/>


<div class="form-popup" id="login_form">
    <form action="login/login.php" class="form-container" method="post" >
        <h1>Login</h1>

        <label for="username"><b>Username</b></label>
        <input type="text" placeholder="Enter Username" name="username" required>

        <label for="psw"><b>Password</b></label>
        <input type="password" placeholder="Enter Password" name="psw" required>

        <input type="submit" name='login_btn' class="btn" value="Login"/>
        <input type="submit" class="btn cancel" onclick="closeForm()" value="Close"/>
    </form>

</div>
<div style="text-align: center">
        <br><br><br><br><h2>Select an Option:</h2>
        <form action="Forms/create_club.php" class="db">
                <input type="submit"  value="Create a Club" />
        </form>
        <form action="Forms/create_club_member.php" class="db">
            <input type="submit" value="Club Member Edit Page" />
        </form>
        <form action="Forms/create_advisor.php" class="db">
                <input type="submit" value="Add an Advisor" />
        </form>
        <form action="Forms/order.php" class="db">
                <input type="submit" value="Place an Order" />
        </form>
        <form action="delete.php" class="db">
                <input type="submit" value="Removal page" />
        </form>

        <br><br><br><br><br><h2>Reports:</h2>
        <form action="Reports/database.php" class="db">
                <input type="submit" value="View Database Tables" />
        </form>
        <form action="Reports/missing.php" class="db">
                <input type="submit" value="View Who is Missing What?" />
        </form>
        <form action="Reports/other_view.php" class="db">
                <input type="submit" value="View Members/Advisors in a Club" />
        </form>
        <form action="Reports/view_orders.php" class="db">
                <input type="submit" value="View Orders by Member" />
        </form>
</div>
<!-- Uncomment the php code below to access a connection tester
        <h1><br><br><br><br><br>Test connection to the database</h1>
        <form action="" method="POST">
            <input type="submit" name="c" value="Check Connection" />
        </form>

-->
<!--        --><?php
//            include 'connect_db.php';
//            if(isset($_POST['c'])){
//                $connection = createConnection();
//                    if($connection) {
//                        echo '<h4><br><br>Success.</h4>';
//                    }
//                    else {
//                        echo '<h4><br><br>Failed to connect to database, check the connection string in connect_db.php</h4>';
//                    }
//                echo '<script>scrollToBottom();</script>';
//                closeConnection($connection);
//            }
//
//        ?>
</body>
</html>