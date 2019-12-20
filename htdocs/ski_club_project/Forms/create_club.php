<?php
/**
 * create_club.php
 * Uses a user friendly form to insert data into the advisor relation in the club database.
 * @author Landon Shenberger
 */
    include '../Utilities/connect_db.php';
    $connection = createConnection(); // starts the session also
?>
<!DOCTYPE html>
<html>
<head>
  <title>Add a Club</title>
  <link rel="stylesheet" type="text/css" href="../Resources/style.css">
</head>
<script type="text/javascript" src="../Utilities/ResultScroll.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <form action="../home_page.php">
        <button class="btn" type="submit"><i class="fa fa-home"></i></button>
    </form>
<h1>Add a Club</h1>
<body>
    <div>
        <form action="" method="POST">
            Club Number (1-5 length numeric [1-99999]):<span class="required">*</span>
            <input type="number" name="club_no" min="1" max="99999" required><br/>

            Club Name:<span class="required">*</span>
            <input type="text" name="club_name" maxlength="100" required><br/>

            Total Bus Seats Allowed:
            <input type="number" name="max_bus_participants" min="1"><br/>

            Resort:
            <input type="text" name="desired_resort" maxlength="100"><br/>

            <input type="submit" value="Add" name="insert_button">

        </form>
    </div>
    <div>
    <h2>Results will appear here: </h2>
        <?php
            include '../Utilities/utilities.php';
            include '../Utilities/error_handler.php';
            include '../login/login_validation.php';
            if (isValid($connection)){
                if (getLoggedIn()){
                    if(isset($_POST['insert_button'])){ //check if form was submitted
                        $bus = $_POST['max_bus_participants'];
                        $resort = $_POST['desired_resort'];
                        if ($bus == "") { $bus = NULL;}
                        if ($resort == "") {$resort = NULL;}
                        pg_prepare($connection, "add_club", 'INSERT INTO ski_club VALUES ($1, $2, $3, $4)');
                        $param_array = array($_POST['club_no'], $_POST['club_name'], $bus, $resort);
                        if (@pg_send_execute($connection, "add_club", $param_array)){
                            if ($error = getError($connection)){
                                print($error);
                                print("<script>scrollToBottom();</script>");
                            }
                            else{
                                print("<script>scrollToBottom();</script>");
                                print ("Successfully added the club ".getClubMetaData($connection,$_POST['club_no'] ).".");
                            }
                        }
                    }
                }
                else{
                    print("<script>scrollToBottom();</script>");
                    echo '<h1>You must be logged in to add a club!</h1>';
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