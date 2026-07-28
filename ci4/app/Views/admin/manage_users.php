<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;800&display=swap');
        body { font-family: 'Inter', sans-serif; background-color: transparent; }
        .modal-enter { opacity: 0; transform: scale(0.95); }
        .modal-enter-active { opacity: 1; transform: scale(1); transition: all 0.2s ease-out; }
    </style>
</head>
<body class="p-8">

    <div class="max-w-6xl mx-auto">
        
        <div class="mb-8 flex flex-col md:flex-row md:items-end justify-between gap-4">
            <div>
                <h1 class="text-3xl font-extrabold text-gray-900">User Management ⚙️</h1>
                <p class="text-gray-500 mt-2 text-lg">Manage accounts, update roles, and reset passwords.</p>
            </div>
            
            <div class="relative w-full md:w-80">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400"></i>
                </div>
                <input type="text" id="searchInput" onkeyup="filterTable()" placeholder="Search by name or username..." class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 shadow-sm bg-white text-gray-700">
            </div>
        </div>

        <?= $message ?? '' ?>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse" id="usersTable">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200 text-gray-500 text-xs uppercase tracking-wider">
                            <th class="p-4 font-bold">Profile</th>
                            <th class="p-4 font-bold">Name</th>
                            <th class="p-4 font-bold">Username</th>
                            <th class="p-4 font-bold">Current Role</th>
                            <th class="p-4 font-bold text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm text-gray-700">
                        <?php foreach($users as $u): ?>
                            <tr class="hover:bg-gray-50 transition user-row">
                                <td class="p-4">
                                    <?php if (!empty($u['uploadSignature'])): ?>
                                        <img src="<?= base_url(htmlspecialchars($u['uploadSignature'])) ?>" alt="Pic" class="h-10 w-10 rounded-full object-cover border border-gray-200">
                                    <?php else: ?>
                                        <div class="h-10 w-10 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center font-bold border border-emerald-200">
                                            <?= strtoupper(substr($u['name'], 0, 1)) ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td class="p-4 font-semibold searchable-name"><?= htmlspecialchars($u['name']) ?></td>
                                <td class="p-4 text-gray-500 searchable-username">@<?= htmlspecialchars($u['username']) ?></td>
                                <td class="p-4">
                                    <span class="inline-block px-3 py-1 bg-slate-100 border border-slate-200 rounded-lg text-xs font-semibold text-slate-700">
                                        <?= htmlspecialchars($roleMap[$u['user_role_id']] ?? 'Unknown Role ID: ' . $u['user_role_id']) ?>
                                    </span>
                                </td>
                                <td class="p-4 text-center">
                                    <button onclick="openModal(<?= $u['user_id'] ?>)" class="px-4 py-2 bg-emerald-50 text-emerald-700 hover:bg-emerald-600 hover:text-white rounded-lg font-semibold transition border border-emerald-200 hover:border-emerald-600">
                                        <i class="fas fa-edit mr-1"></i> Edit
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        
                        <tr id="noResultsRow" class="hidden">
                            <td colspan="5" class="p-8 text-center text-gray-500">
                                <i class="fas fa-search-minus text-3xl mb-3 text-gray-300"></i>
                                <p>No users found matching your search.</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div id="editModal" class="fixed inset-0 z-50 flex items-center justify-center hidden bg-gray-900/60 backdrop-blur-sm p-4">
        
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl overflow-hidden flex flex-col max-h-[90vh]">
            
            <div class="px-8 py-5 border-b border-gray-100 bg-gray-50/80 flex justify-between items-center sticky top-0 z-10">
                <div>
                    <h3 class="font-bold text-xl text-gray-800">Edit User Details</h3>
                    <p class="text-sm text-gray-500" id="modalUserName">Selected User</p>
                </div>
                <button onclick="closeModal()" class="text-gray-400 hover:text-red-500 transition text-2xl focus:outline-none">
                    &times;
                </button>
            </div>
            
            <div class="p-8 overflow-y-auto">
                <form action="<?= base_url('/admin/manage-users') ?>" method="POST" enctype="multipart/form-data" class="space-y-6">
                    
                    <input type="hidden" name="user_id" id="modalUserId">

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Assign Role</label>
                        <select name="user_role_id" id="roleSelect" class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-white text-gray-700 shadow-sm" required>
                            <option value="" disabled>-- Select Role --</option>
                            <?php foreach($roles as $r): ?>
                                <option value="<?= htmlspecialchars($r['role_id']) ?>">
                                    [<?= htmlspecialchars($r['office_level']) ?>] - <?= htmlspecialchars($r['role_name']) ?>
                                </option>
                            <?php endforeach; ?>
                            <option value="Admin">System Admin (Legacy String ID)</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 border border-gray-200 rounded-xl p-6 bg-slate-50 relative overflow-hidden">
                        
                        <div class="md:col-span-2">
                            <h4 class="text-sm font-bold text-gray-800 mb-1">Update Password <span class="text-xs font-normal text-gray-500 ml-2">(Optional)</span></h4>
                            <p class="text-xs text-gray-500 leading-relaxed">Leave blank to keep the current password. If updating, must contain at least <strong>8 characters, an uppercase letter, a lowercase letter, a number, and a special character.</strong></p>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">New Password</label>
                            <input type="password" name="password" id="newPassword" placeholder="Enter new password" class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-white text-gray-700 shadow-sm transition-colors">
                            
                            <div class="mt-3 hidden" id="strengthContainer">
                                <div class="flex justify-between items-center mb-1">
                                    <span class="text-xs font-semibold text-gray-600">Strength:</span>
                                    <span id="strengthText" class="text-xs font-bold text-red-500">Low</span>
                                </div>
                                <div class="h-2 w-full bg-gray-200 rounded-full overflow-hidden">
                                    <div id="strengthBar" class="h-full w-1/3 bg-red-500 transition-all duration-300"></div>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Confirm Password</label>
                            <input type="password" name="confirm_password" id="confirmPassword" placeholder="Confirm new password" class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-white text-gray-700 shadow-sm transition-colors" disabled>
                            <p id="matchText" class="text-xs mt-2 hidden font-semibold"></p>
                        </div>
                    </div>

                    <div class="border border-gray-200 rounded-xl p-6 bg-slate-50">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Update Profile Picture / Signature</label>
                        <div class="flex items-center gap-6">
                            <div id="currentImageContainer" class="h-16 w-16 rounded-full bg-gray-200 border border-gray-300 flex items-center justify-center overflow-hidden shrink-0 shadow-sm">
                                <i class="fas fa-image text-gray-400 text-xl" id="imagePlaceholder"></i>
                                <img id="currentImage" src="" alt="Current Profile" class="hidden h-full w-full object-cover">
                            </div>
                            <div class="flex-1">
                                <input type="file" name="profile_pic" accept="image/png, image/jpeg, image/jpg" class="w-full border border-gray-300 rounded-xl px-4 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-white text-gray-700 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 shadow-sm">
                                <p class="text-xs text-gray-500 mt-2">Accepted formats: JPG, PNG. Leave blank to keep existing image.</p>
                            </div>
                        </div>
                    </div>

                    <div class="pt-4 flex justify-end gap-3 sticky bottom-0 bg-white pt-4">
                        <button type="button" onclick="closeModal()" class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 font-bold py-3 px-6 rounded-xl transition shadow-sm">
                            Cancel
                        </button>
                        <button type="submit" name="update_user" id="submitBtn" class="bg-emerald-700 hover:bg-emerald-800 text-white font-bold py-3 px-8 rounded-xl transition shadow-md flex items-center gap-2">
                            <i class="fas fa-save"></i> Save Changes
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>

    <script>
        const usersData = <?= json_encode($users) ?>;
        const modal = document.getElementById('editModal');
        
        const newPassword = document.getElementById('newPassword');
        const confirmPassword = document.getElementById('confirmPassword');
        const strengthContainer = document.getElementById('strengthContainer');
        const strengthText = document.getElementById('strengthText');
        const strengthBar = document.getElementById('strengthBar');
        const matchText = document.getElementById('matchText');
        const submitBtn = document.getElementById('submitBtn');

        let isPasswordValid = true;
        let doPasswordsMatch = true;

        function validateForm() {
            if (newPassword.value.length > 0) {
                if (isPasswordValid && doPasswordsMatch) {
                    submitBtn.disabled = false;
                    submitBtn.classList.remove('opacity-50', 'cursor-not-allowed', 'bg-gray-400');
                    submitBtn.classList.add('bg-emerald-700', 'hover:bg-emerald-800');
                } else {
                    submitBtn.disabled = true;
                    submitBtn.classList.add('opacity-50', 'cursor-not-allowed', 'bg-gray-400');
                    submitBtn.classList.remove('bg-emerald-700', 'hover:bg-emerald-800');
                }
            } else {
                submitBtn.disabled = false;
                submitBtn.classList.remove('opacity-50', 'cursor-not-allowed', 'bg-gray-400');
                submitBtn.classList.add('bg-emerald-700', 'hover:bg-emerald-800');
            }
        }

        newPassword.addEventListener('input', function() {
            const val = newPassword.value;
            
            if (val.length === 0) {
                strengthContainer.classList.add('hidden');
                matchText.classList.add('hidden');
                newPassword.classList.remove('border-red-500', 'border-amber-500', 'border-emerald-500');
                confirmPassword.classList.remove('border-red-500', 'border-emerald-500');
                confirmPassword.value = '';
                confirmPassword.disabled = true;
                isPasswordValid = true;
                doPasswordsMatch = true;
                validateForm();
                return;
            }

            confirmPassword.disabled = false;
            strengthContainer.classList.remove('hidden');
            
            const hasLength = val.length >= 8;
            const hasUpper = /[A-Z]/.test(val);
            const hasLower = /[a-z]/.test(val);
            const hasNumber = /\d/.test(val);
            const hasSpecial = /[!@#$%^&*(),.?":{}|<>]/.test(val);
            
            let strength = 0;
            if (hasLength) strength++;
            if (hasUpper || hasLower) strength++;
            if (hasNumber) strength++;
            if (hasSpecial) strength++;

            newPassword.classList.remove('border-red-500', 'border-amber-500', 'border-emerald-500');

            if (hasLength && hasUpper && hasLower && hasNumber && hasSpecial) {
                strengthText.innerText = 'High';
                strengthText.className = 'text-xs font-bold text-emerald-600';
                strengthBar.className = 'h-full w-full bg-emerald-500 transition-all duration-300';
                newPassword.classList.add('border-emerald-500');
                isPasswordValid = true;
            } else if (strength >= 3) {
                strengthText.innerText = 'Medium';
                strengthText.className = 'text-xs font-bold text-amber-500';
                strengthBar.className = 'h-full w-2/3 bg-amber-500 transition-all duration-300';
                newPassword.classList.add('border-amber-500');
                isPasswordValid = false;
            } else {
                strengthText.innerText = 'Low';
                strengthText.className = 'text-xs font-bold text-red-500';
                strengthBar.className = 'h-full w-1/3 bg-red-500 transition-all duration-300';
                newPassword.classList.add('border-red-500');
                isPasswordValid = false;
            }

            checkMatch();
        });

        function checkMatch() {
            if (newPassword.value.length === 0) return;
            
            matchText.classList.remove('hidden');
            confirmPassword.classList.remove('border-red-500', 'border-emerald-500');

            if (confirmPassword.value.length === 0) {
                matchText.classList.add('hidden');
                doPasswordsMatch = false;
            } else if (newPassword.value === confirmPassword.value) {
                matchText.innerText = 'Passwords match';
                matchText.className = 'text-xs mt-2 text-emerald-600 font-semibold';
                confirmPassword.classList.add('border-emerald-500');
                doPasswordsMatch = true;
            } else {
                matchText.innerText = 'Passwords do not match';
                matchText.className = 'text-xs mt-2 text-red-500 font-semibold';
                confirmPassword.classList.add('border-red-500');
                doPasswordsMatch = false;
            }
            validateForm();
        }
        
        confirmPassword.addEventListener('input', checkMatch);

        function filterTable() {
            let input = document.getElementById("searchInput").value.toLowerCase();
            let rows = document.querySelectorAll(".user-row");
            let hasResults = false;

            rows.forEach(row => {
                let name = row.querySelector(".searchable-name").innerText.toLowerCase();
                let username = row.querySelector(".searchable-username").innerText.toLowerCase();
                
                if (name.includes(input) || username.includes(input)) {
                    row.style.display = "";
                    hasResults = true;
                } else {
                    row.style.display = "none";
                }
            });

            document.getElementById("noResultsRow").style.display = hasResults ? "none" : "";
        }

        function openModal(userId) {
            const user = usersData.find(u => u.user_id == userId);
            
            if (user) {
                document.getElementById('modalUserId').value = user.user_id;
                document.getElementById('modalUserName').innerText = user.name + ' (@' + user.username + ')';
                document.getElementById('roleSelect').value = user.user_role_id;
                
                newPassword.value = '';
                confirmPassword.value = '';
                newPassword.dispatchEvent(new Event('input'));

                const currentImage = document.getElementById('currentImage');
                const imagePlaceholder = document.getElementById('imagePlaceholder');
                
                if (user.uploadSignature && user.uploadSignature.trim() !== '') {
                    currentImage.src = '<?= base_url('/') ?>' + user.uploadSignature;
                    currentImage.classList.remove('hidden');
                    imagePlaceholder.classList.add('hidden');
                } else {
                    currentImage.classList.add('hidden');
                    imagePlaceholder.classList.remove('hidden');
                }

                modal.classList.remove('hidden');
            }
        }

        function closeModal() {
            modal.classList.add('hidden');
        }

        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });
    </script>
</body>
</html>
