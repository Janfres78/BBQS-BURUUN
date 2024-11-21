<?php include 'server/server.php'; ?>

<?php
session_start();
unset($_SESSION['ticket_number']);
unset($_SESSION['tracking_number']);
unset($_SESSION['selected_option']);

// Redirect back to the main page
header("Location: generate_queue.php");  // Replace with the main page URL
exit;
?>
