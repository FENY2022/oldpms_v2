<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Email | O-LDPMS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="icon" href="<?= base_url('logo/denr_logo.png') ?>" type="image/x-icon">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;800&display=swap');
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 min-h-screen flex items-center justify-center px-4">
    <div class="max-w-md w-full bg-white rounded-2xl shadow-xl p-8 text-center">
        <img src="<?= base_url('logo/denr_logo.png') ?>" alt="DENR Logo" class="h-16 w-16 mx-auto mb-6">
        <h1 class="text-2xl font-bold text-gray-800 mb-4">Email Verification</h1>
        <?php if ($status === 'success'): ?>
            <div class="bg-green-50 border border-green-200 rounded-xl p-6 mb-6">
                <i class="fas fa-check-circle text-green-500 text-4xl mb-3"></i>
                <p class="text-green-700 font-semibold"><?= $message ?></p>
            </div>
        <?php else: ?>
            <div class="bg-red-50 border border-red-200 rounded-xl p-6 mb-6">
                <i class="fas fa-exclamation-circle text-red-500 text-4xl mb-3"></i>
                <p class="text-red-700 font-semibold"><?= $message ?></p>
            </div>
        <?php endif; ?>
        <a href="<?= base_url('/') ?>" class="inline-block bg-emerald-700 text-white px-6 py-3 rounded-xl font-bold hover:bg-emerald-800 transition">Go to Login</a>
    </div>
</body>
</html>
