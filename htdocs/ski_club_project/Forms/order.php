<?php
/**
 * order.php
 * Uses a user friendly form to insert data into the order relation in the club database.
 * @author Landon Shenberger
 */
    include '../Utilities/connect_db.php';
    $connection = createConnection(); // placed here as it starts the session
?>
<!DOCTYPE html>
<html>
<head>
  <title>Place an order</title>
  <link rel="stylesheet" type="text/css" href="../Resources/style.css">

</head>
<script type="text/javascript" src="../Utilities/ResultScroll.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <form action="../home_page.php">
        <button class="btn" type="submit"><i class="fa fa-home"></i></button>
    </form>
<h1>Place an Order: </h1>
<body>
    <div>
        <form action="" method="POST">
            Member ID of the Member Placing the Order (1-5 numeric [1-99999]):
            <input type="number" name="unique_ID" max="99999" min="1"><br/>
            Select Item:<span class="required">*</span>
            <select name="item_type">
                    <option value="Hoodie">Hoodie</option>
                    <option value="T-Shirt Short Sleeve">T-Shirt Short Sleeve</option>
                    <option value="T-Shirt Long Sleeve">T-Shirt Long Sleeve</option>
            </select><br/>
            Custom Name Label:
            <input type="text" name="custom_name_label" minlength="1" maxlength="75"><br/>
            Select Size:<span class="required">*</span>
            <select name="size">
                    <option value="Small">Small</option>
                    <option value="Medium">Medium</option>
                    <option value="Large">Large</option>
            </select><br/>
            Color: <span class="required">*</span>
            <select name="color">
                    <option value="Grey">Grey</option>
                    <option value="Red">Red</option>
                    <option value="Blue">Blue</option>
                    <option value="Green">Green</option>
                    <option value="Light Grey">Light Grey</option>
                    <option value="Military Green">Military Green</option>
            </select><br/>

            Quantity:<span class="required">*</span>
            <input type="number" name="quantity" min="1" required><br/>

            Item Price:<span class="required">*</span>
            <input type="number" min="0.01" step="0.01" name="price_per_item" required><br/>



            <input type="submit" name="order_button" value="Place Order">
        </form>
    </div>

    <div>
    <h2>Results will appear here: </h2>
        <?php
            include '../login/login_validation.php';
            include '../Utilities/error_handler.php';
            include '../Utilities/utilities.php';
            if (isValid($connection)){
                if (getLoggedIn()) {
                    if(isset($_POST['order_button'])){
                        $member_id = $_POST['unique_ID'];
                        if ($member_id == "") {$member_id = NULL;}
                        $name_label = $_POST['custom_name_label'];
                        if ($name_label == "") {$name_label = NULL;}
                        // leave order_id out because it is auto increment key
                        pg_prepare($connection, "add_order", 'INSERT INTO member_order VALUES ($1, $2, $3, $4, $5, $6, $7)');
                        $order_params = array($member_id,
                            $_POST['item_type'],
                            $name_label,
                            $_POST['size'],
                            $_POST['color'],
                            $_POST['quantity'],
                            $_POST['price_per_item']);
                        if(@pg_send_execute($connection, "add_order", $order_params)){
                            if ($error = getError($connection)){
                                print ($error);
                                print("<script>scrollToBottom();</script>");
                            }
                            else{
                                print("<script>scrollToBottom();</script>");
                                $result_string = "Successfully placed order for $" . $_POST['quantity']*$_POST['price_per_item'];
                                // if there is a member associated with this order, display it.
                                if (!empty($order_params[0])){
                                    $result_string .= ", placed for member ". getMemberMetaData($connection, $order_params[0]);
                                }
                                print ($result_string);
                            }
                        }
                    }
                }
                else{
                    print("<script>scrollToBottom();</script>");
                    echo '<h1>You mush be logged in to place an order!</h1>';
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