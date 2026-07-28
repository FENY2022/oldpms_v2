<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Settings</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="icon" href="<?= base_url('logo/denr_logo.png') ?>" type="image/x-icon">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;800&display=swap');
        body { font-family: 'Inter', sans-serif; background-color: transparent; }
    </style>
</head>
<body class="p-6 lg:p-10">

    <div class="max-w-4xl mx-auto">
        <div class="mb-8">
            <h2 class="text-3xl font-extrabold text-gray-900">Profile Settings</h2>
            <p class="text-gray-500 mt-1">Manage your account details and password.</p>
        </div>

        <?php if (session()->getFlashdata('success')): ?>
            <div class="mb-6 bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded-r-lg">
                <div class="flex">
                    <div class="flex-shrink-0"><i class="fas fa-check-circle text-emerald-500"></i></div>
                    <div class="ml-3"><p class="text-sm text-emerald-700 font-medium"><?= esc(session()->getFlashdata('success')) ?></p></div>
                </div>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg">
                <div class="flex">
                    <div class="flex-shrink-0"><i class="fas fa-exclamation-circle text-red-500"></i></div>
                    <div class="ml-3"><p class="text-sm text-red-700 font-medium"><?= esc(session()->getFlashdata('error')) ?></p></div>
                </div>
            </div>
        <?php endif; ?>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-8">
            <div class="px-6 py-5 border-b border-gray-100 bg-slate-50 flex items-center gap-3">
                <i class="fas fa-user text-emerald-600"></i>
                <h3 class="font-bold text-gray-800 text-lg">Personal Information</h3>
            </div>
            
            <form action="<?= base_url('/client/profile') ?>" method="POST" enctype="multipart/form-data" class="p-6">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="update_profile">
                
                <div class="mb-6 flex items-center gap-6">
                    <div class="w-24 h-24 rounded-full overflow-hidden bg-gray-100 border-2 border-gray-200 shadow-sm flex-shrink-0 flex items-center justify-center">
                        <?php if (!empty($user['profile_picture'])): ?>
                            <img src="<?= base_url('/') . esc($user['profile_picture']) ?>" alt="Profile Picture" class="w-full h-full object-cover">
                        <?php else: ?>
                            <i class="fas fa-user text-4xl text-gray-400"></i>
                        <?php endif; ?>
                    </div>
                    <div class="flex-grow">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Profile Picture</label>
                        <input type="file" name="profile_picture" accept=".jpg, .jpeg, .png, .gif" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 transition">
                        <p class="text-xs text-gray-400 mt-2">Recommended size: 2MB max. Formats: JPG, PNG, GIF.</p>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">First Name</label>
                        <input type="text" name="firstname" value="<?= esc($user['firstname'] ?? '') ?>" required class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition shadow-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Last Name</label>
                        <input type="text" name="lastname" value="<?= esc($user['lastname'] ?? '') ?>" required class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition shadow-sm">
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Email Address</label>
                    <input type="email" name="email" value="<?= esc($user['email'] ?? '') ?>" required class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition shadow-sm">
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="bg-emerald-700 text-white px-6 py-2.5 rounded-lg font-bold hover:bg-emerald-800 transition shadow-sm">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100 bg-slate-50 flex items-center gap-3">
                <i class="fas fa-lock text-emerald-600"></i>
                <h3 class="font-bold text-gray-800 text-lg">Change Password</h3>
            </div>
            
            <form action="<?= base_url('/client/profile') ?>" method="POST" class="p-6">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="change_password">
                
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Current Password</label>
                    <input type="password" name="current_password" required class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition shadow-sm">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">New Password</label>
                        <input type="password" name="new_password" id="new_password" required class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition shadow-sm">
                        
                        <div class="mt-2">
                            <div class="flex justify-between items-center mb-1">
                                <span class="text-xs text-gray-500">Password strength:</span>
                                <span id="strength-text" class="text-xs font-bold text-gray-400">None</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-1.5">
                                <div id="strength-bar" class="bg-gray-400 h-1.5 rounded-full w-0 transition-all duration-300"></div>
                            </div>
                            <p class="text-xs text-gray-500 mt-2 leading-relaxed">Must contain at least 8 characters, an uppercase letter, a lowercase letter, a number, and a special character.</p>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Confirm New Password</label>
                        <input type="password" name="confirm_password" required class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition shadow-sm">
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="bg-gray-800 text-white px-6 py-2.5 rounded-lg font-bold hover:bg-gray-900 transition shadow-sm">
                        Update Password
                    </button>
                </div>
            </form>
        </div>

    </div>

    <script>
        // Password Strength Checker
        document.getElementById('new_password').addEventListener('input', function() {
            const password = this.value;
            let strength = 0;
            const strengthBar = document.getElementById('strength-bar');
            const strengthText = document.getElementById('strength-text');

            // Check criteria
            if (password.length >= 8) strength += 1;
            if (password.match(/[A-Z]/)) strength += 1;
            if (password.match(/[a-z]/)) strength += 1;
            if (password.match(/\d/)) strength += 1;
            if (password.match(/[\W_]/)) strength += 1;

            // Update UI based on strength
            if (password.length === 0) {
                strengthBar.style.width = '0%';
                strengthBar.className = 'bg-gray-400 h-1.5 rounded-full transition-all duration-300';
                strengthText.textContent = 'None';
                strengthText.className = 'text-xs font-bold text-gray-400';
            } else if (strength <= 2) {
                strengthBar.style.width = '33%';
                strengthBar.className = 'bg-red-500 h-1.5 rounded-full transition-all duration-300';
                strengthText.textContent = 'Weak';
                strengthText.className = 'text-xs font-bold text-red-500';
            } else if (strength >= 3 && strength <= 4) {
                strengthBar.style.width = '66%';
                strengthBar.className = 'bg-yellow-500 h-1.5 rounded-full transition-all duration-300';
                strengthText.textContent = 'Moderate';
                strengthText.className = 'text-xs font-bold text-yellow-500';
            } else if (strength === 5) {
                strengthBar.style.width = '100%';
                strengthBar.className = 'bg-emerald-500 h-1.5 rounded-full transition-all duration-300';
                strengthText.textContent = 'Strong';
                strengthText.className = 'text-xs font-bold text-emerald-500';
            }
        });
    </script>
</body>
</html>
