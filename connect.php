<?php
// Start the session
session_start();

// Connect to the database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "gym";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize error variable
$error = "";

// Check if form data was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Prepare SQL statement to prevent SQL injection
    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if user exists
    if ($result->num_rows > 0) {
        // Fetch user data
        $user = $result->fetch_assoc();

        // Verify password
        if (password_verify($password, $user['password'])) {
            // Successful login, redirect to dashboard
            $_SESSION['user'] = $user['email'];  // Start a session for the user
            header("Location: dashboard.html");  // Redirect to dashboard page
            exit(); // Prevent further script execution
        } else {
            // Incorrect password
            $error = "Incorrect password. Please try again.";
        }
    } else {
        // User does not exist
        $error = "Email not registered.";
    }

    $stmt->close();
}
$conn->close();
?>