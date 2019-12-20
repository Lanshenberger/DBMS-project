<?php
/**
 * other_view.php
 * This file displays reports on the specific members or advisors in a specific club. It essentially reports the members/
 * advisors of a club.
 * @author Landon Shenberger
 */
    include '../Utilities/connect_db.php';
    $connection = createConnection(); // starts the session also
?>
<!DOCTYPE html>
<html>
<head>
  <title>View Database</title>
  <link rel="stylesheet" type="text/css" href="../Resources/style.css">
</head>
<script src="../Utilities/ResultScroll.js"></script>
<script src="../Utilities/downloader.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <form action="../home_page.php">
        <button class="btn" type="submit"><i class="fa fa-home"></i></button>
</form>
<h1>Choose an Option: </h1>
<body>
    <div>
        <form action="" method="POST">
        <select name="database" size="2" >
            <option value="club_mem">Display members by club</option>
            <option value="advisor_mem">Display advisors by club</option>
        
        </select>
        <input type="submit" name="display_btn" value="Select">
        </form>
    </div>
    <h2>Results will appear here: </h2>
        <?php
            include '../Utilities/displayTable.php';
            include '../Utilities/utilities.php';
            include '../login/login_validation.php';
            if (isValid($connection)) {
                if (getLoggedIn()) {
                    if (isset($_POST['display_btn'])) {
                        $option = @$_POST['database'];
                        $b_name = ""; // used to name the selection form submit button accordingly.
                        $is_member = true; // used to denote the whether we are concerned with members or advisors
                        switch ($option) {
                            case "club_mem":
                                $b_name = "btn_club_mem";
                                break;
                            case "advisor_mem":
                                $b_name = "btn_advisor_mem";
                                $is_member = false;
                                break;
                            default:
                                $b_name = "";
                                echo "Please Select an option.";
                                break;
                        }
                        if ($b_name != "") {
                            displayClubs($b_name, $connection, $is_member);
                        }
                    } else {
                        // for the club member display tuples
                        if (isset($_POST['btn_club_mem'])) {
                            $c_id = @$_POST['uid'];
                            if ($c_id != "") {
                                pg_prepare($connection, "display_club_mem_per_club",
                                    'SELECT unique_id, first_name, last_name FROM club_member WHERE "club_no."=$1');
                                $r = pg_execute($connection, "display_club_mem_per_club", array($c_id));
                                $headers = array("Member ID", "First Name", "Last Name");
                                displayTuplesInClub($connection, $r, $c_id, $headers);
                            } else {
                                echo 'Please select a club and try again.';
                            }

                        } // for advisor display tuples
                        else if (isset($_POST['btn_advisor_mem'])) {
                            $c_id = @$_POST['uid'];
                            if ($c_id != "") {
                                pg_prepare($connection, "display_advisor_mem_per_club",
                                    'SELECT advisor_id, first_name, last_name FROM advisor WHERE "club_no."=$1');
                                $r = pg_execute($connection, "display_advisor_mem_per_club", array($c_id));
                                displayTuplesInClub($connection, $r, $c_id, array("Advisor ID", "First Name", "Last Name"));
                            } else {
                                echo 'Please enter a valid advisor id.';
                            }
                        } else {
                            //echo 'Please select a club.';
                        }
                    }
                } else {
                    print("<script>scrollToBottom();</script>");
                    echo '<h1>You mush be logged in to view the clubs!</h1>';
                }
                closeConnection($connection);
            }
            else{
                print("<script>scrollToBottom();</script>");
                echo '<h1>The database connection is invalid. Check connect_db.php.</h1>';
            }


        /**
         * This function is used to dynamically create a HTML selection form for clubs. It only displays clubs with
         * at least one member (if we are concerned with members as determined by the hardcoded form above), or at least
         * one member. It uses a helper function found in utilities.php to generate the actual form.
         * @param $name string when the HTML form is created dynamically, the name of the submit field is set to this
         * variable.
         * @param $connection resource the connection to the database.
         * @param $is_member boolean if we are interested in displaying the clubs with members in them (i.e. at least
         * one member in that club), set this variable to true; if we are interested in displaying the clubs with
         * advisors in them (i.e. at least one advisor), set the variable to false.
         */
        function displayClubs($name, $connection, $is_member){
                if ($is_member){ $from = "club_member"; $s = "club members";} else {$from = "advisor"; $s = "advisors";}
                print ("<h4> Note: only clubs with ".$s." are displayed (i.e. the club is not empty).</h4>");
                pg_prepare($connection, "club_display",
                    'SELECT "club_no.", club_name, coalesce(desired_resort, $1) FROM ski_club
                                                        WHERE "club_no." IN (SELECT "club_no." FROM '.$from.')');
                $r = pg_execute($connection, "club_display", array("No Resort"));
                if (pg_num_rows($r) > 0){
                    echo generateHTMLSelectionForm($r, $name);
                }
                else{
                    echo 'There are no clubs with any '.$s. ' in them';
                }
                print /** @lang HTML */ ("<script> scrollToBottom(); </script>");
            }


        /**
         * This function prints out the table given a sql query result. It also displays a download button that can be
         * used to download the report as CSV.
         * @param $connection resource the connection to the database.
         * @param $result resource the query result resource that will be used to display the tuples (rows).
         * @param $club_id integer the unique id of the club we are getting data from. It is used to display meta data
         * about the club.
         * @param array $headers the headers of the table in order. The number of headers must match the number of
         * selected attributes in the query (arity)
         */
        function displayTuplesInClub($connection, $result, $club_id, $headers = array()){
                $club = getClubMetaData($connection, $club_id);
                $club_html = '<span style="float: left"><h2>'.$club.':</h2></span>';
                echo $club_html;
                if (pg_num_rows($result) > 0){
                    $download_btn = <<<DOWNLOADCLUB
                                        <input type='submit' name='download_btn' value='Download CSV' 
                                        onclick='exportTableToCSV("$club")'/>
                                        DOWNLOADCLUB;
                    echo '<span style="float: right">'.$download_btn.'</span>';
                    displayTable($connection, $result, $headers);
                }
                else{
                    echo 'No results.';
                }
            }
        ?>
</body>
</html>