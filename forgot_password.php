<?php
// forgot_password.php
require_once 'db.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'sendemail/phpmailer/src/Exception.php';
require 'sendemail/phpmailer/src/PHPMailer.php';
require 'sendemail/phpmailer/src/SMTP.php';

$msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_forgot'])) {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    
    // 1. Check if email exists in your user_client table
    $stmt = $pdo->prepare("SELECT * FROM user_client WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        // 2. Generate Token
        $token = bin2hex(random_bytes(32));
        
        // 3. Delete any old tokens for this user
        $pdo->prepare("DELETE FROM password_resets WHERE email = ?")->execute([$email]);
        
        // 4. Insert new token using MySQL's internal clock (DATE_ADD) to fix timezone bugs
        $insertStmt = $pdo->prepare("INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 1 HOUR))");
        $insertStmt->execute([$email, $token]);
        
        // 5. Send Email
        $reset_link = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/reset_password.php?token=" . $token;
        
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'venzonanthonie@gmail.com'; 
            $mail->Password = 'irsw yeav xgqy rmll'; 
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;
            
            $mail->setFrom('venzonanthonie@gmail.com', 'System Admin');
            $mail->addAddress($email);
            
            $mail->isHTML(true);
            $mail->Subject = 'Password Reset Request';
            $mail->Body    = "Click here to reset your password: <a href='$reset_link'>$reset_link</a><br><br>This link expires in 1 hour.";
            
            $mail->send();
            $msg = "<div class='text-green-600 mb-4 font-semibold text-center'>Reset link sent! Check your email.</div>";
        } catch (Exception $e) {
            $msg = "<div class='text-red-600 mb-4 font-semibold text-center'>Email failed: {$mail->ErrorInfo}</div>";
        }
    } else {
        $msg = "<div class='text-red-600 mb-4 font-semibold text-center'>Email not found.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Forgot Password</title>
    <script src="https://cdn.tailwindcss.com"></script> 
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">
    <div class="bg-white p-8 rounded-lg shadow-md w-96">
        <h2 class="text-2xl font-bold mb-6 text-center">Forgot Password</h2>
        <?= $msg ?>
        <form method="POST">
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Email Address</label>
                <input class="shadow border rounded w-full py-2 px-3 text-gray-700" name="email" type="email" required>
            </div>
            <button class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded w-full" type="submit" name="submit_forgot">Send Reset Link</button>
            <div class="mt-4 text-center"><a href="index.php" class="text-sm text-blue-500">Back to Login</a></div>
        </form>
    </div>
</body>
</html>