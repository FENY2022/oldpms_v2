<?php
// reset_password.php
require_once 'db.php';

$msg = "";
$status = "";
$token = $_GET['token'] ?? '';

// 1. Initial Token Check
if (empty($token)) {
    $error_title = "Invalid Request";
    $error_msg = "No security token was provided. Please use the link sent to your email.";
    include_error_page($error_title, $error_msg);
    exit;
}

// 2. Validate Token and Expiry
$stmt = $pdo->prepare("SELECT * FROM password_resets WHERE token = ? AND expires_at > NOW()");
$stmt->execute([$token]);
$reset_request = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$reset_request) {
    $error_title = "Link Expired";
    $error_msg = "This password reset link is invalid or has expired. Please request a new one.";
    include_error_page($error_title, $error_msg);
    exit;
}

// 3. Handle Form Submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_reset'])) {
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    if ($new_password !== $confirm_password) {
        $status = "error";
        $msg = "Passwords do not match. Please try again.";
    } elseif (strlen($new_password) < 8) {
        $status = "error";
        $msg = "Password must be at least 8 characters long.";
    } else {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $email = $reset_request['email'];
        
        // Update both columns in user_client
        $updateStmt = $pdo->prepare("UPDATE user_client SET password = ?, password_unhashed = ? WHERE email = ?");
        if ($updateStmt->execute([$hashed_password, $new_password, $email])) {
            // Success: Clean up token
            $pdo->prepare("DELETE FROM password_resets WHERE email = ?")->execute([$email]);
            $status = "success";
            $msg = "Password updated successfully!";
        } else {
            $status = "error";
            $msg = "A database error occurred. Please try again later.";
        }
    }
}

// Helper function to show a nice error page if token is bad
function include_error_page($title, $message) {
    echo "<!DOCTYPE html><html lang='en'><head><meta charset='UTF-8'><script src='https://cdn.tailwindcss.com'></script><link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css'><style>body{font-family:'Inter',sans-serif;}.hero-pattern{background-image:linear-gradient(rgba(6,78,59,0.9),rgba(6,78,59,0.8)),url('https://images.unsplash.com/photo-1589939705384-5185137a7f0f?auto=format&fit=crop&q=80&w=2000');background-size:cover;}</style></head>
    <body class='hero-pattern h-screen flex items-center justify-center p-4'>
        <div class='bg-white p-8 rounded-2xl shadow-2xl max-w-sm w-full text-center'>
            <div class='text-red-500 text-5xl mb-4'><i class='fas fa-exclamation-circle'></i></div>
            <h2 class='text-2xl font-bold mb-2'>$title</h2>
            <p class='text-gray-600 mb-6'>$message</p>
            <a href='forgot_password.php' class='bg-emerald-800 text-white px-6 py-2 rounded-lg font-semibold hover:bg-emerald-900 transition'>Request New Link</a>
        </div>
    </body></html>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Set New Password | DENR System</title>
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
            background-size: cover; background-position: center;
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 1.5rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }
        .loader {
            border: 2px solid #f3f3f3; border-top: 2px solid currentColor; border-radius: 50%;
            width: 1rem; height: 1rem; animation: spin 1s linear infinite; display: inline-block; margin-right: 8px; vertical-align: middle;
        }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
    </style>
</head>
<body class="hero-pattern min-h-screen flex items-center justify-center p-4">

    <div class="glass-card w-full max-w-md p-8 sm:p-10">
        <!-- Success State Header -->
        <?php if ($status === 'success'): ?>
            <div class="text-center py-4">
                <div class="bg-green-100 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-check text-green-600 text-3xl"></i>
                </div>
                <h2 class="text-3xl font-extrabold text-gray-900 mb-2">Success!</h2>
                <p class="text-gray-600 mb-8"><?= $msg ?></p>
                <a href="index.php" class="block w-full bg-denr hover:bg-emerald-900 text-white font-bold py-4 rounded-xl transition duration-300 shadow-lg">
                    Go to Login
                </a>
            </div>
        <?php else: ?>

            <!-- Default Form Header -->
            <div class="text-center mb-8">
                <div class="bg-denr w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4 shadow-xl">
                    <i class="fas fa-lock text-white text-3xl"></i>
                </div>
                <h2 class="text-3xl font-extrabold text-gray-900">New Password</h2>
                <p class="text-gray-500 mt-2">Create a secure password for your account</p>
            </div>

            <!-- Notification Message -->
            <?php if ($msg): ?>
                <div class="mb-6 p-4 rounded-xl flex items-center gap-3 bg-red-50 text-red-700 border border-red-200">
                    <i class="fas fa-exclamation-circle"></i>
                    <p class="text-sm font-semibold"><?= $msg ?></p>
                </div>
            <?php endif; ?>

            <form method="POST" onsubmit="return showLoading()">
                <!-- New Password -->
                <div class="mb-5">
                    <label class="block text-gray-700 text-sm font-bold mb-2 ml-1">New Password</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="fas fa-key text-gray-400 group-focus-within:text-emerald-600"></i>
                        </div>
                        <input 
                            type="password" name="new_password" id="pass1" required 
                            class="block w-full pl-11 pr-12 py-3.5 border border-gray-300 rounded-xl bg-white focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all"
                            placeholder="••••••••"
                        >
                        <button type="button" onclick="togglePass('pass1', 'eye1')" class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-emerald-600">
                            <i class="fas fa-eye" id="eye1"></i>
                        </button>
                    </div>
                </div>

                <!-- Confirm Password -->
                <div class="mb-8">
                    <label class="block text-gray-700 text-sm font-bold mb-2 ml-1">Confirm New Password</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="fas fa-check-double text-gray-400 group-focus-within:text-emerald-600"></i>
                        </div>
                        <input 
                            type="password" name="confirm_password" id="pass2" required 
                            class="block w-full pl-11 pr-12 py-3.5 border border-gray-300 rounded-xl bg-white focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all"
                            placeholder="••••••••"
                        >
                        <button type="button" onclick="togglePass('pass2', 'eye2')" class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-emerald-600">
                            <i class="fas fa-eye" id="eye2"></i>
                        </button>
                    </div>
                </div>

                <button 
                    type="submit" name="submit_reset" id="submitBtn"
                    class="w-full bg-denr hover:bg-emerald-900 text-white font-bold py-4 rounded-xl transition duration-300 shadow-lg flex items-center justify-center"
                >
                    <span id="btnLoader" class="loader hidden"></span>
                    <span id="btnText">Update Password</span>
                </button>
            </form>
        <?php endif; ?>
    </div>

    <script>
        // Toggle Password Visibility
        function togglePass(inputId, eyeId) {
            const input = document.getElementById(inputId);
            const eye = document.getElementById(eyeId);
            if (input.type === "password") {
                input.type = "text";
                eye.classList.remove('fa-eye');
                eye.classList.add('fa-eye-slash');
            } else {
                input.type = "password";
                eye.classList.remove('fa-eye-slash');
                eye.classList.add('fa-eye');
            }
        }

        // Loading State
        function showLoading() {
            const btn = document.getElementById('submitBtn');
            const loader = document.getElementById('btnLoader');
            const text = document.getElementById('btnText');
            
            btn.classList.add('opacity-80', 'cursor-not-allowed');
            loader.classList.remove('hidden');
            text.innerText = "Saving...";
            return true;
        }
    </script>
</body>
</html>
