<?php
require_once 'db.php'; // Ensure database connection is included

if (isset($_GET['id']) && isset($_GET['token'])) {
    $client_id = intval($_GET['id']);
    $token = $_GET['token'];

    // Fetch the user from the database
    $stmt = $pdo->prepare("SELECT email, Status FROM user_client WHERE client_id = ?");
    $stmt->execute([$client_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // If already verified
        if ($user['Status'] == 1) {
            echo "<script>
                    alert('Your account is already verified! You can log in.');
                    window.location.href = 'index.php';
                  </script>";
            exit;
        }

        // Re-create the hash to verify it matches
        $secret_key = "denr_caraga_secret"; 
        $expected_token = md5($user['email'] . $client_id . $secret_key);

        if ($token === $expected_token) {
            // Update the status to 1 (Verified)
            $update_stmt = $pdo->prepare("UPDATE user_client SET Status = 1 WHERE client_id = ?");
            if ($update_stmt->execute([$client_id])) {
                echo "<script>
                        alert('Email verified successfully! You can now log into your account.');
                        window.location.href = 'index.php';
                      </script>";
            } else {
                echo "Failed to update account status. Please contact support.";
            }
        } else {
            echo "Invalid verification token. The link may be broken or expired.";
        }
    } else {
        echo "User not found in our system.";
    }
} else {
    echo "Invalid request. Missing ID or Token.";
}
?>