<?php
include 'server/server.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ticket_id'])) {
    $ticketId = intval($_POST['ticket_id']);

    // Update the ticket status to "done"
    $updateQuery = "UPDATE tblticket_logs SET status = 'done' WHERE id = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param('i', $ticketId);
    
    if ($stmt->execute()) {
        // Redirect back to the report page or wherever appropriate
        header("Location: view_report.php?success=1");
    } else {
        echo "Error updating ticket: " . $stmt->error;
    }
    
    $stmt->close();
}
?>
