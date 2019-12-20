/**
 * @file downloader.js universal javascript file to download HTML tables into CSV format.
 * @author Landon Shenberger
 */

/**
 * @function downloadCSV creates a download link for a csv Blob object and clicks the download link to download the
 * csv.
 * @param {array} csv file represented as an array with commas (obviously) separating columns in each row, while a newline
 * character (\n) separates the rows.
 * @param {string} filename the name of the file to be downloaded. The file extension .csv is added automatically and the
 * file name before it is downloaded, along with a timestamp.
 */
function downloadCSV(csv, filename) {
    var csvFile;
    var downloadLink;
    // Add timestamp to the filename
    let date = new Date();
    let dateString = date.toString();
    filename = filename.concat("-", dateString, '.csv'); // add date and csv file extension
    csvFile = new Blob([csv], {type: "text/csv"});
    // Create download link
    downloadLink = document.createElement("a");
    // File name
    downloadLink.download = filename;
    // Create a link to the file
    downloadLink.href = window.URL.createObjectURL(csvFile);
    // Hide download link
    downloadLink.style.display = "none";
    // Add the link to DOM
    document.body.appendChild(downloadLink);
    // Click download link automatically
    downloadLink.click();
}

/**
 * @function exportTableToCSV reads the current page being displayed for any HTML table and converts those tables into a
 * CSV array. The helper function downloadCSV takes in the generated array to download it.
 * @param filename {string} the file name (which is "table" by default) to be downloaded. Do not include the .csv extension
 * as it is added automatically with a timestamp when the downloadCSV function is called.
 */
function exportTableToCSV(filename = "table") {
    var csv = []; // new csv array
    var rows = document.querySelectorAll("table tr");

    for (var i = 0; i < rows.length; i++) {
        var row = [], cols = rows[i].querySelectorAll("td, th");

        for (var j = 0; j < cols.length ; j++)
            row.push(cols[j].innerText);
        // Check for any empty rows and do not add to them
        if (!(row.length === 0)){
            csv.push(row.join(","));
        }
    }
    // Download CSV file
    downloadCSV(csv.join("\n"), filename);
}

