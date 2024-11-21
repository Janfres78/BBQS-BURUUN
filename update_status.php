<?php
// Include the server connection
include 'server/server.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ticket_id'])) {
    // Get the ticket ID from the form
    $ticket_id = $_POST['ticket_id'];

    // Update the ticket status to "Done"
    $updateQuery = "UPDATE tblticket_logs SET status = 'Done' WHERE id = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("i", $ticket_id);

    if ($stmt->execute()) {
        // Optionally, set a session message to confirm success
        $_SESSION['message'] = "Ticket marked as Done successfully!";
        $_SESSION['success'] = 'success';
    } else {
        $_SESSION['message'] = "Error updating ticket: " . $stmt->error;
        $_SESSION['success'] = 'danger';
    }

    // Close statement and connection
    $stmt->close();
    $conn->close();

    // Redirect back to view_report.php
    header("Location: view_report.php");
    exit();
}
?>
