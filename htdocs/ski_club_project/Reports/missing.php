<?php
/**
 * missing.php
 * This file is used to generate meaningful reports pertaining to a specific club member. A club member can gave several
 * components, and in any implementation of the club database the member may be required to possess certain components.
 * This file reports those compnents pertaining to a club member by using the "Display all missing elements by member"
 * option, or entire reports on the possession of one specific component can be generated alternatively with the other
 * options.
 * @author Landon Shenberger
 */

include '../Utilities/connect_db.php';
$connection = createConnection(); // starts the session also
?>
<!DOCTYPE html>
<html>
<head>
  <title>View Missing Info</title>
  <link rel="stylesheet" type="text/css" href="../Resources/style.css">
</head>
<script src="../Utilities/ResultScroll.js"></script>
<script src="../Utilities/downloader.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.22/pdfmake.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/0.4.1/html2canvas.min.js"></script>
<script type="text/javascript">
    /**
     * @function exportPDF a function that uses the pdfmake and html2canvas libraries to export any html table that is
     * currently being displayed.
     * @param filename {string} the name of the file. Do not add the file extension .pdf as it is already appended to the
     * filename along with a timestamp.
     */
    function exportPDF(filename = "file") {
        //add timestamp to the filename
        let date = new Date();
        let dateString = date.toString();
        filename = filename.concat("-", dateString, '.pdf'); // add date and pdf file extension
        html2canvas(document.getElementById('component_table'), {
            onrendered: function (canvas) {
                var data = canvas.toDataURL();
                var docDefinition = {
                    content: [
                        {
                            image: data,
                            width: 500
                        }
                    ]

                };
                pdfMake.createPdf(docDefinition).download(filename);
            }
        });
    }
</script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <form action="../home_page.php">
        <button class="btn" type="submit"><i class="fa fa-home"></i></button>
</form>
<h1>Choose an Option: </h1>
<body>
    <div>
        <form action="" method="POST">
        <select name="database" size="8" >
            <option value="bus">Who needs a bus pass but does not have one?</option>
            <option value="medical">Who has not submitted medical information?</option>
            <option value="emergency">Who has not submitted an emergency form?</option>
            <option value="waiver">Who has not submitted a waiver form?</option>
            <option value="pass">Who has not bought a pass?</option>
            <option value="by_member">Display all missing elements by member.</option>

        </select>
        <input type="submit" name="display_btn" value="Display">
        </form>
    </div>
    <h2><span style="float: left">Results will appear here: </span></h2>
        <?php
            include '../Utilities/displayTable.php';
            include '../Utilities/utilities.php';
            include '../login/login_validation.php';
            if (isValid($connection)) {
                if (getLoggedIn()) {
                    if (isset($_POST['display_btn'])) {
                        $option = @$_POST['database'];
                        $do_display = true; //determines when a table shall be displayed
                        $headers = array(); // hold the headers for the table
                        $filename = ""; // holds the file names for downloads
                        if ($option == "bus") {
                            // Select the club members who need a bus pass (i.e. needs_bus_pass) and that members id is NOT IN the
                            // bus_pass relation meaning they do not have a pass.
                            pg_prepare($connection, "missing_bus",
                                'SELECT * from club_member WHERE needs_bus_pass=$1 AND unique_id 
                                                            NOT IN (SELECT club_member.unique_id FROM club_member JOIN bus_pass 
                                                                ON club_member.unique_id = bus_pass.unique_id  
                                                                       AND club_member.needs_bus_pass=$2)');
                            $result = pg_execute($connection, "missing_bus", array(true, true));
                            $headers = getHeaders("club_member");
                            $filename = "missing_bus_pass";
                        } else if ($option == "medical") {
                            // select club members where their id (PK) does not appear in the medical insurance relation.
                            pg_prepare($connection, "missing_med",
                                'SELECT * FROM club_member WHERE unique_id 
                                    NOT IN (SELECT unique_id FROM club_member JOIN medical_insurance USING (unique_id))');
                            $result = pg_execute($connection, "missing_med", array());
                            $headers = getHeaders("club_member");
                            $filename = "missing_medical_info";
                        } else if ($option == "emergency") {
                            // select club members where their id (PK) is not in the form relation where the form is "Emergency Form"
                            pg_prepare($connection, "missing_emergency", 'SELECT * FROM club_member WHERE unique_id 
                                    NOT IN (SELECT unique_id 
                                    FROM club_member JOIN form using (unique_id) 
                                    WHERE form_type=$1)');
                            $result = pg_execute($connection, "missing_emergency", array("Emergency Form"));
                            $headers = getHeaders("club_member");
                            $filename = "missing_emergency_form";
                        } else if ($option == "waiver") {
                            // select club members where their id (PK) is not in the form relation where the form is "Waiver Form"
                            pg_prepare($connection, "missing_waiver", 'SELECT * FROM club_member WHERE unique_id 
                                    NOT IN (SELECT unique_id 
                                    FROM club_member JOIN form using (unique_id) 
                                    WHERE form_type=$1)');
                            $result = pg_execute($connection, "missing_waiver", array("Waiver Form"));
                            $headers = getHeaders("club_member");
                            $filename = "missing_waiver_form";
                        } else if ($option == "pass") {
                            // select club members where their id (PK) do not appear in the pass relation
                            pg_prepare($connection, "missing_pass", 'SELECT * FROM club_member WHERE unique_id 
                                    NOT IN (SELECT unique_id 
                                    FROM club_member JOIN pass 
                                    USING (unique_id))');
                            $result = pg_execute($connection, "missing_pass", array());
                            $headers = getHeaders("club_member");
                            $filename = "missing_pass";
                        } else if ($option == "by_member") {
                            // here we are not interested in displaying the table yet
                            $do_display = false;
                            // get all members first
                            pg_prepare($connection, "all_members", 'SELECT unique_id, first_name, last_name FROM club_member');
                            // make sure te query does not fail.
                            if ($selection_form_result = pg_execute($connection, "all_members", array())){
                                // make the query yields results
                                if (pg_num_rows($selection_form_result) > 0){
                                    $selection_form = "<br>";
                                    $selection_form .= generateHTMLSelectionForm($selection_form_result, "by_member");
                                    // display the form.
                                    echo $selection_form;
                                    print /** @lang HTML */
                                        ("<script> scrollToBottom(); </script>");
                                }
                                else{
                                    echo '<br><br> There are no members in the database';
                                }
                            }

                        } else {
                            echo "<br><br>Please Select an option.";
                            $do_display = false;
                        }
                        // display the results here:
                        if ($do_display) {
                            // check if there are results before we display
                            if (pg_num_rows($result) > 0){
                                // display the download button
                                // use heredoc to easily call js function exportTableToCSV on click
                                $download_btn = <<<DOWNLOAD
                                            <input type='submit' name='download_btn' value='Download CSV' 
                                            onclick='exportTableToCSV("$filename")'/>
                                        DOWNLOAD;
                                echo '<span style="float: right">' . $download_btn . '</span>';
                                displayTable($connection, $result, $headers);
                            }
                            else{
                                // no results
                                echo '<br><br><br>No results';
                            }
                        }
                    } else if (isset($_POST['by_member'])) {
                        if (!@$_POST['uid']) {
                            echo '<br><br>No option selected, try again.';
                        } else {
                            $id = @$_POST['uid'];
                            pg_prepare($connection, "get_member",
                                'SELECT unique_id, first_name, last_name FROM club_member WHERE unique_id=$1');
                            $result = pg_execute($connection, "get_member", array($id));
                            if (pg_num_rows($result) == 0) {
                                echo '<br><br>Invalid selection. Try again.';
                            } else {
                                // get member info
                                $member_ary = pg_fetch_row($result, 0);
                                $m_info = implode(" ", $member_ary); // take the members info from array to a single string
                                $filename = "missing_report_for_" . $m_info;
                                // display the download button for PDF
                                // use heredoc to easily call js function onClick
                                $download_btn = <<<DOWNLOADPDF
                                            <input type='submit' name='download_btn_pdf' value='Download PDF' 
                                            onclick='exportPDF("$filename");'/>
                                        DOWNLOADPDF;
                                // display button
                                echo '<span style="float: right">' . $download_btn . '</span>';
                                // display member data
                                echo '<br><h3>Member: ' . rtrim($m_info) . ':</h3>';

                                // check bus pass.
                                $q_b_pass = "SELECT unique_id
                        FROM club_member 
                        WHERE needs_bus_pass=true 
                        AND unique_id=$id 
                        AND unique_id 
                        NOT IN(SELECT C.unique_id 
                            FROM club_member AS C, bus_pass AS B 
                            WHERE C.unique_id=B.unique_id 
                            AND C.needs_bus_pass=true)";

                                //NOTE: the two images (i.e. red_x.png and green_check.png) reside in the Resources directory

                                if ((pg_num_rows(pg_query($connection, $q_b_pass))) > 0) { // if true the member needs a bus pass and does not have one.
                                    $i_b_pass = "../Resources/red_x.png";
                                } else {
                                    $i_b_pass = "../Resources/green_check.png";
                                }

                                // Check medical information
                                $q_m_info = "SELECT unique_id 
                        FROM club_member 
                        WHERE unique_id=$id
                        AND unique_id 
                        NOT IN (SELECT C.unique_id 
                            FROM club_member AS C, medical_insurance AS M 
                            WHERE C.unique_id=M.unique_id)";
                                if ((pg_num_rows(pg_query($connection, $q_m_info))) > 0) { // if true the member has not submitted medical info
                                    $i_m_info = "../Resources/red_x.png";
                                } else {
                                    $i_m_info = "../Resources/green_check.png";
                                }

                                // check emergency form
                                $q_e_info = "SELECT * from club_member 
                        WHERE unique_id=$id
                        AND unique_id
                        NOT IN (SELECT C.unique_id 
                            FROM club_member AS C, form AS F
                            WHERE C.unique_id=F.unique_id 
                            AND F.form_type='Emergency Form')";
                                if ((pg_num_rows(pg_query($connection, $q_e_info))) > 0) { // if true the member has not submitted an emergency form
                                    $i_e_info = "../Resources/red_x.png";
                                } else {
                                    $i_e_info = "../Resources/green_check.png";
                                }

                                // check waiver form
                                $q_w_info = "SELECT * from club_member 
                        WHERE unique_id=$id
                        AND unique_id 
                        NOT IN (SELECT C.unique_id 
                            FROM club_member AS C, form AS F 
                            WHERE C.unique_id=F.unique_id 
                            AND F.form_type='Waiver Form')";
                                if ((pg_num_rows(pg_query($connection, $q_w_info))) > 0) {
                                    $i_w_info = "../Resources/red_x.png";
                                } else {
                                    $i_w_info = "../Resources/green_check.png";
                                }

                                // check pass
                                $q_p_info = "SELECT * from club_member 
                        WHERE unique_id=$id
                        AND unique_id 
                        NOT IN (SELECT C.unique_id 
                            FROM club_member AS C, pass AS P 
                            WHERE C.unique_id=P.unique_id)";
                                if ((pg_num_rows(pg_query($connection, $q_p_info))) > 0) { // no pass
                                    $i_p_info = "../Resources/red_x.png";
                                } else {
                                    $i_p_info = "../Resources/green_check.png";
                                }


                                //table view
                                echo sprintf(/** @lang HTML */ "<table id='component_table'>
                    <tr>
                      <th>Component</th>
                      <th>Yes/No</th> 
                    </tr>
                    <tr>
                      <td>Bus Pass</td>
                      <td><img src=%s height=30 width=30></td>
                    </tr>
                    <tr>
                      <td>Medical Form</td>
                      <td><img src=%s height=30 width=30></td>
                    </tr>
                    <tr>
                      <td>Emergency Form</td>
                      <td><img src=%s height=30 width=30></td>
                    </tr>
                    <tr>
                      <td>Waiver Form</td>
                      <td><img src=%s height=30 width=30></td>
                    </tr>
                    <tr>
                      <td>Resort Pass</td>
                      <td><img src=%s height=30 width=30></td>
                    </tr>
                  </table>", $i_b_pass, $i_m_info, $i_e_info, $i_w_info, $i_p_info);


                                // scroll to the bottom after the table is created
                                print /** @lang HTML */
                                    ("<script> scrollToBottom();  </script>");
                            }
                        }
                    } else {
                        echo "<br><br>Please Select an option.";
                    }
                } else {
                    print("<script>scrollToBottom();</script>");
                    echo '<br><br><h1>You mush be logged in to view missing info pertaining to any user!</h1>';
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