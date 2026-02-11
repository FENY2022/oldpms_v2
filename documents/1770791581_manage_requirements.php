<?php
// manage_requirements.php
require_once 'db.php';

// --- BACKEND HANDLERS ---

// 0. Handle AJAX Reordering (Must be at the top before HTML output)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'update_order') {
    if (isset($_POST['order']) && is_array($_POST['order'])) {
        foreach ($_POST['order'] as $index => $id) {
            $seq = $index + 1; // Sequence starts at 1
            $stmt = $pdo->prepare("UPDATE requirements SET sequence = ? WHERE id = ?");
            $stmt->execute([$seq, $id]);
        }
    }
    exit; // Stop execution here so we don't return the whole HTML page to the AJAX call
}

$message = '';
$messageType = '';

// Helper to handle file uploads
function handleFileUpload($file, $targetDir = "documents/") {
    if (isset($file) && $file['error'] == 0) {
        if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);
        $fileName = time() . '_' . basename($file['name']);
        $targetFile = $targetDir . $fileName;
        if (move_uploaded_file($file['tmp_name'], $targetFile)) {
            return $targetFile;
        }
    }
    return null;
}

// 1. Handle ADD Requirement
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'add') {
    $name = $_POST['requirement_name'];
    $new_status = $_POST['new_app_status'];
    $renew_status = $_POST['renewal_app_status'];
    
    // File upload
    $download_link = handleFileUpload($_FILES['file_upload']);

    // Determine next sequence number
    $seqStmt = $pdo->query("SELECT MAX(sequence) FROM requirements");
    $next_seq = (int)$seqStmt->fetchColumn() + 1;

    $stmt = $pdo->prepare("INSERT INTO requirements (requirement_name, new_app_status, renewal_app_status, download_link, sequence) VALUES (?, ?, ?, ?, ?)");
    if ($stmt->execute([$name, $new_status, $renew_status, $download_link, $next_seq])) {
        $message = "Requirement added successfully!";
        $messageType = "success";
    } else {
        $message = "Error adding requirement.";
        $messageType = "error";
    }
}

// 2. Handle EDIT Requirement
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'edit') {
    $id = $_POST['id'];
    $name = $_POST['requirement_name'];
    $new_status = $_POST['new_app_status'];
    $renew_status = $_POST['renewal_app_status'];
    
    // Check if new file uploaded, else keep old one
    $new_file_path = handleFileUpload($_FILES['file_upload']);
    
    if ($new_file_path) {
        $stmt = $pdo->prepare("UPDATE requirements SET requirement_name=?, new_app_status=?, renewal_app_status=?, download_link=? WHERE id=?");
        $res = $stmt->execute([$name, $new_status, $renew_status, $new_file_path, $id]);
    } else {
        $stmt = $pdo->prepare("UPDATE requirements SET requirement_name=?, new_app_status=?, renewal_app_status=? WHERE id=?");
        $res = $stmt->execute([$name, $new_status, $renew_status, $id]);
    }

    if ($res) {
        $message = "Requirement updated successfully!";
        $messageType = "success";
    } else {
        $message = "Error updating requirement.";
        $messageType = "error";
    }
}

// 3. Handle DELETE Requirement
if (isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];
    
    // Optional: Delete physical file
    $stmt = $pdo->prepare("SELECT download_link FROM requirements WHERE id = ?");
    $stmt->execute([$id]);
    $file = $stmt->fetchColumn();
    if ($file && file_exists($file)) {
        unlink($file);
    }

    $delStmt = $pdo->prepare("DELETE FROM requirements WHERE id = ?");
    if ($delStmt->execute([$id])) {
        $message = "Requirement deleted successfully!";
        $messageType = "success";
    } else {
        $message = "Error deleting requirement.";
        $messageType = "error";
    }
}

// Fetch All Requirements (Ordered by sequence, then ID as fallback)
$req_stmt = $pdo->query("SELECT * FROM requirements ORDER BY sequence ASC, id ASC");
$requirements = $req_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Manage Requirements</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="icon" href="logo/denr_logo.png" type="image/x-icon">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;800&display=swap');
        body { font-family: 'Inter', sans-serif; }
        
        /* Smooth row transition for reordering */
        tbody tr { transition: background-color 0.2s ease; }
        tbody tr.moving { background-color: #ecfdf5; opacity: 0.9; }
    </style>
</head>
<body class="bg-gray-50 text-gray-800">

    <nav class="bg-emerald-900 text-white shadow-lg sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex items-center gap-3">
                    <img src="logo/denr_logo.png" alt="DENR Logo" class="h-10 w-10 bg-white rounded-full p-1">
                    <span class="text-xl font-bold tracking-tight">O-LDPMS <span class="text-emerald-300 font-normal">| Manage Requirements</span></span>
                </div>
                <a href="index.php" class="text-sm font-semibold hover:text-emerald-300 transition"><i class="fas fa-arrow-left mr-2"></i> Back to Main Site</a>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        
        <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
            <div>
                <h1 class="text-3xl font-extrabold text-gray-900">Manage Requirements</h1>
                <p class="text-gray-500 mt-1">Add, edit, or reorder application requirements.</p>
            </div>
            <button onclick="openModal('addModal')" class="bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-3 rounded-xl font-bold shadow-lg transition flex items-center gap-2 transform active:scale-95">
                <i class="fas fa-plus-circle"></i> Add Requirement
            </button>
        </div>

        <?php if ($message): ?>
        <div class="mb-6 p-4 rounded-xl border <?= $messageType === 'success' ? 'bg-green-50 border-green-200 text-green-800' : 'bg-red-50 border-red-200 text-red-800' ?> flex items-center justify-between">
            <span><?= htmlspecialchars($message) ?></span>
            <button onclick="this.parentElement.remove()" class="text-sm font-bold hover:underline">Dismiss</button>
        </div>
        <?php endif; ?>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse" id="requirementsTable">
                    <thead>
                        <tr class="bg-gray-50 text-gray-600 uppercase text-xs tracking-wider border-b border-gray-200">
                            <th class="px-6 py-4 font-bold w-16">Seq</th>
                            <th class="px-6 py-4 font-bold text-center w-24">Order</th>
                            <th class="px-6 py-4 font-bold">Requirement Name</th>
                            <th class="px-6 py-4 font-bold">Attachment</th>
                            <th class="px-6 py-4 font-bold">New App Status</th>
                            <th class="px-6 py-4 font-bold">Renewal Status</th>
                            <th class="px-6 py-4 font-bold text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php 
                        $counter = 1; // Used for static visual numbering
                        foreach ($requirements as $req): 
                        ?>
                        <tr class="hover:bg-gray-50 transition duration-150" data-id="<?= $req['id'] ?>">
                            <td class="px-6 py-4 font-bold text-gray-400 text-sm seq-num"><?= $counter++ ?></td>
                            
                            <td class="px-6 py-4 text-center">
                                <div class="flex flex-col items-center justify-center space-y-1">
                                    <button onclick="moveRow(this, -1)" class="text-gray-400 hover:text-emerald-600 transition p-1" title="Move Up">
                                        <i class="fas fa-chevron-up"></i>
                                    </button>
                                    <button onclick="moveRow(this, 1)" class="text-gray-400 hover:text-emerald-600 transition p-1" title="Move Down">
                                        <i class="fas fa-chevron-down"></i>
                                    </button>
                                </div>
                            </td>

                            <td class="px-6 py-4 font-semibold text-gray-900"><?= htmlspecialchars($req['requirement_name']) ?></td>
                            <td class="px-6 py-4">
                                <?php if (!empty($req['download_link'])): ?>
                                    <a href="<?= htmlspecialchars($req['download_link']) ?>" target="_blank" class="text-emerald-600 hover:text-emerald-800 text-sm font-medium flex items-center gap-1">
                                        <i class="fas fa-paperclip"></i> View File
                                    </a>
                                <?php else: ?>
                                    <span class="text-gray-400 text-xs italic">None</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <span class="px-2 py-1 rounded text-xs font-bold <?= $req['new_app_status'] == 'Required' ? 'bg-indigo-50 text-indigo-700' : 'bg-gray-100 text-gray-600' ?>">
                                    <?= htmlspecialchars($req['new_app_status']) ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <span class="px-2 py-1 rounded text-xs font-bold <?= $req['renewal_app_status'] == 'Required' ? 'bg-indigo-50 text-indigo-700' : 'bg-gray-100 text-gray-600' ?>">
                                    <?= htmlspecialchars($req['renewal_app_status']) ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <button onclick="openEditModal(<?= htmlspecialchars(json_encode($req)) ?>)" class="bg-blue-50 text-blue-600 hover:bg-blue-100 p-2 rounded-lg transition" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <a href="?delete_id=<?= $req['id'] ?>" onclick="return confirm('Are you sure you want to delete this requirement?');" class="bg-red-50 text-red-600 hover:bg-red-100 p-2 rounded-lg transition" title="Delete">
                                        <i class="fas fa-trash-alt"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if(empty($requirements)): ?>
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-gray-400">No requirements found.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div id="addModal" class="fixed inset-0 z-[60] hidden bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg transform transition-all">
            <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                <h3 class="text-xl font-bold text-gray-900">Add New Requirement</h3>
                <button onclick="closeModal('addModal')" class="text-gray-400 hover:text-red-500 transition"><i class="fas fa-times text-xl"></i></button>
            </div>
            <form action="" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
                <input type="hidden" name="action" value="add">
                
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Requirement Name</label>
                    <input type="text" name="requirement_name" required class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 outline-none" placeholder="e.g. 1. Application Form">
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">New App Status</label>
                        <input type="text" name="new_app_status" required class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 outline-none" placeholder="e.g. Required">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Renewal Status</label>
                        <input type="text" name="renewal_app_status" required class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 outline-none" placeholder="e.g. Updated">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Upload File (Optional)</label>
                    <input type="file" name="file_upload" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100">
                    <p class="text-xs text-gray-400 mt-1">Upload DOCX or PDF forms if needed.</p>
                </div>

                <div class="pt-4 flex justify-end gap-3">
                    <button type="button" onclick="closeModal('addModal')" class="px-5 py-2.5 rounded-lg text-gray-600 font-bold hover:bg-gray-100 transition">Cancel</button>
                    <button type="submit" class="px-5 py-2.5 rounded-lg bg-emerald-600 text-white font-bold hover:bg-emerald-700 shadow-md transition">Save Requirement</button>
                </div>
            </form>
        </div>
    </div>

    <div id="editModal" class="fixed inset-0 z-[60] hidden bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
         <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg transform transition-all">
            <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                <h3 class="text-xl font-bold text-gray-900">Edit Requirement</h3>
                <button onclick="closeModal('editModal')" class="text-gray-400 hover:text-red-500 transition"><i class="fas fa-times text-xl"></i></button>
            </div>
            <form action="" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="id" id="edit_id">
                
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Requirement Name</label>
                    <input type="text" name="requirement_name" id="edit_name" required class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 outline-none">
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">New App Status</label>
                        <input type="text" name="new_app_status" id="edit_new" required class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Renewal Status</label>
                        <input type="text" name="renewal_app_status" id="edit_renew" required class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 outline-none">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Replace File (Optional)</label>
                    <input type="file" name="file_upload" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100">
                    <p class="text-xs text-gray-400 mt-1">Leave empty to keep existing file.</p>
                </div>

                <div class="pt-4 flex justify-end gap-3">
                    <button type="button" onclick="closeModal('editModal')" class="px-5 py-2.5 rounded-lg text-gray-600 font-bold hover:bg-gray-100 transition">Cancel</button>
                    <button type="submit" class="px-5 py-2.5 rounded-lg bg-blue-600 text-white font-bold hover:bg-blue-700 shadow-md transition">Update Requirement</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Modal handlers
        function openModal(id) { document.getElementById(id).classList.remove('hidden'); }
        function closeModal(id) { document.getElementById(id).classList.add('hidden'); }
        
        function openEditModal(data) {
            document.getElementById('edit_id').value = data.id;
            document.getElementById('edit_name').value = data.requirement_name;
            document.getElementById('edit_new').value = data.new_app_status;
            document.getElementById('edit_renew').value = data.renewal_app_status;
            openModal('editModal');
        }

        window.onclick = function(event) {
            if (event.target.classList.contains('bg-black/60')) {
                event.target.classList.add('hidden');
            }
        }

        // --- ROW REORDERING LOGIC ---
        function moveRow(btn, direction) {
            const row = btn.closest('tr');
            const tbody = row.parentNode;
            
            // Visual feedback
            row.classList.add('moving');
            setTimeout(() => row.classList.remove('moving'), 500);

            if (direction === -1 && row.previousElementSibling) {
                tbody.insertBefore(row, row.previousElementSibling);
            } else if (direction === 1 && row.nextElementSibling) {
                tbody.insertBefore(row.nextElementSibling, row);
            } else {
                return; // Reached the top or bottom
            }

            updateRowNumbers();
            saveNewOrder();
        }

        function updateRowNumbers() {
            // Keep the visible numbers (1, 2, 3...) sequential regardless of movement
            const rows = document.querySelectorAll('#requirementsTable tbody tr');
            rows.forEach((row, index) => {
                const seqCell = row.querySelector('.seq-num');
                if (seqCell) seqCell.textContent = index + 1;
            });
        }

        function saveNewOrder() {
            // Grab the real Database IDs in their new visual order
            const rows = document.querySelectorAll('#requirementsTable tbody tr');
            const orderIDs = Array.from(rows).map(row => row.getAttribute('data-id'));
            
            // Send to PHP backend silently via AJAX
            const params = new URLSearchParams();
            params.append('action', 'update_order');
            orderIDs.forEach(id => params.append('order[]', id));

            fetch(window.location.href, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: params.toString()
            })
            .then(response => {
                if (!response.ok) console.error("Failed to save new order.");
            });
        }
    </script>
</body>
</html>