<?php

//Borrow Book Form Validation and Invoice Printing

ini_set('display_errors', 1);
error_reporting(E_ALL);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $studentname = $_POST['studentname'];
    $studentid = $_POST['studentid'];
    $email = $_POST['email']; 
    $bt = $_POST['booktitle'];
    $bd = $_POST['borrowdate'];
    $rn = $_POST['returndate'];
    $tn = $_POST['token'];
    $fees = $_POST['fees'];
    
    $errors = [];
    $borrowDate = strtotime($bd);
    $returnDate = strtotime($rn);

    if (empty($studentname) || !preg_match('/^[a-zA-Z\s]+$/', $studentname)) {
        $errors[] = 'Student Name is invalid';
    }
    if (empty($studentid) || !preg_match('/^\d{2}-\d{5}-\d{1}$/', $studentid)) {
        $errors[] = 'Invalid Student ID format';
    }
    if (empty($email) || !preg_match('/^\d{2}-\d{5}-\d@student\.aiub\.edu$/', $email)) {
        $errors[] = 'Invalid email format';
    }
    if (!$borrowDate || !$returnDate || $returnDate <= $borrowDate) {
        $errors[] = 'Invalid Borrow/Return Dates';
    }
    $dateDiff = ($returnDate - $borrowDate) / 86400;
    if ($dateDiff > 10 || $dateDiff <= 0) {
        $errors[] = 'Borrow period must be between 1 and 10 days';
    }
    if (!ctype_digit($tn)) {
        $errors[] = 'Token Number must contain only numbers';
    }
    if (!is_numeric($fees) || $fees <= 0) {
        $errors[] = 'Fees must be a positive number';
    }

    if (isset($_COOKIE["borrowed_books"])) {
        $borrowedBooks = json_decode($_COOKIE["borrowed_books"], true);
        if (in_array($bt, $borrowedBooks)) {
            $errors[] = "'$bt' Is Not Avaiable";
        }
    } else {
        $borrowedBooks = [];
    }

    if ($errors) {
        foreach ($errors as $error) {
            echo '<b style="color: red;">Error: ' . $error . '</b>';
        }
    } else {
        $borrowedBooks[] = $bt;
        setcookie("borrowed_books", json_encode($borrowedBooks), time() + 120, "/");

        echo "<div style='text-align: center; font-family: Arial, sans-serif; padding: 20px; background-color: #f4f7fa;'>";
        echo "<p style='font-size: 2.5rem; font-weight: bold; color: #333;'>Invoice</p>";
        echo "<table style='width: 60%; margin: auto; border-collapse: collapse; background-color: #ffffff; border-radius: 8px; box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);'>";
        
        echo "<tr style='background-color:rgb(78, 223, 216); color: white; text-align: center;'>
                <th style='border: 1px solid #ddd; padding: 15px; font-size: 1.1rem;'>Item</th>
                <th style='border: 1px solid #ddd; padding: 15px; font-size: 1.1rem;'>Details</th>
              </tr>";
        
        echo "<tr style='background-color: #f9f9f9;'>
                <td style='border: 1px solid #ddd; padding: 12px;'>Student Name</td>
                <td style='border: 1px solid #ddd; padding: 12px;'>$studentname</td>
              </tr>";
        echo "<tr style='background-color: #ffffff;'>
                <td style='border: 1px solid #ddd; padding: 12px;'>Student ID</td>
                <td style='border: 1px solid #ddd; padding: 12px;'>$studentid</td>
              </tr>";
        echo "<tr style='background-color: #f9f9f9;'>
                <td style='border: 1px solid #ddd; padding: 12px;'>Email</td>
                <td style='border: 1px solid #ddd; padding: 12px;'>$email</td>
              </tr>";
        echo "<tr style='background-color: #ffffff;'>
                <td style='border: 1px solid #ddd; padding: 12px;'>Book Title</td>
                <td style='border: 1px solid #ddd; padding: 12px;'>$bt</td>
              </tr>";
        echo "<tr style='background-color: #f9f9f9;'>
                <td style='border: 1px solid #ddd; padding: 12px;'>Borrow Date</td>
                <td style='border: 1px solid #ddd; padding: 12px;'>$bd</td>
              </tr>";
        echo "<tr style='background-color: #ffffff;'>
                <td style='border: 1px solid #ddd; padding: 12px;'>Return Date</td>
                <td style='border: 1px solid #ddd; padding: 12px;'>$rn</td>
              </tr>";
        echo "<tr style='background-color: #f9f9f9;'>
                <td style='border: 1px solid #ddd; padding: 12px;'>Token Number</td>
                <td style='border: 1px solid #ddd; padding: 12px;'>$tn</td>
              </tr>";
        echo "<tr style='background-color: #ffffff;'>
                <td style='border: 1px solid #ddd; padding: 12px;'>Fees</td>
                <td style='border: 1px solid #ddd; padding: 12px;'>$fees</td>
              </tr>";
        
        echo "</table>";
        
        echo "<div style='text-align: center; margin-top: 30px;'>
                <button style='background-color: #28a745; color: white; border: none; padding: 14px 28px; margin: 10px; cursor: pointer; border-radius: 8px; font-size: 1.1rem; transition: background-color 0.3s;' onclick='window.print()'>Print Invoice</button>
              </div>";
        echo "</div>";
         

    }
} else {
    echo '<b>Error: Invalid request method</b>';
}

?>