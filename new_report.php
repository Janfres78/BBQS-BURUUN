<?php include 'server/server.php'; ?>

<?php
// Fetch all logs data for the new report (including "removed" logs)
$fetchLogsQuery = "SELECT tl.*, r.firstname, r.lastname FROM tblticket_logs tl 
                LEFT JOIN tblresident r ON tl.resident_id = r.id
                ORDER BY log_date DESC, log_time DESC";  // No filter for "is_removed"
$logsResult = $conn->query($fetchLogsQuery);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <?php include 'templates/header.php'; ?>
    <link rel="stylesheet" href="assets/js/plugin/datatables/datatables.min.css">
    <link rel="stylesheet" href="assets/js/plugin/datatables/Buttons-1.6.1/css/buttons.dataTables.min.css">
    <title>Print Report - BBQS BURU-UN</title>
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
                        <h2 class="text-white fw-bold">Queue Reports</h2>
                    </div>
                </div>
                <div class="page-inner">
                    <div class="row mt--2">
                        <div class="col-md-12">
                            <!-- Success/Error Messages (if any) -->
                            <?php if(isset($_SESSION['message'])): ?>
                                <div class="alert alert-<?php echo $_SESSION['success']; ?>" role="alert">
                                    <?php echo $_SESSION['message']; ?>
                                </div>
                                <?php unset($_SESSION['message']); ?>
                                <?php unset($_SESSION['success']); ?>
                            <?php endif; ?>
                            
                            <!-- Logs Table -->
                            <div class="table-responsive">
                                <table id="reportTable" class="display table table-striped logs-table">
                                    <thead>
                                        <tr>
                                            <th>Ticket Number</th>
                                            <th>Tracking Number</th>
                                            <th>Chosen Option</th>
                                            <th>Resident</th>
                                            <th>Date</th>
                                            <th>Time</th>
                                            <th>General/Priority</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while($log = $logsResult->fetch_assoc()): ?>
                                            <tr>
                                                <td><?= $log['ticket_number']; ?></td>
                                                <td><?= $log['tracking_number']; ?></td>
                                                <td><?= $log['chosen_option']; ?></td>
                                                <td><?= $log['firstname'] . ' ' . $log['lastname']; ?></td>
                                                <td><?= $log['log_date']; ?></td>
                                                <td><?= $log['log_time']; ?></td>
                                                <td><?= $log['priority'] ? 'Senior Citizen/PWDs' : 'Regular'; ?></td>
                                                <td><?= $log['status']; ?></td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php include 'templates/footer.php'; ?>
        </div>
    </div>

    <!-- Include DataTables and other Scripts -->
    <script src="assets/js/plugin/datatables/datatables.min.js"></script>
    <script src="assets/js/plugin/datatables/Buttons-1.6.1/js/dataTables.buttons.min.js"></script>
    <script src="assets/js/plugin/datatables/Buttons-1.6.1/js/buttons.print.min.js"></script>
    <script>
    $(document).ready(function() {
        // DataTable initialization with print button
        var table = $('#reportTable').DataTable({
            "order": [[ 4, "desc" ]], // Order by date column
            dom: 'Bfrtip',  // Allows buttons to be displayed in the DataTable interface
            buttons: [
                {
                    extend: 'print',  // This is the print button configuration
                    text: 'Print Report',
                    className: 'btn btn-info',  // Optional: customize button style
                    title: 'Queue Ticket Report - BBQS BURU-UN',  // Title of the printed report
                    messageTop: 'Report generated on: ' + new Date().toLocaleString(), // Optional: Add a message on top of the print
                    customize: function (win) {
                        // Customize the print layout if needed
                        $(win.document.body).css('font-size', '12pt');  // Example: change font size
                    }
                }
            ]
        });
    });
    </script>
</body>
</html>
