<?php include 'server/server.php'; ?>

<?php
// Reset Queue logic
if (isset($_POST['reset_queue'])) {
    $resetQuery = "UPDATE tblticket_logs SET is_removed = 1"; // Marks all logs as removed
    if ($conn->query($resetQuery) === TRUE) {
        $_SESSION['message'] = "Queue reset successfully!";
        $_SESSION['success'] = 'success';
    } else {
        $_SESSION['message'] = "Error resetting queue: " . $conn->error;
        $_SESSION['success'] = 'danger';
    }
}

// Fetch all logs to display in the table (filter out removed logs)
$fetchLogsQuery = "SELECT tl.*, r.firstname, r.lastname FROM tblticket_logs tl 
                LEFT JOIN tblresident r ON tl.resident_id = r.id
                WHERE tl.is_removed = 0
                ORDER BY log_date DESC, log_time DESC";
$logsResult = $conn->query($fetchLogsQuery);

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <?php include 'templates/header.php'; ?>
    <link rel="stylesheet" href="assets/js/plugin/dataTables.dateTime.min.css">
    <link rel="stylesheet" href="assets/js/plugin/datatables/Buttons-1.6.1/css/buttons.dataTables.min.css">
    <title>Queue Logs - BBQS BURU-UN</title>
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
                        <h2 class="text-white fw-bold">Queue Logs</h2>
                    </div>
                </div>
                <div class="page-inner">
                    <div class="row mt--2">
                        <div class="col-md-12">
                            <!-- Success message -->
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
                                            <th>Action</th>
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
                                                <td>
                                                    <?php if ($log['status'] !== 'Done'): ?>
                                                        <form action="update_status.php" method="POST" style="display:inline;">
                                                            <input type="hidden" name="ticket_id" value="<?= $log['id']; ?>">
                                                            <button type="submit" name="mark_done" class="btn btn-success">Done</button>
                                                        </form>
                                                    <?php else: ?>
                                                        <span class="text-muted">Completed</span>
                                                    <?php endif; ?>
                                                </td>
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
    <script src="assets/js/plugin/moment/moment.min.js"></script>
    <script src="assets/js/plugin/dataTables.dateTime.min.js"></script>
    <script src="assets/js/plugin/datatables/Buttons-1.6.1/js/dataTables.buttons.min.js"></script>
    <script src="assets/js/plugin/datatables/Buttons-1.6.1/js/buttons.print.min.js"></script>
    <script>
$(document).ready(function() {
    // DataTable initialization without print button
    var table = $('#reportTable').DataTable({
        "order": [[ 4, "desc" ]], // Order by date column
        dom: 'Bfrtip',
        buttons: [
            {
                text: 'Reset Queue',
                className: 'btn btn-danger', // Added 'text-white' for white font
                action: function () {
                    if (confirm('Are you sure you want to reset the queue?')) {
                        // Create a form dynamically and submit it
                        var form = $('<form>', {
                            method: 'POST',
                            action: '' // Replace with the correct action URL if needed
                        });

                        form.append($('<input>', {
                            type: 'hidden',
                            name: 'reset_queue',
                            value: 'true'
                        }));

                        $('body').append(form);
                        form.submit(); // Submit the form
                    }
                }
            }
        ]
    });
});


function confirmResetQueue() {
    if (confirm('Are you sure you want to reset the queue?')) {
        return true; // Proceed with form submission
    }
    return false; // Prevent form submission
}

</script>


</body>
</html>
