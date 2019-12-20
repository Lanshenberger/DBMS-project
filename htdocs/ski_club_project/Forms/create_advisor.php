<?php
/**
 * create_advisor.php
 * Uses a user friendly form to insert data into the advisor relation in the club database.
 * @author Landon Shenberger
 */
    include '../Utilities/connect_db.php';
    $connection = createConnection(); // starts the session also
?>
<!DOCTYPE html>
<html>
<head>
  <title>Add an Advisor</title>
  <link rel="stylesheet" type="text/css" href="../Resources/style.css">
</head>
<script type="text/javascript" src="../Utilities/ResultScroll.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <form action="../home_page.php">
        <button class="btn" type="submit"><i class="fa fa-home"></i></button>
</form>
<h1>Add an Advisor: </h1>
<body>

    <div>
        <form action="" method="POST">
            Advisor ID (1-5 length numeric [1-99999]): <span class="required">*</span>
            <input type="number" name="advisor_ID" min="1" max="99999" required><br/>

            Club Number(1-5 length numeric [1-99999]):
            <input type="number" name="club_no" min="1" max="99999"><br/>

            Email: 
            <input type="text" name="email" maxlength="200"><br/>

            First Name: <span class="required">*</span>
            <input type="text" name="first_name" minlength="2" maxlength="50" required><br/>

            Last Name: <span class="required">*</span>
            <input type="text" name="last_name"  minlength="2" maxlength="50" required><br/>


            <input type="submit" value="Add" name="insert_button">
        </form>
    </div>
    <div>
    <h2>Results will appear here: </h2>
        <?php
            include '../Utilities/utilities.php';
            include '../login/login_validation.php';
            include '../Utilities/error_handler.php';
            // check db connection
            if(isValid($connection)){
                if (getLoggedIn()){
                    if(isset($_POST['insert_button'])){ //check if form was submitted
                        $club = $_POST['club_no'];
                        $email = $_POST['email'];
                        if ($club == "") {$club = NULL;}
                        if ($email == "") {$email = NULL;}
                        pg_prepare($connection, "add_advisor", 'INSERT INTO advisor VALUES ($1, $2, $3, $4, $5)');
                        $advisor_params = array($_POST['advisor_ID'], $club, $email, $_POST['first_name'], $_POST['last_name']);
                        if(@pg_send_execute($connection, "add_advisor", $advisor_params)){
                            if ($error = getError($connection)){
                                print($error);
                                print("<script>scrollToBottom();</script>");
                            }
                            else{
                                print("<script>scrollToBottom();</script>");
                                $club_string = "";
                                if ($club != NULL){
                                    $club_string = ", advising the club: ".getClubMetaData($connection, $club);
                                }
                                print ("Successfully the added advisor: ". getAdvisorMetaData($connection, $_POST['advisor_ID']) . $club_string);
                            }

                        }
                    }
                }
                else{
                    print("<script>scrollToBottom();</script>");
                    echo '<h1>You must be logged in to add an advisor!</h1>';
                }
                closeConnection($connection);
            }
            else{
                print("<script>scrollToBottom();</script>");
                echo '<h1>The database connection is invalid. Check connect_db.php.</h1>';
            }
        ?>
    </div>
</body>
</html>