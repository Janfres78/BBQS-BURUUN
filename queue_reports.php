<?php
include 'server/server.php'; // Include your server connection

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
    <title>Queue Reports - BBQS BURU-UN</title>
</head>
<body>
    <div class="wrapper">
        <?php include 'templates/main-header.php'; ?>
        <?php include 'templates/sidebar.php'; ?>

        <div class="main-panel">
            <div class="content">
                <h2>Queue Reports</h2>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Ticket Number</th>
                                <th>Tracking Number</th>
                                <th>Chosen Option</th>
                                <th>Resident</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($log = $logsResult->fetch_assoc()): ?>
                                <tr>
                                    <td><?= $log['id']; ?></td>
                                    <td><?= $log['tracking_number']; ?></td>
                                    <td><?= $log['chosen_option']; ?></td>
                                    <td><?= $log['firstname'] . ' ' . $log['lastname']; ?></td>
                                    <td><?= $log['log_date']; ?></td>
                                    <td><?= $log['log_time']; ?></td>
                                    <td><?= $log['status']; ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php include 'templates/footer.php'; ?>
        </div>
    </div>
</body>
</html>
