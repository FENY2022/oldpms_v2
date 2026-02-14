<?php
// reset_password.php
require_once 'db.php';

$msg = "";
$token = $_GET['token'] ?? '';

if (empty($token)) {
    die("<div style='text-align:center; padding:50px; font-family:sans-serif;'>Invalid request. No token provided.</div>");
}

// Check if token exists and has not expired
$stmt = $pdo->prepare("SELECT * FROM password_resets WHERE token = ? AND expires_at > NOW()");
$stmt->execute([$token]);
$reset_request = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$reset_request) {
    die("<div style='text-align:center; padding:50px; font-family:sans-serif; color:red;'>This password reset link is invalid or has expired. Please request a new one.</div>");
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_reset'])) {
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    if ($new_password !== $confirm_password) {
        $msg = "<div class='text-red-600 mb-4 font-semibold text-center'>Passwords do not match.</div>";
    } elseif (strlen($new_password) < 8) {
        $msg = "<div class='text-red-600 mb-4 font-semibold text-center'>Password must be at least 8 characters long.</div>";
    } else {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $email = $reset_request['email'];
        
        // Update both the hashed and unhashed password columns in user_client
        $updateStmt = $pdo->prepare("UPDATE user_client SET password = ?, password_unhashed = ? WHERE email = ?");
        if ($updateStmt->execute([$hashed_password, $new_password, $email])) {
            // Delete the token so it can't be reused
            $pdo->prepare("DELETE FROM password_resets WHERE email = ?")->execute([$email]);
            $msg = "<div class='text-green-600 mb-4 font-semibold text-center'>Password reset successfully! <br><br><a href='index.php' class='text-blue-600 underline font-bold'>Click here to login</a></div>";
        } else {
            $msg = "<div class='text-red-600 mb-4 font-semibold text-center'>Error updating password. Please try again.</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">
    <div class="bg-white p-8 rounded-lg shadow-md w-96">
        <h2 class="text-2xl font-bold mb-6 text-center">Reset Password</h2>
        <?= $msg ?>
        
        <?php if (strpos($msg, 'successfully') === false): ?>
        <form method="POST" action="">
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="new_password">New Password</label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="new_password" name="new_password" type="password" required>
            </div>
            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="confirm_password">Confirm New Password</label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="confirm_password" name="confirm_password" type="password" required>
            </div>
            <div class="flex items-center justify-between">
                <button class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline w-full" type="submit" name="submit_reset">Update Password</button>
            </div>
        </form>
        <?php endif; ?>
    </div>
</body>
</html>