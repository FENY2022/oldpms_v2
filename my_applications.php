<?php
// Start Session
session_start();

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: index.php");
    exit;
}

require_once 'db.php';
$client_id = $_SESSION['client_id'];

// --- Handle Re-upload of PDF Files (Multiple Files Support & 3 min Limit) ---
$upload_msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'reupload_file') {
    $req_id = intval($_POST['requirement_id']);
    $app_id = intval($_POST['app_id']);

    // Security Check: Ensure this app belongs to the logged-in client AND calculate time difference in MySQL directly
    $verify_stmt = $pdo->prepare("
        SELECT client_id, TIMESTAMPDIFF(SECOND, date_submitted, NOW()) as seconds_elapsed 
        FROM permit_applications 
        WHERE app_id = ?
    ");
    $verify_stmt->execute([$app_id]);
    $app_data = $verify_stmt->fetch(PDO::FETCH_ASSOC);

    if ($app_data && $app_data['client_id'] == $client_id) {
        
        // Use the exact seconds elapsed calculated by the database
        $time_elapsed = (int)$app_data['seconds_elapsed'];
        
        if ($time_elapsed <= 180) {
            if (isset($_FILES['new_files']) && !empty($_FILES['new_files']['name'][0])) {
                $upload_dir = 'uploads/applications/';
                if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

                $all_uploaded = true;
                $file_count = count($_FILES['new_files']['name']);

                // Validate all files first before deleting old ones
                for ($i = 0; $i < $file_count; $i++) {
                    if ($_FILES['new_files']['error'][$i] == 0) {
                        $file_ext = strtolower(pathinfo($_FILES['new_files']['name'][$i], PATHINFO_EXTENSION));
                        if ($file_ext !== 'pdf') {
                            $all_uploaded = false;
                            $upload_msg = "Invalid file type detected. Only PDFs are allowed.";
                            break;
                        }
                    }
                }

                if ($all_uploaded) {
                    // Delete old files for this specific requirement from DB and Server
                    $old_stmt = $pdo->prepare("SELECT file_id, file_path FROM permit_requirements_files WHERE app_id = ? AND requirement_id = ?");
                    $old_stmt->execute([$app_id, $req_id]);
                    $delete_stmt = $pdo->prepare("DELETE FROM permit_requirements_files WHERE file_id = ?");
                    
                    while ($old = $old_stmt->fetch(PDO::FETCH_ASSOC)) {
                        if (file_exists($old['file_path'])) {
                            unlink($old['file_path']);
                        }
                        $delete_stmt->execute([$old['file_id']]);
                    }

                    // Upload new files and insert to DB
                    $insert_stmt = $pdo->prepare("INSERT INTO permit_requirements_files (app_id, requirement_id, file_path) VALUES (?, ?, ?)");
                    for ($i = 0; $i < $file_count; $i++) {
                        if ($_FILES['new_files']['error'][$i] == 0) {
                            $file_tmp = $_FILES['new_files']['tmp_name'][$i];
                            // Generate safe, unique filename
                            $new_file_name = $app_id . '_' . $req_id . '_' . $i . '_' . time() . '_' . rand(1000, 9999) . '.pdf';
                            $destination = $upload_dir . $new_file_name;

                            if (move_uploaded_file($file_tmp, $destination)) {
                                $insert_stmt->execute([$app_id, $req_id, $destination]);
                            }
                        }
                    }

                    // Insert Audit Trail Log
                    $log_stmt = $pdo->prepare("INSERT INTO application_logs (app_id, action, remarks) VALUES (?, ?, ?)");
                    $log_stmt->execute([$app_id, 'Document Updated', "Requirement document(s) were updated/re-uploaded by the applicant."]);

                    $upload_msg = "Documents successfully updated!";
                }
            } else {
                $upload_msg = "No files selected.";
            }
        } else {
            $upload_msg = "Re-upload time limit (3 minutes) has expired.";
        }
    } else {
        $upload_msg = "Error uploading file or permission denied.";
    }
}

// 1. Fetch Applications for the Current Client and calculate exact seconds passed via DB
$stmt = $pdo->prepare("
    SELECT *, TIMESTAMPDIFF(SECOND, date_submitted, NOW()) as seconds_elapsed 
    FROM permit_applications 
    WHERE client_id = ? 
    ORDER BY app_id DESC
");
$stmt->execute([$client_id]);
$applications = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 2. Fetch Audit Trail Logs & Uploaded Files grouped by App ID
$logs = [];
$final_files = [];

if (!empty($applications)) {
    $app_ids = array_column($applications, 'app_id');
    $placeholders = implode(',', array_fill(0, count($app_ids), '?'));
    
    // Fetch Logs
    $log_stmt = $pdo->prepare("SELECT * FROM application_logs WHERE app_id IN ($placeholders) ORDER BY created_at DESC");
    $log_stmt->execute($app_ids);
    $all_logs = $log_stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($all_logs as $log) {
        $logs[$log['app_id']][] = $log;
    }

    // Fetch Files joined with Requirements details
    $file_stmt = $pdo->prepare("
        SELECT prf.*, r.requirement_name 
        FROM permit_requirements_files prf
        JOIN requirements r ON prf.requirement_id = r.id
        WHERE prf.app_id IN ($placeholders)
        ORDER BY r.sequence ASC, prf.file_id ASC
    ");
    $file_stmt->execute($app_ids);
    $all_files = $file_stmt->fetchAll(PDO::FETCH_ASSOC);

    // Group files by application ID -> requirement ID (to handle multiple files per req)
    $grouped_files = [];
    foreach ($all_files as $file) {
        if (!isset($grouped_files[$file['app_id']][$file['requirement_id']])) {
            $grouped_files[$file['app_id']][$file['requirement_id']] = [
                'requirement_id' => $file['requirement_id'],
                'requirement_name' => $file['requirement_name'],
                'files' => []
            ];
        }
        $grouped_files[$file['app_id']][$file['requirement_id']]['files'][] = [
            'file_id' => $file['file_id'],
            'file_path' => $file['file_path']
        ];
    }
    
    // Re-index to standard arrays for easy JSON parsing
    foreach ($grouped_files as $a_id => $reqs) {
        $final_files[$a_id] = array_values($reqs);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Applications</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;800&display=swap');
        body { font-family: 'Inter', sans-serif; }
        
        /* Modal Animations */
        .modal-enter { opacity: 0; transform: scale(0.95); }
        .modal-enter-active { opacity: 1; transform: scale(1); transition: all 0.2s ease-out; }
    </style>
</head>
<body class="bg-slate-50 min-h-screen p-6 lg:p-10 relative">

    <?php if(!empty($upload_msg)): ?>
    <div id="toastMsg" class="fixed top-5 right-5 z-[100] <?= strpos(strtolower($upload_msg), 'error') !== false || strpos(strtolower($upload_msg), 'expired') !== false ? 'bg-red-600' : 'bg-emerald-600' ?> text-white px-6 py-4 rounded-xl shadow-2xl flex items-center gap-3 transition-opacity duration-500">
        <i class="fas <?= strpos(strtolower($upload_msg), 'error') !== false || strpos(strtolower($upload_msg), 'expired') !== false ? 'fa-exclamation-circle' : 'fa-check-circle' ?> text-xl"></i>
        <span class="font-bold"><?= htmlspecialchars($upload_msg) ?></span>
    </div>
    <script>
        setTimeout(() => {
            const t = document.getElementById('toastMsg');
            if(t) { t.style.opacity = '0'; setTimeout(()=>t.remove(), 500); }
        }, 4000);
    </script>
    <?php endif; ?>

    <div class="mb-8 flex justify-between items-center">
        <div>
            <h2 class="text-3xl font-extrabold text-gray-900">My Applications</h2>
            <p class="text-gray-500 mt-1">Track the status and history of your submitted permits.</p>
        </div>
        
        <button onclick="window.location.reload();" class="bg-white border border-gray-200 text-gray-600 px-4 py-2 rounded-lg text-sm font-bold hover:bg-gray-50 transition shadow-sm">
            <i class="fas fa-sync-alt mr-1"></i> Refresh
        </button>
    </div>

    <?php if (empty($applications)): ?>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-10 text-center">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 text-gray-400 mb-4">
                <i class="fas fa-folder-open text-2xl"></i>
            </div>
            <h4 class="text-gray-900 font-bold mb-1">No applications found</h4>
            <p class="text-gray-500 text-sm max-w-sm mx-auto">You haven't submitted any applications yet.</p>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
            <?php foreach ($applications as $app): 
                // We now strictly use the database's elapsed time logic
                $can_edit = ((int)$app['seconds_elapsed'] <= 180); 
            ?>
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden flex flex-col hover:border-emerald-300 transition-colors">
                    <div class="p-6 flex-1">
                        <div class="flex justify-between items-start mb-4">
                            <span class="px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider bg-emerald-100 text-emerald-800">
                                <?= htmlspecialchars($app['app_type']) ?>
                            </span>
                            <span class="text-xs font-bold text-gray-400">
                                ID: #<?= str_pad($app['app_id'], 5, '0', STR_PAD_LEFT) ?>
                            </span>
                        </div>
                        
                        <h3 class="text-lg font-bold text-gray-900 leading-tight mb-1"><?= htmlspecialchars($app['business_name']) ?></h3>
                        <p class="text-sm text-gray-500 mb-4"><i class="fas fa-user-tag mr-1"></i> <?= htmlspecialchars($app['applicant_type']) ?></p>
                        
                        <?php if ($app['app_type'] == 'Renewal' && !empty($app['reference_number'])): ?>
                            <div class="bg-blue-50 text-blue-800 border border-blue-100 rounded-lg p-3 text-xs mb-4">
                                <span class="font-bold">Ref No:</span> <?= htmlspecialchars($app['reference_number']) ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="bg-slate-50 px-6 py-4 border-t border-gray-100 mt-auto">
                        <button onclick="openDocsModal(<?= $app['app_id'] ?>)" class="w-full bg-blue-50 border border-blue-200 text-blue-700 px-4 py-2.5 rounded-xl text-sm font-bold hover:bg-blue-100 transition flex items-center justify-center gap-2 mb-2">
                            <i class="fas fa-file-pdf"></i> View / Edit Documents
                        </button>

                        <button onclick="openAuditTrail(<?= $app['app_id'] ?>)" class="w-full bg-white border border-gray-300 text-gray-700 px-4 py-2.5 rounded-xl text-sm font-bold hover:bg-emerald-50 hover:text-emerald-700 hover:border-emerald-300 transition flex items-center justify-center gap-2">
                            <i class="fas fa-history"></i> View Audit Trail
                        </button>
                    </div>
                </div>

                <script type="application/json" id="logs_<?= $app['app_id'] ?>">
                    <?= json_encode($logs[$app['app_id']] ?? []) ?>
                </script>
                <script type="application/json" id="files_<?= $app['app_id'] ?>">
                    <?= json_encode($final_files[$app['app_id']] ?? []) ?>
                </script>
                <script type="application/json" id="app_meta_<?= $app['app_id'] ?>">
                    <?= json_encode(['can_edit' => $can_edit]) ?>
                </script>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div id="auditModal" class="fixed inset-0 z-[60] hidden bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden transition-all transform flex flex-col max-h-[90vh] modal-enter" id="auditModalContent">
            <div class="bg-emerald-900 p-6 text-white flex justify-between items-center shrink-0">
                <div>
                    <h3 class="text-xl font-bold">Application Audit Trail</h3>
                    <p class="text-emerald-200 text-sm mt-1" id="auditModalAppId">App ID: #00000</p>
                </div>
                <button onclick="closeModal('auditModal')" class="hover:bg-emerald-800 p-2 h-8 w-8 flex items-center justify-center rounded-full transition"><i class="fas fa-times"></i></button>
            </div>
            <div class="p-8 overflow-y-auto flex-1 bg-slate-50">
                <ul id="auditTimeline" class="relative border-l-2 border-emerald-200 ml-3 space-y-6"></ul>
            </div>
        </div>
    </div>

    <div id="docsModal" class="fixed inset-0 z-[60] hidden bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl overflow-hidden transition-all transform flex flex-col max-h-[90vh] modal-enter" id="docsModalContent">
            <div class="bg-blue-900 p-6 text-white flex justify-between items-center shrink-0">
                <div>
                    <h3 class="text-xl font-bold">Application Documents</h3>
                    <p class="text-blue-200 text-sm mt-1" id="docsModalAppId">App ID: #00000</p>
                </div>
                <button onclick="closeModal('docsModal')" class="hover:bg-blue-800 p-2 h-8 w-8 flex items-center justify-center rounded-full transition"><i class="fas fa-times"></i></button>
            </div>
            <div class="p-6 overflow-y-auto flex-1 bg-slate-50">
                <p class="text-xs text-gray-500 mb-4 bg-yellow-50 text-yellow-800 border border-yellow-200 p-3 rounded-lg">
                    <i class="fas fa-info-circle"></i> You can view your submitted documents below. The re-upload window is strictly available for <strong>3 minutes</strong> after submitting your application.
                </p>
                <ul id="docsList" class="space-y-4"></ul>
            </div>
        </div>
    </div>

    <div id="pdfModal" class="fixed inset-0 z-[70] hidden bg-black/80 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-5xl h-[90vh] overflow-hidden flex flex-col transition-all transform modal-enter" id="pdfModalContent">
            <div class="bg-gray-900 p-4 text-white flex justify-between items-center shrink-0">
                <h3 class="text-lg font-bold flex items-center gap-2">
                    <i class="fas fa-file-pdf text-red-400"></i> <span id="pdfModalTitle">View Document</span>
                </h3>
                <button onclick="closeModal('pdfModal')" class="hover:bg-gray-700 bg-gray-800 p-2 h-8 w-8 flex items-center justify-center rounded-full transition"><i class="fas fa-times"></i></button>
            </div>
            <div class="flex-1 bg-gray-100 relative w-full h-full">
                <iframe id="pdfIframe" src="" class="w-full h-full border-none"></iframe>
            </div>
        </div>
    </div>

    <script>
        // PDF Viewer Logic
        function openPdfModal(filePath, title) {
            document.getElementById('pdfModalTitle').innerText = title;
            document.getElementById('pdfIframe').src = filePath + '#toolbar=0'; 
            
            const modal = document.getElementById('pdfModal');
            const content = document.getElementById('pdfModalContent');
            modal.classList.remove('hidden');
            requestAnimationFrame(() => content.classList.add('modal-enter-active'));
        }

        // File / Documents Logic
        function openDocsModal(appId) {
            document.getElementById('docsModalAppId').innerText = `Tracking App ID: #${String(appId).padStart(5, '0')}`;
            
            const filesData = document.getElementById('files_' + appId).textContent;
            const files = JSON.parse(filesData);
            
            const metaData = document.getElementById('app_meta_' + appId).textContent;
            const meta = JSON.parse(metaData);
            const canEdit = meta.can_edit;

            const list = document.getElementById('docsList');
            list.innerHTML = '';

            if (files.length === 0) {
                list.innerHTML = '<li class="text-center p-6 text-gray-500 font-medium">No documents found for this application.</li>';
            } else {
                files.forEach(reqGroup => {
                    const li = document.createElement('li');
                    li.className = "bg-white p-4 rounded-xl border border-gray-200 shadow-sm flex flex-col gap-3 transition hover:border-blue-300";
                    
                    // Generate view buttons (triggers Modal) for all files under this requirement
                    let fileLinks = reqGroup.files.map((f, index) => {
                        const safeTitle = (reqGroup.requirement_name + ' - File ' + (index + 1)).replace(/'/g, "\\'");
                        return `
                        <button type="button" onclick="openPdfModal('${f.file_path}', '${safeTitle}')" class="text-xs font-bold text-blue-600 hover:text-blue-800 mt-2 mr-2 inline-flex items-center gap-1 bg-blue-50 px-3 py-1.5 rounded-lg border border-blue-100 transition hover:bg-blue-200">
                            <i class="fas fa-eye"></i> View File ${index + 1}
                        </button>
                    `}).join('');

                    // Show reupload form if within 3 minutes, else show lock message
                    let editForm = canEdit ? `
                        <form method="POST" enctype="multipart/form-data" class="mt-2 pt-3 border-t border-gray-100 flex items-center gap-3">
                            <input type="hidden" name="action" value="reupload_file">
                            <input type="hidden" name="requirement_id" value="${reqGroup.requirement_id}">
                            <input type="hidden" name="app_id" value="${appId}">
                            <input type="file" name="new_files[]" accept=".pdf" multiple required class="flex-1 text-xs text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200 cursor-pointer">
                            <button type="submit" class="bg-gray-800 text-white text-xs px-4 py-2 rounded-lg font-bold hover:bg-gray-900 transition flex items-center gap-1 shrink-0">
                                <i class="fas fa-upload"></i> Re-upload
                            </button>
                        </form>
                    ` : `
                        <div class="mt-1 pt-2 border-t border-gray-100 text-xs text-red-500 italic">
                            <i class="fas fa-lock mr-1"></i> The 3-minute re-upload window has expired.
                        </div>
                    `;

                    li.innerHTML = `
                        <div class="flex justify-between items-start">
                            <div class="w-full">
                                <p class="font-bold text-gray-800 text-sm leading-tight">${reqGroup.requirement_name}</p>
                                <div class="flex flex-wrap">
                                    ${fileLinks}
                                </div>
                            </div>
                        </div>
                        ${editForm}
                    `;
                    list.appendChild(li);
                });
            }

            const modal = document.getElementById('docsModal');
            const content = document.getElementById('docsModalContent');
            modal.classList.remove('hidden');
            requestAnimationFrame(() => content.classList.add('modal-enter-active'));
        }

        // Audit Trail Logic
        function openAuditTrail(appId) {
            document.getElementById('auditModalAppId').innerText = `Tracking App ID: #${String(appId).padStart(5, '0')}`;
            const logsData = document.getElementById('logs_' + appId).textContent;
            const logs = JSON.parse(logsData);
            const timeline = document.getElementById('auditTimeline');
            timeline.innerHTML = '';

            if (logs.length === 0) {
                timeline.innerHTML = '<li class="text-gray-500 text-sm ml-4">No tracking history found.</li>';
            } else {
                logs.forEach((log, index) => {
                    const isLatest = index === 0; 
                    const dotClass = isLatest ? 'bg-emerald-500 ring-4 ring-emerald-100' : 'bg-gray-300 ring-4 ring-white';
                    const titleClass = isLatest ? 'text-emerald-700 font-extrabold' : 'text-gray-700 font-bold';
                    const li = document.createElement('li');
                    li.className = "pl-8 relative";
                    const dateObj = new Date(log.created_at);
                    const formattedDate = dateObj.toLocaleString('en-US', { month: 'short', day: 'numeric', year: 'numeric', hour: 'numeric', minute: '2-digit', hour12: true });

                    const safeAction = log.action ? log.action.replace(/</g, "&lt;").replace(/>/g, "&gt;") : '';
                    const safeRemarks = log.remarks ? log.remarks.replace(/</g, "&lt;").replace(/>/g, "&gt;") : '';

                    li.innerHTML = `
                        <div class="absolute w-3 h-3 rounded-full -left-[7px] top-1.5 ${dotClass}"></div>
                        <div class="flex flex-col pb-2">
                            <p class="text-sm ${titleClass} mb-1">${safeAction}</p>
                            ${safeRemarks ? `<div class="bg-white border border-gray-200 rounded-md p-3 mb-2 shadow-sm"><p class="text-xs text-gray-600 leading-relaxed italic"><i class="fas fa-quote-left text-gray-300 mr-1"></i> ${safeRemarks}</p></div>` : ''}
                            <span class="text-[10px] uppercase font-bold text-gray-400 font-mono tracking-wider"><i class="far fa-clock mr-1"></i> ${formattedDate}</span>
                        </div>
                    `;
                    timeline.appendChild(li);
                });
            }

            const modal = document.getElementById('auditModal');
            const content = document.getElementById('auditModalContent');
            modal.classList.remove('hidden');
            requestAnimationFrame(() => content.classList.add('modal-enter-active'));
        }

        function closeModal(id) {
            const modal = document.getElementById(id);
            const content = document.getElementById(id + 'Content');
            content.classList.remove('modal-enter-active');
            
            setTimeout(() => {
                modal.classList.add('hidden');
                
                if (id === 'pdfModal') {
                    document.getElementById('pdfIframe').src = '';
                }
            }, 200);
        }
    </script>
</body>
</html>