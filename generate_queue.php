<?php 
include 'server/server.php'; 

// Set timezone to Manila
date_default_timezone_set('Asia/Manila');

function generateRandomTrackingNumber() {
    return strtoupper(substr(md5(time()), 0, 10));
}

// Function to get the next ticket number
function getNextTicketNumber($conn) {
    // Fetch the current highest ticket number from tblticket_logs
    $query = "SELECT MAX(ticket_number) AS max_ticket FROM tblticket_logs";
    $result = $conn->query($query);

    // Check if the query was successful
    if ($result) {
        $row = $result->fetch_assoc();
        $currentMax = $row['max_ticket'];

        // If the max ticket number is 60 or greater, reset to 1; otherwise, increment by 1
        if ($currentMax >= 60) {
            return 1;
        } else {
            return $currentMax + 1;
        }
    } else {
        // In case of a query error, return 1 to start from the beginning
        return 1;
    }
}


if (isset($_POST['generate_ticket'])) {
    $selectedOption = $_POST['selected_option'];
    $selectedResident = $_POST['selected_resident'];
    $trackingNumber = generateRandomTrackingNumber();
    $date = date('Y-m-d');
    $time = date('h:i:s A'); // Change to 12-hour format with AM/PM
    $isSeniorCitizen = isset($_POST['is_senior_citizen']) ? 1 : 0; // Set priority

    // Get the next ticket number
    $nextTicketNumber = getNextTicketNumber($conn);

    // Insert log into the database
    $insertLogQuery = "INSERT INTO tblticket_logs (ticket_number, tracking_number, chosen_option, resident_id, log_date, log_time, priority) 
                    VALUES ('$nextTicketNumber', '$trackingNumber', '$selectedOption', '$selectedResident', '$date', '$time','$isSeniorCitizen')";

    if ($conn->query($insertLogQuery) === TRUE) {
        $last_id = $conn->insert_id;

// Fetch the resident's name
$fetchResidentsQuery = "SELECT id, firstname, lastname FROM tblresident WHERE id = '$selectedResident' AND is_deleted = 0"; // Only fetch active residents
$residentResult = $conn->query($fetchResidentsQuery);



if ($residentResult && $residentResult->num_rows > 0) {
    $resident = $residentResult->fetch_assoc();
    $residentName = $resident['firstname'] . ' ' . $resident['lastname'];
} else {
    // Handle the case where the resident is not found
    $residentName = "Resident not found";  // Or you can choose another message or behavior
}

// Store in session
$_SESSION['selected_resident'] = $residentName;  // Store full name in session


        $_SESSION['message'] = "Ticket generated successfully!";
        $_SESSION['ticket_number'] = $nextTicketNumber;
        $_SESSION['tracking_number'] = $trackingNumber;
        $_SESSION['selected_option'] = $selectedOption;
        $_SESSION['is_senior_citizen'] = $isSeniorCitizen ? 'Senior Citizen' : ''; // Add priority to session
        $_SESSION['selected_resident'] = $residentName; // Store full name in session
    } else {
        $_SESSION['message'] = "Error: " . $conn->error;
        $_SESSION['success'] = 'danger';
    }
}

// Fetch all logs to display in the table
$fetchLogsQuery = "SELECT tl.*, r.firstname, r.lastname FROM tblticket_logs tl 
                   LEFT JOIN tblresident r ON tl.resident_id = r.id
                   ORDER BY log_date DESC, log_time DESC";
$logsResult = $conn->query($fetchLogsQuery);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <?php include 'templates/header.php'; ?>
    <title>Queueing - BBQS BURU-UN</title>
    <style>
        /* General Styles */
        body {
            font-family: Arial, sans-serif;
        }

            .form-control, select.form-control {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            border: 1px solid #ccc;
            box-shadow: 0 1px 5px rgba(0, 0, 0, 0.1);
            font-size: 16px;
        }

        .form-control:focus {
            outline: none;
            border-color: #007bff;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        .btn-info {
            background-color: #17a2b8;
            color: white;
        }

        .btn-info:hover {
            background-color: #138496;
        }

        .btn-danger {
            background-color: #dc3545;
            color: white;
        }

        .btn-danger:hover {
            background-color: #c82333;
        }



        /* Container for form */
        .generate-form {
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 10px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            margin: 0 auto;
        }

        .form-group {
            margin-bottom: 15px;
        }

        /* Ticket Info Styling */
        #printReceipt {
            border: 1px dashed #007bff;
            padding: 20px;
            margin: 20px 0;
            background-color: #fff;
            text-align: center;
            box-shadow: 0 3px 6px rgba(0, 0, 0, 0.1);
        }

        /* Table Styling */
        .logs-table {
            width: 100%;
            border-collapse: collapse;
        }

        .logs-table th, .logs-table td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .logs-table th {
            background-color: #f1f1f1;
        }

        /* Page-specific styles */
        .page-title {
            margin: 20px 0;
            font-size: 24px;
            font-weight: bold;
        }

        @media print {
            body {
                margin: 0;
                padding: 0;
                width: 52mm;  /* A8 width */
                height: 74mm; /* A8 height */
                font-size: 12px; /* Adjust font size as necessary */
            }
            
            .print-box {
                padding: 5mm; /* Adjust padding for better fit */
                text-align: center; /* Center-align the content */
            }
            
            /* Hide everything else except the print content */
            * {
                display: none;
            }
            
            .print-box, .print-box * {
                display: block; /* Show print box content */
            }
        }
        /* Dropdown Styling */
ul#resident-list {
    list-style-type: none;
    padding: 0;
    margin: 0;
    background-color: #fff;
    border: 1px solid #ccc;
    max-height: 200px;
    overflow-y: auto;
    display: none; /* Initially hidden */
}

ul#resident-list li {
    padding: 8px;
    cursor: pointer;
}

ul#resident-list li:hover {
    background-color: #f0f0f0;
}

    </style>
</head>
<body>
    <?php include 'templates/loading_screen.php'; ?>

    <div class="wrapper">
        <?php include 'templates/main-header.php'; ?>
        <?php include 'templates/sidebar.php'; ?>

        <div class="main-panel">
            <div class="content">
                <div class="panel-header bg-primary-gradient">
                    <div class="page-inner">
                        <h2 class="text-white fw-bold">Generate Queue Number</h2>
                    </div>
                </div>
                <div class="page-inner">
                    <!-- Success message -->
                    <?php if(isset($_SESSION['message'])): ?>
                        <div class="alert alert-success" role="alert">
                            <?php echo $_SESSION['message']; ?>
                        </div>
                        <?php unset($_SESSION['message']); ?>
                    <?php endif; ?>

    <!-- Form to generate ticket -->
    <div class="generate-form">
        <form method="POST" onsubmit="return confirmQueue()">
            <div class="form-group">
                <label for="selected_option">Select Option</label>
                <select name="selected_option" class="form-control" required>
                <option value="Barangay Certificate">Barangay Certificate</option>
                <option value="Barangay Business Clearance">Barangay Business Clearance</option>
                <option value="Business Permit">Business Permit</option>
                <option value="Building Permit">Building Permit</option>
                <option value="Certificate of Residency">Certificate of Residency</option>
                <option value="Certificate Of Indigency">Certificate Of Indigency</option>
            </select>
            <label>
                <input type="checkbox" name="is_senior_citizen" value="1"> Senior Citizen/PWDs
            </label>
        </div>

        <div class="form-group">
            <label for="search_resident">Search Resident</label>
            <input type="text" id="search_resident" class="form-control search-input" placeholder="Search Resident by Name" onkeyup="searchResident()" required>
            <ul id="resident-list" class="dropdown-list"></ul> <!-- Display search results here -->
        </div>



        <div class="form-group">
            <input type="hidden" id="selected_resident" name="selected_resident" value="">
        </div>  

        <button type="submit" name="generate_ticket" class="btn btn-info">Generate Ticket</button>
    </form>
</div>

<script>
// JavaScript for live search functionality
// JavaScript for live search functionality
function searchResident() {
    var input = document.getElementById('search_resident').value;
    var residentList = document.getElementById('resident-list');

    if (input.length > 0) {
        // Send AJAX request to search residents based on input
        var xhr = new XMLHttpRequest();
        xhr.open("GET", "search_resident.php?query=" + input, true);
        xhr.onreadystatechange = function () {
            if (xhr.readyState == 4 && xhr.status == 200) {
                residentList.innerHTML = xhr.responseText;
                residentList.style.display = 'block'; // Show the list
            }
        };
        xhr.send();
    } else {
        residentList.innerHTML = "";
        residentList.style.display = 'none'; // Hide the list if input is empty
    }
}

// Function to set the selected resident
function setResident(id, name) {
    document.getElementById('selected_resident').value = id;
    document.getElementById('search_resident').value = name;
    document.getElementById('resident-list').innerHTML = "";
    document.getElementById('resident-list').style.display = 'none'; // Hide the list after selecting
}
</script>



<script>
    // JavaScript function to confirm the queue action
    function confirmQueue() {
        // Display the confirmation dialog
        const userChoice = confirm("Do you want to continue with the queue?");
        
        // Return true to submit the form if the user clicks "OK"
        // Return false to cancel if the user clicks "Cancel"
        return userChoice;
    }
</script>

                    <!-- Print Receipt Section -->
                    <?php if (isset($_SESSION['ticket_number'])): ?>
                        <div id="printReceipt" class="container">
                            <h2>BARANGAY BURU-UN</h2>
                            <div class="ticket-number">
                                <?php echo $_SESSION['ticket_number']; ?><br>
                            <p> Tracking Number: <?php echo $_SESSION['tracking_number']; ?> </p>
                            </div>
                            <strong><?php echo $_SESSION['selected_option']; ?></strong><br> 
                            Name: <?php echo $_SESSION['selected_resident']; ?>
                            <p>The ticket is not for sale.</p>
                            <p>Valid Only on Issued date.</p>
                            <p>Date: <?php echo date('Y-m-d'); ?></p>
                            <p>Time: <?php echo date('h:i:s A'); ?></p>

                            <!-- Display message for Senior Citizens -->
                            <?php if ($_SESSION['is_senior_citizen']): ?>
                                <p><strong>Note:</strong> This ticket is marked as priority for Senior Citizens/PWDs.</p>
                            <?php endif; ?>
                        </div>
                        <center>
                            <button class="btn btn-info" onclick="printDiv('printReceipt', '')">
                                <i class="fa fa-print"></i> Print Receipt
                            </button>
                        </center>
                        <?php unset($_SESSION['ticket_number'], $_SESSION['tracking_number'], $_SESSION['selected_option'], $_SESSION['selected_resident']); ?>
                    <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <?php include 'templates/footer.php'; ?>

     <script>
        function printDiv(divName, additionalText) {
            // Create an iframe for printing
            var iframe = document.createElement('iframe');
            document.body.appendChild(iframe);

            // Style the iframe for A8 size
            iframe.style.position = 'absolute';
            iframe.style.width = '0';
            iframe.style.height = '0';
            iframe.style.border = 'none';

            var doc = iframe.contentWindow.document;
            doc.open();
            
            // Write the content
            doc.write('<html><head><title>Print Receipt</title>');
            doc.write('<style>');
            doc.write('@media print { body { margin: 0; padding: 0; width: 52mm; height: 74mm; font-size: 12px; } }');
            doc.write('.print-box { padding: 5mm; text-align: center; }');
            doc.write('</style></head><body>');

            // Add the additional text
            doc.write('<h2>' + additionalText + '</h2>');

            // Add the content of the div
            var printContents = document.getElementById(divName).innerHTML;
            doc.write('<div class="print-box">' + printContents + '</div>');

            doc.write('</body></html>');
            doc.close();

            // Trigger print
            iframe.contentWindow.focus();
            iframe.contentWindow.print();

            // Remove the iframe after printing
            iframe.parentNode.removeChild(iframe);
        }
    </script>
</body>
</html>
