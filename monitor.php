    <?php include 'server/server.php'; ?>

    <?php
// Fetch the latest 6 ticket log entries to display in the Queue Monitor
$monitorQuery = "SELECT tl.id, tl.ticket_number, tl.tracking_number, tl.chosen_option, r.firstname, r.lastname, tl.status, priority 
                FROM tblticket_logs tl 
                LEFT JOIN tblresident r ON tl.resident_id = r.id
                WHERE tl.is_removed = 0  -- Only fetch logs that are not removed
                ORDER BY tl.log_date DESC, tl.log_time DESC
                LIMIT 6"; // Fetch top 6 tickets
$monitorResult = $conn->query($monitorQuery);
?>


    <!DOCTYPE html>
<html lang="en">
<head>
    <?php include 'templates/header.php'; ?>
    <title>Queue Monitor - BBQS BURU-UN</title>
    <style>
        .monitor-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 10px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
            max-width: 1200px;
            margin: 0 auto;
        }
        .ticket-item {
            display: flex;
            flex-direction: column;
            background-color: #fff;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            width: 280px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        .monitor-info {
            font-size: 1rem;
            font-weight: bold;
            color: #333;
            margin: 5px 0;
        }
        .ticket-status {
            color: #007bff;
        }
        
    </style>
<script>
    // Auto-reload the page every 10 seconds and play a bell sound
    setInterval(() => {
        // Play a bell sound before reloading
        var bellAudio = new Audio('assets/audio/bells.mp3'); // Path to your bell audio file
        bellAudio.play().then(() => {
            // Reload after the sound is played
            setTimeout(() => {
                window.location.reload();
            }, 1500); // Delay to let the sound finish (adjust based on sound length)
        }).catch(error => {
            console.error("Audio playback failed:", error);
            window.location.reload(); // Reload anyway if audio playback fails
        });
    }, 10000); // Interval set to 10 seconds

    // Listen for 'reload-monitor' event and reload the page immediately if set
    window.addEventListener('storage', function(event) {
        if (event.key === 'reload-monitor') {
            var bellAudio = new Audio('assets/audio/bells.mp3');
            bellAudio.play().then(() => {
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            }).catch(error => {
                console.error("Audio playback failed:", error);
                window.location.reload();
            });
            localStorage.removeItem('reload-monitor'); // Clear flag after reload
        }
    });
</script>

</head>
<body>
                <h2 class="text-center">BBQS BURU-UN QUEUEING MONITOR</h2>

                <div class="monitor-container">
                    <?php if ($monitorResult->num_rows > 0): ?>
                        <?php while ($ticket = $monitorResult->fetch_assoc()): ?>
                            <div class="ticket-item">
                                <div class="monitor-info">Ticket Number: <span class="ticket-status"><?= $ticket['ticket_number'] ?></span></div>
                                <div class="monitor-info">Tracking Number: <span class="ticket-status"><?= $ticket['tracking_number'] ?></span></div>
                                <div class="monitor-info">Certificate: <span class="ticket-status"><?= $ticket['chosen_option'] ?></span></div>
                                <div class="monitor-info">Resident Name: <span class="ticket-status"><?= $ticket['firstname'] . ' ' . $ticket['lastname'] ?></span></div>
                                <div class="monitor-info">Priority: <span class="ticket-status"><?= $ticket['priority'] ? 'Senior Citizen/PWDs' : 'Regular' ?></span></div>
                                <div class="monitor-info">Status: <spanx` class="ticket-status"><?= $ticket['status'] ?></spanx></div>
                            </div>

                            <script>
                                // Check if the ticket status is "done"
                                if ("<?= $ticket['status'] ?>" === "done") {
                                    var audio = new Audio('assets/audio/done-queue.mp3');
                                    audio.play();
                                }
                            </script>

                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="monitor-info">No tickets in the queue.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
