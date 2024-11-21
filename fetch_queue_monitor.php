<?php
include 'server/server.php';

$fetchActiveQueueQuery = "SELECT id, tracking_number, firstname, lastname FROM tblticket_logs 
                          LEFT JOIN tblresident ON tblticket_logs.resident_id = tblresident.id 
                          WHERE status != 'Done'
                          ORDER BY log_date ASC, log_time ASC";
$activeQueueResult = $conn->query($fetchActiveQueueQuery);

while($queue = $activeQueueResult->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . $queue['tracking_number'] . "</td>";
    echo "<td>" . $queue['firstname'] . " " . $queue['lastname'] . "</td>";
    echo "</tr>";
}
?>
