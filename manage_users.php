<?php
session_start();

// PREVENT UNAUTHORIZED ACCESS
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || $_SESSION['user_type'] !== 'denr_user') {
    die("Unauthorized access.");
}

// ---------------------------------------------------------
// DATABASE CONNECTION (Update with your credentials)
// ---------------------------------------------------------
$host = '127.0.0.1';
$dbname = 'v2_oldpms';
$dbuser = 'root';
$dbpass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $dbuser, $dbpass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

$message = '';

// ---------------------------------------------------------
// HANDLE FORM SUBMISSION (UPDATE USER)
// ---------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_user'])) {
    $edit_user_id = $_POST['user_id'];
    $new_role = $_POST['user_role_id'];
    $new_password = $_POST['password'];
    
    $updateParts = ["user_role_id = :role"];
    $params = [':role' => $new_role, ':id' => $edit_user_id];

    // Handle Password Update
    if (!empty($new_password)) {
        $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
        $updateParts[] = "password = :password";
        $updateParts[] = "unhashPassword = :unhash";
        $params[':password'] = $hashed_password;
        $params[':unhash'] = $new_password; 
    }

    // Handle File Upload (Profile Picture / Signature)
    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'uploads/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $file_name = time() . '_' . preg_replace("/[^a-zA-Z0-9.-]/", "_", basename($_FILES['profile_pic']['name']));
        $target_path = $upload_dir . $file_name;
        
        if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], $target_path)) {
            $updateParts[] = "uploadSignature = :pic";
            $params[':pic'] = $target_path;
        } else {
            $message = "<div class='mb-6 p-4 text-red-700 bg-red-100 border border-red-200 rounded-xl'>Failed to upload image. Check directory permissions.</div>";
        }
    }

    // Execute Update
    if (empty($message)) {
        $update_query = "UPDATE denr_users SET " . implode(", ", $updateParts) . " WHERE user_id = :id";
        try {
            $stmt = $pdo->prepare($update_query);
            if ($stmt->execute($params)) {
                $message = "<div class='mb-6 p-4 text-emerald-700 bg-emerald-100 border border-emerald-200 rounded-xl font-semibold'><i class='fas fa-check-circle mr-2'></i>User successfully updated!</div>";
            }
        } catch (PDOException $e) {
            $message = "<div class='mb-6 p-4 text-red-700 bg-red-100 border border-red-200 rounded-xl'><i class='fas fa-exclamation-triangle mr-2'></i>Error updating user: " . $e->getMessage() . "</div>";
        }
    }
}

// ---------------------------------------------------------
// FETCH DATA
// ---------------------------------------------------------
// Fetch Roles to map them for the table display
$roleStmt = $pdo->query("SELECT role_id, office_level, role_name FROM denr_roles ORDER BY office_level ASC, role_id ASC");
$roles = $roleStmt->fetchAll(PDO::FETCH_ASSOC);

$roleMap = [];
foreach ($roles as $r) {
    $roleMap[$r['role_id']] = "[{$r['office_level']}] {$r['role_name']}";
}
$roleMap['Admin'] = "[ADMIN] System Admin (Legacy)";

// Fetch Users
$stmt = $pdo->query("SELECT user_id, name, username, user_role_id, uploadSignature FROM denr_users ORDER BY name ASC");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Pass users array to JavaScript for the modal logic
$usersJson = json_encode($users);
?>

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
        
        /* Modal transitions */
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

        <?= $message ?>

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
                                        <img src="<?= htmlspecialchars($u['uploadSignature']) ?>" alt="Pic" class="h-10 w-10 rounded-full object-cover border border-gray-200">
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
                <form action="manage_users.php" method="POST" enctype="multipart/form-data" class="space-y-6">
                    
                    <input type="hidden" name="user_id" id="modalUserId">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
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

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">New Password</label>
                            <input type="text" name="password" placeholder="Enter new password" class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-white text-gray-700 shadow-sm">
                            <p class="text-xs text-gray-400 mt-1">Leave blank to keep current password.</p>
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
                        <button type="submit" name="update_user" class="bg-emerald-700 hover:bg-emerald-800 text-white font-bold py-3 px-8 rounded-xl transition shadow-md flex items-center gap-2">
                            <i class="fas fa-save"></i> Save Changes
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>

    <script>
        const usersData = <?= $usersJson ?>;
        const modal = document.getElementById('editModal');

        // Filter Table Logic
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

        // Open Modal Logic
        function openModal(userId) {
            const user = usersData.find(u => u.user_id == userId);
            
            if (user) {
                // Populate data
                document.getElementById('modalUserId').value = user.user_id;
                document.getElementById('modalUserName').innerText = user.name + ' (@' + user.username + ')';
                document.getElementById('roleSelect').value = user.user_role_id;
                
                // Handle Profile Picture Preview
                const currentImage = document.getElementById('currentImage');
                const imagePlaceholder = document.getElementById('imagePlaceholder');
                
                if (user.uploadSignature && user.uploadSignature.trim() !== '') {
                    currentImage.src = user.uploadSignature;
                    currentImage.classList.remove('hidden');
                    imagePlaceholder.classList.add('hidden');
                } else {
                    currentImage.classList.add('hidden');
                    imagePlaceholder.classList.remove('hidden');
                }

                // Show modal
                modal.classList.remove('hidden');
            }
        }

        // Close Modal Logic
        function closeModal() {
            modal.classList.add('hidden');
        }

        // Close modal if user clicks outside the modal box
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });
    </script>
</body>
</html>