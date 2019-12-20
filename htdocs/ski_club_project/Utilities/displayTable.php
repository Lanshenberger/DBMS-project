<?php
/**
 * displayTable.php
 * A file containing a php function to display a query result as an HTML table
 */

/**
 * A specific function used to display HTML tables given a club database result set
 * @param $result resource the result set needing to be displayed
 * @param array $headers an optional parameter used for the headers of the table. If the number of header names
 * @param $connection resource the connection to the database. It is needed to display additional data in tuples by
 * performing queries.
 * does not match the arity of the result set, the supplied array is then ignored.
 */
function displayTable($connection, $result , $headers = array("empty")){
            $cursor = pg_fetch_all($result);
            printf("<table>\n");
            printf("<tr>\n");
            // if there is no header array supplied or the supplied header array's count does not match the
            // arity of the table, the attribute names are used as the headers.
            if ($headers[0] == "empty" || pg_num_fields($result) != count($headers)){
                for($i = 0; $i < pg_num_fields($result); $i++) {
                    printf("<th>" . pg_field_name($result, $i) . "</th>\n");
                }
            }
            else{
                for ($i = 0; $i < count($headers); $i++){
                    printf("<th>" . $headers[$i] . "</th>");
                }
            }
            printf("<tr>\n");
            for ($i = 0; $i < count($cursor); $i++){
                printf("<tr>\n");
                $row = $cursor[$i];
                for ($j = 0; $j < count($row); $j++){
                    $field_name = pg_field_name($result, $j);
                    $print_value = $row[$field_name];
                    // Account for true, false and null.
                    switch ($print_value){
                        case "":
                            $print_value = "N/A";
                            break;
                        case "t":
                            $print_value = "Yes";
                            break;
                        case "f";
                            $print_value = "No";
                    }
                    // Get table name of this data field
                    $table_name = pg_field_table($result, $j);
                    // Check for associations between tables and get meta data, and add to data print value.
                    // utilities.php contains some helper functions to get additional data and is included in
                    // every report file.
                    switch ($field_name){
                        // unique_id always refers to a member.
                        case 'unique_id':
                            // Here we want to set the club members column data to include the name
                            // and id. The $print_value should already contain the id, but we do not want to append
                            // to it because the getMemberMetaData function returns the id; thus, we just overwrite the
                            // variable. If the table we are dealing with is the club_member table, it already displays
                            // this additional information so we ignore it.
                            if (!($table_name == 'club_member')){
                                // The data value may be null, ensure that here
                                if ($club_mem = getMemberMetaData($connection, $print_value)){
                                    $print_value = $club_mem;
                                }
                            }
                           break;
                        case 'club_no.':
                            // here we should collect meta data for the club
                            if (!($table_name == 'ski_club')){
                                if ($ski_club = getClubMetaData($connection, $print_value)){
                                    $print_value = $ski_club;
                                }
                            }
                    }
                    printf("<td>".$print_value ."</td>");
                }
                printf("</tr>");
            }
            printf("</table>");
    }
