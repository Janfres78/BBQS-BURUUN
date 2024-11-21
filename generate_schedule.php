<?php include 'server/server.php'; ?>
<?php 

// Initialize events array
$events = isset($_SESSION['events']) ? $_SESSION['events'] : [];

// Handle event submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['event_date'], $_POST['event_description'])) {
    $event_date = $_POST['event_date'];
    $event_description = $_POST['event_description'];

    // Insert event into the database
    $stmt = $conn->prepare("INSERT INTO tbl_events (event_date, event_description) VALUES (?, ?)");
    $stmt->bind_param("ss", $event_date, $event_description);
    
    if ($stmt->execute()) {
        $_SESSION['message'] = "Event added successfully!";
        $_SESSION['success'] = "success";
    } else {
        $_SESSION['message'] = "Error adding event: " . $stmt->error;
        $_SESSION['success'] = "danger";
    }

    $stmt->close();

    // Optionally fetch all events from the database to keep the session updated
    $events = [];
    $result = $conn->query("SELECT event_date, event_description FROM tbl_events");
    while ($row = $result->fetch_assoc()) {
        $events[$row['event_date']] = $row['event_description'];
    }
    $_SESSION['events'] = $events;

    // Redirect to avoid resubmission
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

// Fetch all events from the database
$result = $conn->query("SELECT event_date, event_description FROM tbl_events");
$events = [];
while ($row = $result->fetch_assoc()) {
    $events[$row['event_date']][] = $row['event_description']; // Store events as an array
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include 'templates/header.php'; ?>
    <title>Event Schedule - BBQS BURU-UN</title>
    <style>
        /* General styles for the calendar */
        #calendar {
            margin: 20px 0;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        #calendar table {
            width: 100%;
            border-collapse: collapse;
        }

        #calendar th {
            background-color: #007bff;
            color: white;
            padding: 10px;
            text-align: center;
        }

        #calendar td {
            padding: 15px;
            text-align: center;
            border: 1px solid #ddd;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.3s;
        }

        #calendar td:hover {
            background-color: #e0e7ff;
            transform: scale(1.05);
        }

        #calendar .today {
            background-color: #ffeb3b;
            font-weight: bold;
        }

        .calendar-controls {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 10px;
        }

        .calendar-controls button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .calendar-controls button:hover {
            background-color: #0056b3;
        }

        .month-year {
            font-size: 1.5em;
            font-weight: bold;
            color: white;
            padding: 10px;
        }

        /* Event form styles */
        form {
            margin: 20px 0;
            display: flex;
            flex-direction: column;
        }

        form label {
            margin: 10px 0 5px;
        }

        form input[type="date"],
        form input[type="text"] {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        form button {
            margin-top: 10px;
            padding: 10px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        form button:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
    <?php include 'templates/loading_screen.php'; ?>

    <div class="wrapper">
        <!-- Main Header -->
        <?php include 'templates/main-header.php'; ?>
        <!-- End Main Header -->

        <!-- Sidebar -->
        <?php include 'templates/sidebar.php'; ?>
        <!-- End Sidebar -->
 
        <div class="main-panel">
            <div class="content">
                <div class="panel-header bg-primary-gradient">
                    <div class="page-inner">
                        <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row">
                            <div>
                                <h2 class="text-white fw-bold">Event Calendar</h2>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="page-inner">
                    <?php if(isset($_SESSION['message'])): ?>
                        <div class="alert alert-<?php echo $_SESSION['success']; ?> <?= $_SESSION['success']=='danger' ? 'bg-danger text-light' : null ?>" role="alert">
                            <?php echo $_SESSION['message']; ?>
                        </div>
                        <?php unset($_SESSION['message']); ?>
                    <?php endif ?>
                    <div class="row mt--2">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex flex-wrap pb-2 justify-content-between">
                                        <div class="px-2 pb-2 pb-md-0 text-center">
                                            <img src="assets/uploads/<?= $brgy_logo ?>" class="img-fluid" width="100">
                                        </div>
                                        <div class="px-2 pb-2 pb-md-0 text-center">
                                            <h2 class="fw-bold mt-3"><?= ucwords($brgy) ?></h2>
                                        </div>
                                        <div class="px-2 pb-2 pb-md-0 text-center">
                                            <img src="assets/img/brgy-logo.png" class="img-fluid" width="100" style="visibility:hidden;">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card">
                                <div class="card-header">
                                    <div class="card-head-row">
                                        <div class="card-title">Event Scheduling</div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <!-- Event Form -->
                                        <form method="POST" class="mb-4">
                                            <label for="event_date">Event Date:</label>
                                            <input type="date" id="event_date" name="event_date" required>
                                            <label for="event_description">Event Description:</label>
                                            <input type="text" id="event_description" name="event_description" required>
                                            <button type="submit">Add Event</button>
                                        </form>

                                        <div id="calendar"></div>

                                        <script>
    // Global variables to track the current month and year
    let currentYear = new Date().getFullYear();
    let currentMonth = new Date().getMonth(); // 0-11, where 0 is January

    function updateCalendar() {
        const year = currentYear;
        const month = currentMonth;
        const day = new Date().getDate();
        
        const calendar = document.getElementById('calendar');
        calendar.innerHTML = ''; // Clear previous calendar

        // Get the number of days in the month
        const daysInMonth = new Date(year, month + 1, 0).getDate();
        const firstDay = new Date(year, month, 1).getDay();
        const today = new Date();
        const monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];

        let table = '<table>';
        table += `<thead>`;
        table += `<tr><th colspan="7" class="month-year">`;
        table += `<div class="calendar-controls">`;
        table += `<button onclick="changeMonth(-1)"><</button>`;
        table += `<span>${monthNames[month]} ${year}</span>`;
        table += `<button onclick="changeMonth(1)">></button>`;
        table += `</div>`;
        table += `</th></tr>`;
        table += `<tr>`;
        ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'].forEach(dayName => {
            table += `<th>${dayName}</th>`;
        });
        table += `</tr></thead><tbody><tr>`;

        // Empty cells before the first day of the month
        for (let i = 0; i < firstDay; i++) {
            table += '<td></td>';
        }

        // Days of the month
        for (let currentDay = 1; currentDay <= daysInMonth; currentDay++) {
            const isToday = today.getFullYear() === year && today.getMonth() === month && today.getDate() === currentDay;
            let cellClass = '';
            if (isToday) {
                cellClass = 'today';
            }

            // Check if there are events for the current day
            const eventDate = `${year}-${String(month + 1).padStart(2, '0')}-${String(currentDay).padStart(2, '0')}`;
            const eventDescription = <?= json_encode($events) ?>[eventDate] || '';

            table += `<td class="${cellClass}" onclick="editEvent('${eventDate}')">${currentDay}<br>${eventDescription}</td>`;
            
            if ((firstDay + currentDay) % 7 === 0) {
                table += `</tr><tr>`;
            }
        }

        // Fill in remaining empty cells
        const totalCells = firstDay + daysInMonth;
        const remainingCells = (7 - (totalCells % 7)) % 7;
        for (let i = 0; i < remainingCells; i++) {
            table += '<td></td>';
        }
        
        table += `</tr></tbody></table>`;
        calendar.innerHTML = table;
    }

    function changeMonth(direction) {
        // Update the month based on the direction
        currentMonth += direction;

        // Adjust the year if the month goes out of bounds
        if (currentMonth < 0) {
            currentMonth = 11;
            currentYear--;
        } else if (currentMonth > 11) {
            currentMonth = 0;
            currentYear++;
        }

        // Update the calendar after changing the month
        updateCalendar();
    }

    function editEvent(date) {
        const eventDescription = prompt("Edit event for " + date + ":", <?= json_encode($events) ?>[date] || '');
        if (eventDescription !== null) {
            document.querySelector('input[name="event_date"]').value = date;
            document.querySelector('input[name="event_description"]').value = eventDescription;
            document.querySelector('form').submit();
        }
    }

    // Initial calendar load
    updateCalendar();
</script>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php include 'templates/footer.php'; ?>
    </div>
</body>
</html>
