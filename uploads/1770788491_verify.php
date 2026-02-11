<?php
require_once 'db.php';

$message_title = "";
$message_desc = "";
$status_icon = "";
$icon_color = "";

// Check if token and email exist in the URL
if (isset($_GET['token']) && isset($_GET['email'])) {
    $token = $_GET['token'];
    $email = filter_var($_GET['email'], FILTER_SANITIZE_EMAIL);

    try {
        // Query the database to find the user with matching email and token
        $stmt = $pdo->prepare("SELECT client_id, Status FROM user_client WHERE email = ? AND verification_token = ? LIMIT 1");
        $stmt->execute([$email, $token]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Check the user's current status
            if ($user['Status'] == 0) {
                // Update Status to 1 (Verified)
                $update_stmt = $pdo->prepare("UPDATE user_client SET Status = 1 WHERE email = ? AND client_id = ?");
                
                if ($update_stmt->execute([$email, $user['client_id']])) {
                    $message_title = "Account Verified!";
                    $message_desc = "Your email address has been successfully verified. You can now log in to the O-LDPMS portal.";
                    $status_icon = "fa-check-circle";
                    $icon_color = "text-emerald-500";
                } else {
                    $message_title = "Verification Failed";
                    $message_desc = "An error occurred while updating your account status. Please try again later or contact support.";
                    $status_icon = "fa-exclamation-triangle";
                    $icon_color = "text-red-500";
                }
            } else {
                // Status is already 1 (or higher)
                $message_title = "Already Verified";
                $message_desc = "This account has already been verified. You can proceed to log in.";
                $status_icon = "fa-info-circle";
                $icon_color = "text-blue-500";
            }
        } else {
            // No matching user found for that email + token combination
            $message_title = "Invalid Link";
            $message_desc = "The verification link is invalid, expired, or the account does not exist. Please make sure you copied the entire link from your email.";
            $status_icon = "fa-times-circle";
            $icon_color = "text-red-500";
        }
    } catch (PDOException $e) {
        $message_title = "Database Error";
        $message_desc = "There was a problem communicating with the database. Please try again later.";
        $status_icon = "fa-exclamation-triangle";
        $icon_color = "text-red-500";
    }

} else {
    // Missing URL parameters
    $message_title = "Missing Information";
    $message_desc = "No verification token provided. Please use the exact link sent to your email inbox.";
    $status_icon = "fa-question-circle";
    $icon_color = "text-yellow-500";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Verification | O-LDPMS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;800&display=swap');
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 min-h-screen flex items-center justify-center p-4">

    <div class="max-w-md w-full bg-white rounded-3xl shadow-xl overflow-hidden border border-gray-100 transform transition-all">
        
        <div class="bg-emerald-900 p-6 flex flex-col items-center justify-center text-center">
            <img src="logo/denr_logo.png" alt="DENR Logo" class="h-16 w-16 mb-3 bg-white rounded-full p-1" onerror="this.style.display='none'">
            <h2 class="text-2xl font-black text-white tracking-tight">O-LDPMS</h2>
            <p class="text-[10px] uppercase font-bold text-emerald-300 tracking-widest">Account Verification</p>
        </div>

        <div class="p-8 text-center space-y-6">
            
            <i class="fas <?= $status_icon ?> text-7xl <?= $icon_color ?> drop-shadow-sm"></i>
            
            <div>
                <h3 class="text-2xl font-bold text-gray-800 mb-2"><?= $message_title ?></h3>
                <p class="text-gray-500 text-sm leading-relaxed">
                    <?= $message_desc ?>
                </p>
            </div>

            <div class="pt-4">
                <a href="index.php" class="inline-flex items-center justify-center w-full bg-emerald-700 text-white font-bold py-4 rounded-xl hover:bg-emerald-800 shadow-lg shadow-emerald-900/20 transition-all active:scale-[0.98] gap-2">
                    <i class="fas fa-home"></i> Return to Homepage to Login
                </a>
            </div>

        </div>
    </div>

</body>
</html>