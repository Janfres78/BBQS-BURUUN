// fetch_residents.php
<?php
include 'server/server.php'; // Include database connection

if (isset($_POST['purok']) && !empty($_POST['purok'])) {
    $purok = $_POST['purok'];
    $query = "SELECT id, firstname, lastname FROM tblresident WHERE purok = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $purok);
    $stmt->execute();
    $result = $stmt->get_result();

    $residents = [];
    while ($row = $result->fetch_assoc()) {
        $residents[] = $row;
    }

    // Return JSON response
    echo json_encode($residents);
}
?>
