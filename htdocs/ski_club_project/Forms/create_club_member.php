<?php
/**
 * create_club_member.php
 * Uses several user friendly forms to insert information pertaining to club member, or a club member themselves
 * in the club database.
 * @author Landon Shenberger
 */
    include '../Utilities/connect_db.php';
    $connection = createConnection();
?>
<html>
    <head>
        <title> Add A Club member </title> 
        <link rel="stylesheet" type="text/css" href="../Resources/style.css">
    </head>
    <script src="../Utilities/ResultScroll.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <form action="../home_page.php">
        <button class="btn" type="submit"><i class="fa fa-home"></i></button>
    </form>
    <h1>Add a Club Member</h1>
    <body>
        <div>

            <form action="" method="POST">
                Club Member ID (1-5 length numeric [1-99999]): <span class="required">*</span>
                <input type="number" name="unique_ID_club_member" min="1" max="99999" required><br/>

                Club Number (1-5 length numeric [1-99999]):
                <input type="number" name="club_no_club_member" min="1" max="99999"><br/>

                First Name: <span class="required">*</span>
                <input type="text" name="first_name" maxlength="100" required><br/>

                Last Name: <span class="required">*</span>
                <input type="text" name="last_name" maxlength="100" required><br/>

                Does this member need a bus pass?: <span class="required">*</span>
                <select name="bus_pass">
                    <option value="needs_pass">Yes</option>
                    <option value="does_not_need_pass">No</option>
                </select><br/>

                Is this member a student?: <span class="required">*</span>
                <select name="student">
                    <option value="is_student">Yes</option>
                    <option value="is_not_student">No</option>
                </select><br/>

                <input type="submit" value="Add" name="button_club_member">
                
            </form>

        </div>
        <h1>Submit a Form</h1>
        <div>

                <form action="" method="POST">
                    Select the form: <span class="required">*</span>
                    <select name="form_type">
                        <option value="Waiver Form">Waiver Form</option>
                        <option value="Emergency Form">Emergency Form</option>
                    </select><br/>
                    ID of the member submitting the form (1-5 length numeric [1-99999]): <span class="required">*</span>
                    <input type="number" name="unique_ID_form" min="1" max="99999" required><br/>
                    Completion Date:
                    <input type="date" name="completion_date"><br/>

                    <input type="submit" value="Submit Form" name="button_form">
                    
                </form>

        </div>
        <h1>Submit a Medical Card</h1>
        <div>

                <form action="" method="POST">
                    Member ID/policy number of card: <span class="required">*</span>
                    <input type="number" name="member_ID" min="1" required><br/>
                    ID of the member submitting the card (1-5 length numeric [1-99999]): <span class="required">*</span>
                    <input type="number" name="unique_ID_medical_card" min="1" max="99999" required><br/>
                    Plan Name:
                    <input type="text" name="plan_name" maxlength="150"><br/>
                    Pharmacy Network:
                    <input type="text" name="pharmacy_network" maxlength="50"><br/>
                    Group Number:
                    <input type="number" name="group_number" min="1"><br/>

                    <input type="submit" value="Submit Medical Card" name="button_medical_card">
                    
                </form>

        </div>

        <h1>Buy Bus Pass</h1>
        <div>

                <form action="" method="POST">
                    ID of the member (1-5 length numeric [1-99999]):
                    <input type="number" name="unique_ID_bus_pass" min="1" max="99999">
                    Check#:
                    <input type="text" name="check" maxlength="12" minlength="1"><br/>
                    Amount Paid:<span class="required">*</span>
                    <input type="number" min="0.01" step="0.01" name="amount_paid" required><br/>
                    <input type="submit" value="Buy Bus Pass" name="button_bus_pass">
                </form>

        </div>
        <h1>Buy Pass</h1>
        <div>

                <form action="" method="POST">
                    Pass ID (1-5 length numeric [1-99999]):<span class="required">*</span>
                    <input type="number" name="pass_ID" min="1" max="99999" required><br/>
                    ID of the member (1-5 length numeric [1-99999]):
                    <input type="number" name="unique_ID_pass" min="1" max="99999">
                    Pass Validity:<span class="required">*</span>
                    <select name="pass_days_validity">
                            <option value="Once a Week Pass">Once a Week Pass</option>
                            <option value="Every Day Pass">Every Day Pass</option>
                    </select><br/>
                    Pass Type:<span class="required">*</span>
                    <select name="type">
                            <option value="Lift Ticket Only">Lift Ticket Only</option>
                            <option value="Lift Ticket w/ Rentals">Lift Ticket w/ Rentals</option>
                            <option value="Lift Ticket w/ Lessons">Lift Ticket w/ Lessons</option>
                            <option value="Lift Ticket w/ Lessons & Rentals">Lift Ticket w/ Lessons & Rentals</option>
                    </select><br/>

                    <input type="submit" value="Buy Pass" name="button_pass">
                    
                </form>
        </div>
        <div>
            <h2>Results will appear here: </h2>
            <?php
                include '../Utilities/utilities.php';
                include '../login/login_validation.php';
                if(isValid($connection)){
                    if (getLoggedIn()){
                        if(isset($_POST['button_club_member']))
                        {
                            $member_club = $_POST['club_no_club_member'];
                            if ($member_club == "" ) {$member_club = NULL;}
                            if ($_POST['bus_pass'] == "needs_pass"){$needs_pass = 1;} else {$needs_pass = 0;}
                            if ($_POST['student'] == "is_student"){$is_student = 1;} else {$is_student = 0;}

                            $result = pg_prepare($connection, "add_club_member", 'INSERT INTO club_member VALUES ($1, $2, $3, $4, $5, $6)');
                            $club_member_params = array($_POST['unique_ID_club_member'],
                                $member_club,
                                $_POST['first_name'],
                                $_POST['last_name'],
                                $needs_pass,
                                $is_student);
                            executeInsert($connection, $result, "add_club_member", $club_member_params,
                                array("added", "club member"));
                        }
                        else if(isset($_POST['button_form']))
                        {
                            $completion_date = $_POST['completion_date'];
                            if ($completion_date == "") { $completion_date = NULL;}
                            $result = pg_prepare($connection, "add_form", 'INSERT INTO form VALUES ($1, $2, $3)');
                            $form_params = array($_POST['form_type'],
                                $_POST['unique_ID_form'],
                                $completion_date);
                            executeInsert($connection, $result, "add_form", $form_params,
                                array("submitted", $_POST['form_type'], "for", getMemberMetaData($connection, $_POST['unique_ID_form'])));
                        }

                        else if(isset($_POST['button_medical_card']))
                        {
                            // account for NULL
                            $plan_name = $_POST['plan_name'];
                            if ("" == $plan_name) {$plan_name = NULL;}
                            $pharmacy_network = $_POST['pharmacy_network'];
                            if ($pharmacy_network == "") { $pharmacy_network = NULL; }
                            $group_number =  $_POST['group_number'];
                            if ("" == $group_number) {$group_number = NULL;}
                            $result = pg_prepare($connection, "submit_medical_info",
                                'INSERT INTO medical_insurance VALUES ($1, $2, $3, $4, $5)');
                            $med_params = array($_POST['member_ID'],
                                $_POST['unique_ID_medical_card'],
                                $plan_name,
                                $pharmacy_network,
                                $group_number);
                            executeInsert($connection, $result, "submit_medical_info", $med_params,
                                array("submitted a medical card for", getMemberMetaData($connection, $_POST['unique_ID_medical_card'])));

                        }
                        else if(isset($_POST['button_bus_pass']))
                        {
                            $b_member_id = $_POST['unique_ID_bus_pass'];
                            if ($b_member_id == ""){$b_member_id = NULL;}
                            $check = $_POST['check'];
                            if ($check == "") {$check = NULL;}
                            // Leave b_pas_id out; it is auto increment
                            $result = pg_prepare($connection, "submit_bus_pass", 'INSERT INTO bus_pass VALUES ($1, $2, $3)');
                            $b_pass_params = array($b_member_id,
                                $check,
                                $_POST['amount_paid']);
                            executeInsert($connection, $result, "submit_bus_pass", $b_pass_params,
                                array("purchased a bus pass for", getMemberMetaData($connection, $b_member_id)));

                        }
                        else if(isset($_POST['button_pass']))
                        {
                            $pass_member_id = $_POST['unique_ID_pass'];
                            if ($pass_member_id == "") {$pass_member_id = NULL;}
                            $result = pg_prepare($connection, "buy_ski_pass", 'INSERT INTO pass VALUES ($1, $2, $3, $4 )');
                            $ski_pass_params = array($_POST['pass_ID'],
                                $pass_member_id,
                                $_POST['pass_days_validity'],
                                $_POST['type']);
                            executeInsert($connection, $result,"buy_ski_pass", $ski_pass_params,
                                array("purchased a ski pass for", getMemberMetaData($connection, $pass_member_id)));
                        }
                    }
                    else{
                        print("<script>scrollToBottom();</script>");
                        echo '<h1>You must be logged in to do anything on this page!</h1>';
                    }
                    closeConnection($connection);
                }
                else{
                    print("<script>scrollToBottom();</script>");
                    echo '<h1>The database connection is invalid. Check connect_db.php.</h1>';
                }


            /**
             * A helper function used for executing insertions of a given prepared statement
             * @param $connection resource the connection to the database
             * @param $result resource the query result resource created from pg_prepare()
             * @param $stmt_name string the name of the statement to be executed
             * @param $param_array array the parameters (values) to be inserted
             * @param $on_success_array array an array used for building sentences on successful insertion
             */
            function executeInsert($connection, $result, $stmt_name, $param_array, $on_success_array){
                    include '../Utilities/error_handler.php'; // for the handling of errors
                    $result_print = "";
                    $length = count($on_success_array);
                    for ($i = 0; $i < $length; $i++){
                        if ($i == $length -1) {
                            $result_print .= $on_success_array[$i];
                        }
                        else{
                            $result_print .= $on_success_array[$i] . " ";
                        }
                    }
                    if ($result){ // prepared statement is valid
                        if (@pg_send_execute($connection, $stmt_name, $param_array)){
                            if ($error = getError($connection)){
                                print($error);
                                print("<script>scrollToBottom();</script>");
                            }
                            else{
                                if ($stmt_name == "add_club_member"){
                                    $result_print .= " ".getMemberMetaData($connection, $param_array[0]);
                                }
                                print("<script>scrollToBottom();</script>");
                                print ("Successfully ".$result_print.".");
                            }
                        }
                    }
                }
            ?>   
        </div>
    </body>
</html>