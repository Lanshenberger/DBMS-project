<?php
/**
 * delete.php
 * This file is used to delete members clubs and advisors
 * @author Landon Shenberger
 */

include 'Utilities/connect_db.php';
$connection = createConnection(); // starts the session also
?>
<!DOCTYPE html>
<html>
<head>
  <title>Remove Page</title>
  <link rel="stylesheet" type="text/css" href="Resources/style.css">
</head>
<script src="Utilities/ResultScroll.js"> </script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <form action="home_page.php">
        <button class="btn" type="submit"><i class="fa fa-home"></i></button>
</form>
<h1>Select an Option: </h1>
<body>
    <div>
        <form action="" method="POST" display="inline">
            <input type="submit" name="advisor_btn" value="Remove an Advisor">
            <input type="submit" name="member_btn" value="Remove a Member">
            <input type="submit" name="club_btn" value="Remove a Club">
        </form>
    </div>
        <?php
            include 'Utilities/utilities.php';
            include 'login/login_validation.php';
            if (isValid($connection)) {
                if (getLoggedIn()) {
                    if (isset($_POST['advisor_btn'])) {
                        $r = pg_prepare($connection, "advisor_display",
                            'SELECT advisor_id, first_name, last_name FROM advisor');
                        $r = pg_execute($connection, "advisor_display", array());
                        echo generateHTMLSelectionForm($r);
                        print /** @lang HTML */
                            ("<script> scrollToBottom(); </script>");
                    } else if (isset($_POST['member_btn'])) {
                        $r = pg_prepare($connection, "member_display",
                            'SELECT unique_id, first_name, last_name FROM club_member');
                        $r = pg_execute($connection, "member_display", array());
                        echo generateHTMLSelectionForm($r);
                        print /** @lang HTML */
                            ("<script> scrollToBottom(); </script>");
                    } else if (isset($_POST['club_btn'])) {
                        $r = pg_prepare($connection, "club_display",
                            'SELECT "club_no.", club_name, coalesce(desired_resort, $1) FROM ski_club');
                        $r = pg_execute($connection, "club_display", array("No Resort"));
                        echo generateHTMLSelectionForm($r);
                        print /** @lang HTML */
                            ("<script> scrollToBottom(); </script>");
                    }


                    if (isset($_POST['del_advisor_btn'])) {
                        executeDelete("advisor", $connection);
                    } else if (isset($_POST['del_club_member_btn'])) {
                        executeDelete("club_member", $connection);
                    } else if (isset($_POST['del_ski_club'])) {
                        executeDelete("ski_club", $connection);
                    }
                } else {
                    print("<script>scrollToBottom();</script>");
                    echo '<h1>You mush be logged in to delete anything!</h1>';
                }
            }
            else{
                print("<script>scrollToBottom();</script>");
                echo '<h1>The database connection is invalid. Check connect_db.php.</h1>';
            }


        /**
         * Deletes a member, ski club, or advisor from the database according to the values passed from the selection
         * form. The primary key of the tuple we are concerned with is acquired from the selection form.
         * @param $relation string the relation of concern (i.e. what is to be deleted from)
         * @param $connection resource the connection to the database
         */
        function executeDelete($relation, $connection){
                if ($relation == "advisor") { $id_name = "advisor_id"; }
                elseif ($relation == "club_member") { $id_name = "unique_id";}
                elseif ($relation == "ski_club") {$id_name = "\"club_no.\"";}
                $uid = $_POST['uid'];
                if ($_POST['uid'] == ""){
                    echo 'No selection. Select an item to delete and try again.';
                }
                else{
                    if(!(pg_prepare($connection, "delete", sprintf("DELETE FROM %s WHERE %s=$1", $relation, $id_name)))) {
                        print("Failed: " . pg_last_error($connection));
                    }
                    else{
                        $name_of_removed = "";
                        switch ($relation){
                            case "advisor":
                                $name_of_removed = "advisor: ". getAdvisorMetaData($connection, $uid);
                                break;
                            case "club_member":
                                $name_of_removed = "club member: ". getMemberMetaData($connection, $uid);
                                break;
                            case "ski_club":
                                $name_of_removed = "ski club: ". getClubMetaData($connection, $uid);
                                break;
                            default:
                                // do nothing, error display is handled by the preceding if statement
                                break;
                        }
                        if (!(pg_execute($connection, "delete", array($uid)))){
                            print("Failed: " . pg_last_error($connection));
                        }
                        else{
                            print ("Successfully removed ".$name_of_removed);
                        }
                    }
                }
            }
        ?>
</body>
</html>