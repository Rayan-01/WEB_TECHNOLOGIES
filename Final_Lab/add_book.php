<?php

//Add Book Form Validation and All Books Listing

$conn = new mysqli("localhost", "root", "", "library_management");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$action = $_POST['action'];
$title = $_POST['title'];
$author = $_POST['author'];
$yearofpublication = $_POST['yearofpublication'];
$genre = $_POST['genre'];

if ($action == 'add') {
    $sql = "INSERT INTO books (title, author, yearofpublication, genre) VALUES ('$title', '$author', '$yearofpublication', '$genre')";
}

if ($conn->query($sql) === TRUE) {
    echo "Record updated successfully";
} else {
    echo "Error updating record: " . $conn->error;
}

$conn->close();
header("Location: index.php");
?>