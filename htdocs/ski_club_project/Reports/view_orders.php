<?php
/**
 * view_orders.php
 * This file displays reports on the orders that a specific member has.
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
<h1>View Orders by Member: </h1>
<body>
        <?php
            include '../login/login_validation.php';
            include '../Utilities/displayTable.php';
            include '../Utilities/utilities.php';
            if(isValid($connection)) {
                if (getLoggedIn()) {
                    // generate an html form
                    echo '<h3> Only club members with orders are included</h3>';
                    pg_prepare($connection, "members",
                        'SELECT unique_id, first_name, last_name FROM club_member WHERE unique_id IN (SELECT unique_id FROM member_order)');
                    echo generateHTMLSelectionForm(pg_execute($connection, "members", array()), "members_with_orders");
                    if (isset($_POST['members_with_orders'])) { // check if the button is submitted
                        $uid = @$_POST['uid']; // get unique id of the member selected
                        if ($uid) {
                            $member = rtrim(getMemberMetaData($connection, $uid));
                            echo '<span style="float: left"> <h2>' . $member . '\'s order(s):</h2></span>'; // get meta data
                            $download_btn = <<<DOWNLOADORDERS
                                        <input type='submit' name='download_btn' value='Download CSV' 
                                        onclick='exportTableToCSV("$member order(s)")'/>
                                        DOWNLOADORDERS;
                            echo '<span style="float: right">' . $download_btn . '</span>';
                            pg_prepare($connection, "m_orders",
                                'SELECT * FROM member_order 
                                WHERE unique_id IN (SELECT unique_id FROM club_member WHERE club_member.unique_id=$1)');
                            $orders = pg_execute($connection, "m_orders", array($uid));
                            displayTable($connection, $orders, getHeaders("member_order"));

                            // collect the total cost of all this members orders with the function select total_order_cost
                            // defined by the database
                            pg_prepare($connection, "total_cost", 'select total_order_cost($1)');
                            $row = pg_fetch_row(pg_execute($connection, "total_cost", array($uid)), 0);
                            echo '<h2>Total Cost of the Order(s): ' . $row[0] . '</h2>'; // disp total

                            echo '<script>scrollToBottom();</script>';

                        } else {
                            echo 'Nothing was selected. Select a member and try again.';
                        }
                    }
                } else {
                    print("<script>scrollToBottom();</script>");
                    echo '<h1>You mush be logged in to view orders!</h1>';
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