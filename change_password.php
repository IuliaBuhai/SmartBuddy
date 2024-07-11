<?php
include 'connect.php';

session_start();

if (!isset($_SESSION['email'])) {
    echo "You need to log in first.";
    exit();
}

if (isset($_POST['changePassword'])) {
    $currentPassword = $_POST['currentPassword'];
    $newPassword = $_POST['newPassword'];
    $email = $_SESSION['email'];

    // Validate input
    if (empty($currentPassword) || empty($newPassword)) {
        echo "Please fill in all fields.";
        exit();
    }

    // Check if the current password is correct
    $sql = $conn->prepare("SELECT Parola FROM users WHERE Email = ?");
    $sql->bind_param("s", $email);
    $sql->execute();
    $result = $sql->get_result();
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        // Verify the current password
        if (password_verify($currentPassword, $row['Parola'])) {
            // Hash the new password securely
            $newPasswordHash = password_hash($newPassword, PASSWORD_DEFAULT);

            // Update the password in the database
            $updateQuery = $conn->prepare("UPDATE users SET Parola = ? WHERE Email = ?");
            $updateQuery->bind_param("ss", $newPasswordHash, $email);
            if ($updateQuery->execute()) {
                echo "Password successfully changed.";
                exit();
            } else {
                echo "Error: Failed to update password.";
                exit();
            }
        } else {
            echo "Current password is incorrect.";
            exit();
        }
    } else {
        echo "User not found.";
        exit();
    }
}
?>
