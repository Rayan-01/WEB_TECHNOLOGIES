<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "library_management");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle book removal
if (isset($_GET['remove'])) {
    $removeId = $_GET['remove'];
    $sql = "DELETE FROM books WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $removeId);
    $stmt->execute();
    $stmt->close();
}

// Search functionality
$searchTerm = '';
if (isset($_POST['search']) && !empty($_POST['search'])) {
    $searchTerm = $_POST['search'];
}

// Function to display books
function displayBooks($searchTerm)
{
    global $conn;

    // Prepare SQL query to fetch books
    if ($searchTerm) {
        $sql = "SELECT * FROM books WHERE title LIKE ? OR author LIKE ? OR genre LIKE ?";
        $stmt = $conn->prepare($sql);
        $searchTerm = "%" . $searchTerm . "%";
        $stmt->bind_param("sss", $searchTerm, $searchTerm, $searchTerm);
    } else {
        $sql = "SELECT * FROM books";
        $stmt = $conn->prepare($sql);
    }

    // Execute the query
    $stmt->execute();
    $result = $stmt->get_result();

    // Display books in the table
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>" . htmlspecialchars($row['id']) . "</td>
                <td>" . htmlspecialchars($row['title']) . "</td>
                <td>" . htmlspecialchars($row['author']) . "</td>
                <td>" . htmlspecialchars($row['yearofpublication']) . "</td>
                <td>" . htmlspecialchars($row['genre']) . "</td>
                <td><a href='index.php?remove=" . $row['id'] . "' class='removeBtn'>Remove</a></td>
            </tr>";
    }

    // Close the statement
    $stmt->close();
}
?>
