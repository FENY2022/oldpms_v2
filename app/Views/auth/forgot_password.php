<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password | O-LDPMS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="icon" href="<?= base_url('logo/denr_logo.png') ?>" type="image/x-icon">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;800&display=swap');
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 min-h-screen flex items-center justify-center px-4">
    <div class="max-w-md w-full bg-white rounded-2xl shadow-xl p-8">
        <div class="text-center mb-8">
            <img src="<?= base_url('logo/denr_logo.png') ?>" alt="DENR Logo" class="h-16 w-16 mx-auto mb-4">
            <h1 class="text-2xl font-bold text-gray-800">Forgot Password</h1>
            <p class="text-gray-500 mt-2">Enter your email to receive a password reset link.</p>
        </div>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-xl text-red-700 text-sm">
                <i class="fas fa-exclamation-triangle mr-2"></i><?= session()->getFlashdata('error') ?>
            </div>
        <?php endif; ?>

        <?php if ($sent): ?>
            <div class="bg-green-50 border border-green-200 rounded-xl p-6 text-center">
                <i class="fas fa-envelope-open-text text-green-500 text-4xl mb-3"></i>
                <p class="text-green-700 font-semibold">If an account with that email exists, a password reset link has been sent.</p>
                <p class="text-green-600 text-sm mt-2">Please check your inbox and follow the link.</p>
            </div>
        <?php else: ?>
            <form method="POST" action="<?= base_url('/forgot-password') ?>" class="space-y-6">
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Email Address</label>
                    <input type="email" name="email" required class="w-full p-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 outline-none" placeholder="Enter your registered email">
                </div>
                <button type="submit" class="w-full bg-emerald-700 text-white font-bold py-3 rounded-xl hover:bg-emerald-800 transition">
                    <i class="fas fa-paper-plane mr-2"></i>Send Reset Link
                </button>
            </form>
        <?php endif; ?>

        <div class="mt-6 text-center">
            <a href="<?= base_url('/') ?>" class="text-emerald-700 hover:text-emerald-900 font-semibold text-sm">
                <i class="fas fa-arrow-left mr-1"></i>Back to Login
            </a>
        </div>
    </div>
</body>
</html>
