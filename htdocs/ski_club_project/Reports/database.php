<?php

    include '../Utilities/connect_db.php';
    $connection = createConnection(); // starts the session also
?>
<!DOCTYPE html>
<html>
<head>
  <title>View Database</title>
  <link rel="stylesheet" type="text/css" href="../Resources/style.css">
</head>
<script src="../Utilities/downloader.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <form action="../home_page.php">
        <button class="btn" type="submit"><i class="fa fa-home"></i></button>
</form>
<h1>Choose a Table: </h1>
<body>
    <div>
        <form action="" method="POST">
        <select name="database" class="select-css" size="8">
            <option value="advisor">Advisors</option>
            <option value="bus_pass">Bus Passes</option>
            <option value="club_member">Club Members</option>
            <option value="form">Forms</option>
            <option value="medical_insurance">Insurance Information</option>
            <option value="member_order">Orders</option>
            <option value="pass">Passes</option>
            <option value="ski_club">Clubs</option>
        </select>
        <input type="submit" name="display_btn" value="Display">
        </form>
    </div>
    <h2><span style="float: left">Results will appear here: </h2></span>
        <?php
            include '../Utilities/displayTable.php';
            include '../Utilities/utilities.php';
            include '../login/login_validation.php';
            if (isValid($connection)){
                if (getLoggedIn()){
                     if (isset($_POST['display_btn'])  && @$_POST['database'] != ""){
                        $selected = $_POST['database'];
                        pg_prepare($connection, "display", 'SELECT * FROM '.$selected);
                        $result = pg_execute($connection, "display", array());
                        $headers = getHeaders($selected);
                        if (pg_num_rows($result) > 0){
                            // use heredoc to easily call js function
                            $download_btn = <<<DOWNLOAD
                                                <input type='submit' name='download_btn' value='Download CSV' 
                                                onclick='exportTableToCSV("$selected")'/>
                                                DOWNLOAD;
                            echo '<span style="float: right">'.$download_btn.'</span>';
                            displayTable($connection, $result, $headers);
                        }
                        else{
                            echo '<br><br>The '.$selected.' table has no data.';
                        }
                     }
                     else{
                         echo "<br><br>Please select an option.";
                     }
                }
                else{
                     print("<script>scrollToBottom();</script>");
                     echo '<br><br><h1>You mush be logged in to view any tables!</h1>';
                }
                closeConnection($connection);
            }
            else{
                print("<script>scrollToBottom();</script>");
                echo '<br><br><h1>The database connection is invalid. Check connect_db.php.</h1>';
            }

        ?>
</body>
</html>