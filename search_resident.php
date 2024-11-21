<?php
include 'server/server.php'; // Include the connection to the database

if (isset($_GET['query'])) {
    $query = $_GET['query'];
    
    // Search for residents based on the input query
    $sql = "SELECT id, firstname, lastname FROM tblresident WHERE (firstname LIKE '%$query%' OR lastname LIKE '%$query%') AND is_deleted = 0";
    $result = $conn->query($sql);    
    
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo '<li onclick="setResident(' . $row['id'] . ', \'' . $row['firstname'] . ' ' . $row['lastname'] . '\')">'
                . $row['firstname'] . ' ' . $row['lastname'] . '</li>';
        }
    } else {
        echo '<li>No results found</li>';
    }
}
?>
