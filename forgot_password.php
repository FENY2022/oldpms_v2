<?php
// forgot_password.php
require_once 'db.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Ensure these paths match your folder structure exactly
require 'sendemail/phpmailer/src/Exception.php';
require 'sendemail/phpmailer/src/PHPMailer.php';
require 'sendemail/phpmailer/src/SMTP.php';

$msg = "";
$status = ""; 

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_forgot'])) {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    
    // 1. Check if email exists in your user_client table
    $stmt = $pdo->prepare("SELECT email FROM user_client WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        try {
            // 2. Generate Token
            $token = bin2hex(random_bytes(32));
            
            // 3. Delete any old tokens for this user
            $pdo->prepare("DELETE FROM password_resets WHERE email = ?")->execute([$email]);
            
            // 4. Insert new token (Expires in 1 hour)
            $insertStmt = $pdo->prepare("INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 1 HOUR))");
            $insertStmt->execute([$email, $token]);
            
            // 5. Send Email
            $reset_link = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/reset_password.php?token=" . $token;
            
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'venzonanthonie@gmail.com'; 
            $mail->Password = 'irsw yeav xgqy rmll'; // Your App Password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;
            
            $mail->setFrom('venzonanthonie@gmail.com', 'DENR System Admin');
            $mail->addAddress($email);
            
            $mail->isHTML(true);
            $mail->Subject = 'O-LDPMS Password Reset Request';
            $mail->Body    = "
                <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden;'>
                    <div style='background-color: #064e3b; padding: 20px; text-align: center;'>
                        <h2 style='color: white; margin: 0;'>O-LDPMS Password Reset</h2>
                    </div>
                    <div style='padding: 30px; color: #4a5568;'>
                        <p>Hello,</p>
                        <p>We received a request to reset the password for your account associated with this email address.</p>
                        <div style='text-align: center; margin: 30px 0;'>
                            <a href='$reset_link' style='background-color: #064e3b; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; font-weight: bold;'>Reset My Password</a>
                        </div>
                        <p style='font-size: 0.85em; color: #718096;'>This link will expire in 1 hour. If you did not request this, please ignore this email.</p>
                    </div>
                </div>";
            
            $mail->send();
            $status = "success";
            $msg = "A reset link has been sent to your email. Please check your inbox.";
        } catch (Exception $e) {
            $status = "error";
            $msg = "Mailer Error: " . $mail->ErrorInfo;
        }
    } else {
        $status = "error";
        $msg = "No account found with that email address.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password | DENR System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="icon" href="logo/denr_logo.png" type="image/x-icon">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;800&display=swap');
        body { font-family: 'Inter', sans-serif; }
        .bg-denr { background-color: #064e3b; }
        .text-denr { color: #064e3b; }
        .hero-pattern {
            background-image: linear-gradient(rgba(6, 78, 59, 0.9), rgba(6, 78, 59, 0.8)), url('https://images.unsplash.com/photo-1589939705384-5185137a7f0f?auto=format&fit=crop&q=80&w=2000');
            background-size: cover;
            background-position: center;
        }

        .loader {
            border: 2px solid #f3f3f3;
            border-top: 2px solid currentColor;
            border-radius: 50%;
            width: 1rem;
            height: 1rem;
            animation: spin 1s linear infinite;
            display: inline-block;
            margin-right: 8px;
            vertical-align: middle;
        }

        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
        
        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 1.5rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }
    </style>
</head>
<body class="hero-pattern min-h-screen flex items-center justify-center p-4">

    <div class="glass-card w-full max-w-md p-8 sm:p-10">
        <!-- Logo/Icon Section -->
        <div class="text-center mb-8">
            <div class="bg-denr w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4 shadow-xl">
                <i class="fas fa-shield-alt text-white text-3xl"></i>
            </div>
            <h2 class="text-3xl font-extrabold text-gray-900">Forgot Password</h2>
            <p class="text-gray-500 mt-2">Enter your registered email to reset</p>
        </div>

        <!-- Alert Message -->
        <?php if ($msg): ?>
            <div class="mb-6 p-4 rounded-xl flex items-center gap-3 <?= $status === 'success' ? 'bg-green-50 text-green-700 border border-green-200' : 'bg-red-50 text-red-700 border border-red-200' ?>">
                <i class="fas <?= $status === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle' ?>"></i>
                <p class="text-sm font-semibold"><?= $msg ?></p>
            </div>
        <?php endif; ?>

        <!-- Form -->
        <form method="POST" id="resetForm" onsubmit="showLoading()">
            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2 ml-1">Email Address</label>
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <i class="fas fa-envelope text-gray-400 group-focus-within:text-emerald-600 transition-colors"></i>
                    </div>
                    <input 
                        type="email" 
                        name="email" 
                        required 
                        class="block w-full pl-11 pr-4 py-3.5 border border-gray-300 rounded-xl leading-5 bg-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all"
                        placeholder="yourname@email.com"
                    >
                </div>
            </div>

            <button 
                type="submit" 
                name="submit_forgot" 
                id="submitBtn"
                class="w-full bg-denr hover:bg-emerald-900 text-white font-bold py-4 rounded-xl transition duration-300 transform hover:translate-y-[-1px] active:translate-y-[1px] shadow-lg flex items-center justify-center"
            >
                <span id="btnLoader" class="loader hidden"></span>
                <span id="btnText">Send Reset Link</span>
            </button>
        </form>

        <!-- Back Link -->
        <div class="mt-8 text-center">
            <a href="index.php" class="text-sm font-bold text-denr hover:text-emerald-700 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>Back to Login
            </a>
        </div>
    </div>

    <script>
        function showLoading() {
            const btn = document.getElementById('submitBtn');
            const loader = document.getElementById('btnLoader');
            const text = document.getElementById('btnText');
            
            btn.classList.add('opacity-80', 'cursor-not-allowed');
            loader.classList.remove('hidden');
            text.innerText = "Sending Email...";
            return true;
        }
    </script>
</body>
</html>
