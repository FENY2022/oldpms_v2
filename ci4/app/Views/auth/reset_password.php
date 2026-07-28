<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password | O-LDPMS</title>
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
            <h1 class="text-2xl font-bold text-gray-800">Reset Password</h1>
        </div>
        <?php if (!empty($success)): ?>
            <div class="bg-green-50 border border-green-200 rounded-xl p-6 text-center mb-6">
                <i class="fas fa-check-circle text-green-500 text-4xl mb-3"></i>
                <p class="text-green-700 font-semibold"><?= $success ?></p>
            </div>
            <a href="<?= base_url('/') ?>" class="block text-center bg-emerald-700 text-white px-6 py-3 rounded-xl font-bold hover:bg-emerald-800 transition">Go to Login</a>
        <?php elseif (!empty($error) && empty($token)): ?>
            <div class="bg-red-50 border border-red-200 rounded-xl p-6 text-center">
                <i class="fas fa-exclamation-circle text-red-500 text-4xl mb-3"></i>
                <p class="text-red-700 font-semibold"><?= $error ?></p>
            </div>
            <a href="<?= base_url('/forgot-password') ?>" class="block text-center mt-4 text-emerald-700 hover:text-emerald-900 font-semibold">
                <i class="fas fa-arrow-left mr-1"></i>Try Again
            </a>
        <?php else: ?>
            <?php if (!empty($error)): ?>
                <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-xl text-red-700 text-sm">
                    <i class="fas fa-exclamation-triangle mr-2"></i><?= $error ?>
                </div>
            <?php endif; ?>
            <form method="POST" action="<?= base_url('/reset-password') ?>" class="space-y-6">
                <?= csrf_field() ?>
                <input type="hidden" name="token" value="<?= esc($token) ?>">
                <input type="hidden" name="email" value="<?= esc($email) ?>">
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">New Password</label>
                    <input type="password" name="password" required minlength="8" class="w-full p-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Confirm Password</label>
                    <input type="password" name="confirm_password" required minlength="8" class="w-full p-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 outline-none">
                </div>
                <button type="submit" class="w-full bg-emerald-700 text-white font-bold py-3 rounded-xl hover:bg-emerald-800 transition">
                    <i class="fas fa-key mr-2"></i>Reset Password
                </button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
