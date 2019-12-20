<?php
/**
 * utilities.php
 * A file containing various useful utility functions specific to the club database.
 */

/**
 * given the uid of a member, data about that member in the form of a string is returned.
 * @param $connection resource the connection to the database
 * @param $uid int the id of the member the user would like to retrieve data from
 * @return bool|string on success the members meta data in the format of [ID] - firsname lastname is returned, false if it failed.
 */
function getMemberMetaData($connection, $uid){
    $meta_data = "";
    // Get the submitter of some submission
    @pg_prepare($connection, "get_club_member", 'SELECT unique_id, first_name, last_name from club_member WHERE unique_id=$1');
    if ($member_data= @pg_execute($connection, "get_club_member", array($uid))){
        // fetch the one members data (will be row 0) as an array indexed numerically to retrieve metadata
        // associated with the member.
        $member_array = pg_fetch_array($member_data, 0, PGSQL_NUM);
        for($i = 0; $i < count($member_array); $i++){
            // 0: id
            // 1: first name
            // 2: last name
            switch($i){
                case 0:
                    $meta_data .= "[" . $member_array[$i] . "] - ";
                    break;
                case 1:
                    $meta_data .= $member_array[$i] . " ";
                    break;
                case 2:
                    $meta_data .= $member_array[$i];
            }
        }
        return $meta_data;
    }
    else{ return false;}
}

/**
 * given the uid of a club, data about that club in the form of a string is returned.
 * @param $connection resource the connection to the database
 * @param $uid int the id corresponding to the club the user would like to retrieve data from.
 * @return bool|string on success the club's meta data in the format of [ID] - clubname is returned, false if it failed.
 */
function getClubMetaData($connection, $uid){
    $meta_data = "";
    @pg_prepare($connection, "get_club", 'SELECT "club_no.", club_name FROM ski_club WHERE "club_no."=$1');
    if ($club_data = @pg_execute($connection, "get_club", array($uid))){
        $club_array = @pg_fetch_array($club_data, 0, PGSQL_NUM);
        for ($i = 0; $i < count($club_array); $i++){
            // 0: club number
            // 1: club name
            switch ($i){
                case 0:
                    $meta_data .= "[". $club_array[$i] . "] - ";
                    break;
                case 1:
                    $meta_data .= $club_array[$i];
                    break;
            }
        }
        return $meta_data;
    }
    else {return false;}
}


/**
 * given the uid of an advisor, data about that advisor in the form of a string is returned.
 * @param $connection resource the connection to the database
 * @param $uid int the id corresponding to the advisor the user would like to retrieve data from.
 * @return bool|string on success the advisor's meta data in the format of [ID] - firstname lastname is returned, false if
 * failed.
 */
function getAdvisorMetaData($connection, $uid)
{
    $meta_data = "";
    // Get the submitter of some submission
    @pg_prepare($connection, "get_advisor", 'SELECT advisor_id, first_name, last_name from advisor WHERE advisor_id=$1');
    if ($advisor_data = @pg_execute($connection, "get_advisor", array($uid))) {
        $advisor_array = @pg_fetch_array($advisor_data, 0, PGSQL_NUM);
        for ($i = 0; $i < count($advisor_array); $i++) {
            // 0: id
            // 1: first name
            // 2: last name
            switch ($i) {
                case 0:
                    $meta_data .= "[" . $advisor_array[$i] . "] - ";
                    break;
                case 1:
                    $meta_data .= $advisor_array[$i] . " ";
                    break;
                case 2:
                    $meta_data .= $advisor_array[$i];
            }
        }
        return $meta_data;
    } else {
        return false;
    }
}


/**
 * returns the header names of the desired relation
 * @param $selected string the name of the desired relation
 * @return array the headers of the selected table, excluding none of them. In other words, all the attributes are returned.
 */
function getHeaders($selected){
    $headers = array();
    switch ($selected){
        case "advisor":
            $headers = array("Advisor ID", "Club", "Email", "First Name", "Last Name");
            break;
        case "bus_pass":
            $headers = array("Purchaser", "Check #", "Amount Paid", "Bus Pass ID");
            break;
        case "club_member":
            $headers = array("Member ID", "Club", "First Name", "Last Name", "Requires a Bus Pass",
                "Is a Student?");
            break;
        case "form":
            $headers = array("Form Type", "Submitter", "Completion Date");
            break;
        case "medical_insurance":
            $headers = array("Policy Number", "Submitter", "Plan Name", "Pharmacy Network",
                "Group Number");
            break;
        case "member_order":
            $headers = array("Member ID", "Item Type", "Custom Name Label", "Size", "Color", "Quantity",
                "Price Per Unit", "Order ID");
            break;
        case "pass":
            $headers = array("Resort Pass ID", "Purchaser", "Frequency", "Type");
            break;
        case "ski_club":
            $headers = array("Club Number", "Club Name", "Max Bus Seats", "Resort");
            break;
        default:
            // do nothing, the array is already initialized as empty
            break;
    }
    return $headers;
}


/**
 * generates an HTML selection form for deletion (by default) or reports.
 * @param $r false|resource result set to be displayed in the HTML selection form. It works best if a result set
 * of select <ID>, <first_name>, <last_name> is given.
 * @param string $alt_name an optional parameter used to give the form a different name than the ones selected
 * programmatically. If it is left unspecified, the name of the selection form will correspond to the deletion
 * form names.
 * @return string the selection form in HTML format if the function succeeded, if not, some details on
 * on the error is returned. Use $_POST['uid'] to get the ID of the selected element.
 */
function generateHTMLSelectionForm($r, $alt_name = ""){
    $relation = pg_field_table($r, 0);
    //set button name according to the optional parameter
    if ($alt_name == ""){
        $btn_name = "Remove";
    }
    else{
        $btn_name = "Select";
    }
    // default argument taken, use delete button names
    if ($alt_name == ""){
        switch ($relation){
            case "advisor":
                $name = "del_advisor_btn";
                break;
            case "club_member":
                $name = "del_club_member_btn";
                break;
            case "ski_club":
                $name = "del_ski_club";
                break;
            default:
                echo 'No valid relation selected'; $name = "";
                break;
        }
    }
    // use alt_name as the name of the form as the argument is supplied
    else{
        $name = $alt_name;
    }
    if (pg_num_rows($r) > 0){ // check if there are any rows
        $form = /** @lang HTML */
            "<div><form action='' method='POST'>
                        <select name='uid' size='10' >"; // opening tags
        while ($row = pg_fetch_row($r)){
            if ($relation == "ski_club"){
                $form .= sprintf(/** @lang HTML */ "<option value=%s>[%s] - %s w/ Resort: %s</option>", $row[0], $row[0], $row[1], $row[2]);
            }
            else{
                $form .= sprintf(/** @lang HTML */ "<option value=%s>[%s] - %s %s</option>", $row[0], $row[0], $row[1], $row[2]); // set each option, the value (id) has the key
            }
        }
        $form .= sprintf(/** @lang HTML */ "</select>
                    <input type='submit' name='%s' value='%s'>
                    </form></div> ", $name, $btn_name); // closing tags
        print/** @lang HTML */
            ("<script> scrollToBottom(); </script>") ;
        return $form;
    }
    else {
        return 'Nothing is in the '.$relation.' relation.';
    }
}