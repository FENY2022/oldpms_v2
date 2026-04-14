<?php
session_start();
require 'db.php'; // Include your database connection

// PREVENT UNAUTHORIZED ACCESS
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || $_SESSION['user_type'] !== 'denr_user') {
    die("<div style='text-align:center; padding: 20px; font-family: sans-serif; color: red;'>Unauthorized access. Please login.</div>");
}

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("<div style='text-align:center; padding: 20px; font-family: sans-serif; color: red;'>Invalid Application ID.</div>");
}

$app_id = intval($_GET['id']);
$user_name = $_SESSION['name'] ?? 'System User';

// -------------------------------------------------------------------------
// HANDLE "MARK AS OK" VIA AJAX (No Page Reload)
// -------------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax_mark_ok'])) {
    header('Content-Type: application/json');
    $file_id = intval($_POST['file_id']);
    
    try {
        $stmt_doc = $pdo->prepare("UPDATE permit_requirements_files SET status = 'OK' WHERE file_id = :file_id");
        $stmt_doc->execute([':file_id' => $file_id]);
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    exit; // Stop execution here for AJAX calls
}

// -------------------------------------------------------------------------
// HANDLE STATUS UPDATE FORM SUBMISSION
// -------------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_application'])) {
    $new_status = $_POST['status'];
    $remarks = trim($_POST['remarks']);

    try {
        $pdo->beginTransaction();

        // 1. Update the permit application status
        $stmt_update = $pdo->prepare("UPDATE permit_applications SET status = :status WHERE app_id = :app_id");
        $stmt_update->execute([':status' => $new_status, ':app_id' => $app_id]);

        // 2. Insert into application logs
        $action = "Status updated to: " . $new_status;
        $stmt_log = $pdo->prepare("INSERT INTO application_logs (app_id, action, remarks) VALUES (:app_id, :action, :remarks)");
        $stmt_log->execute([
            ':app_id' => $app_id,
            ':action' => $action,
            ':remarks' => "Evaluated by " . $user_name . " - " . $remarks
        ]);

        $pdo->commit();
        $_SESSION['success_msg'] = "Application status successfully updated!";
        
        header("Location: view_application.php?id=" . $app_id);
        exit;
    } catch (Exception $e) {
        $pdo->rollBack();
        $error_msg = "Failed to update status: " . $e->getMessage();
    }
}

// FETCH APPLICATION & CLIENT DETAILS
$query_app = "SELECT pa.*, uc.firstname, uc.mid_name, uc.lastname, uc.email, uc.mobilenum, 
              m.muncity_name, m.office_cover 
              FROM permit_applications pa
              LEFT JOIN user_client uc ON pa.client_id = uc.client_id
              LEFT JOIN muncity m ON pa.muncity_id = m.mun_code
              WHERE pa.app_id = :app_id";
$stmt_app = $pdo->prepare($query_app);
$stmt_app->execute([':app_id' => $app_id]);
$app = $stmt_app->fetch(PDO::FETCH_ASSOC);

if (!$app) {
    die("<div style='text-align:center; padding: 20px; font-family: sans-serif; color: red;'>Application not found.</div>");
}

// FETCH SUBMITTED REQUIREMENTS
$query_reqs = "SELECT prf.*, r.requirement_name 
               FROM permit_requirements_files prf
               JOIN requirements r ON prf.requirement_id = r.id
               WHERE prf.app_id = :app_id";
$stmt_reqs = $pdo->prepare($query_reqs);
$stmt_reqs->execute([':app_id' => $app_id]);
$requirements = $stmt_reqs->fetchAll(PDO::FETCH_ASSOC);

// FETCH APPLICATION LOGS
$query_logs = "SELECT * FROM application_logs WHERE app_id = :app_id ORDER BY created_at DESC";
$stmt_logs = $pdo->prepare($query_logs);
$stmt_logs->execute([':app_id' => $app_id]);
$logs = $stmt_logs->fetchAll(PDO::FETCH_ASSOC);

// Determine Status Badge Color
$statusClass = 'bg-yellow-100 text-yellow-800';
$status = strtolower($app['status']);
if (in_array($status, ['approved', 'completed', 'issued'])) {
    $statusClass = 'bg-green-100 text-green-800';
} elseif (in_array($status, ['rejected', 'returned'])) {
    $statusClass = 'bg-red-100 text-red-800';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Review Application #<?php echo str_pad($app_id, 5, '0', STR_PAD_LEFT); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;800&display=swap');
        body { font-family: 'Inter', sans-serif; }
        
        /* Modal Background Scroll Prevention */
        .modal-active { overflow: hidden; }
    </style>
</head>
<body class="bg-slate-50 p-8">

    <div class="max-w-7xl mx-auto">
        <div class="mb-6 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="applications.php" class="h-10 w-10 bg-white rounded-full flex items-center justify-center text-gray-500 hover:text-emerald-600 hover:bg-emerald-50 border border-gray-200 transition shadow-sm">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Review Application</h1>
                    <p class="text-sm text-gray-500">Application ID: #<?php echo str_pad($app['app_id'], 5, '0', STR_PAD_LEFT); ?></p>
                </div>
            </div>
            <div>
                <span class="px-4 py-1.5 rounded-full text-sm font-bold border <?php echo $statusClass; ?>">
                    Current Status: <?php echo htmlspecialchars($app['status']); ?>
                </span>
            </div>
        </div>

        <?php if(isset($_SESSION['success_msg'])): ?>
            <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg flex items-center gap-3 shadow-sm">
                <i class="fas fa-check-circle text-lg"></i>
                <span class="font-semibold"><?php echo $_SESSION['success_msg']; unset($_SESSION['success_msg']); ?></span>
            </div>
        <?php endif; ?>
        <?php if(isset($error_msg)): ?>
            <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg flex items-center gap-3 shadow-sm">
                <i class="fas fa-exclamation-triangle text-lg"></i>
                <span class="font-semibold"><?php echo $error_msg; ?></span>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 space-y-8">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                        <h3 class="font-bold text-gray-800"><i class="fas fa-info-circle text-emerald-600 mr-2"></i> Application Details</h3>
                    </div>
                    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <p class="text-xs text-gray-400 font-bold uppercase tracking-wider mb-1">Business Name</p>
                            <p class="text-gray-800 font-semibold text-lg"><?php echo htmlspecialchars($app['business_name']); ?></p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 font-bold uppercase tracking-wider mb-1">Application Type</p>
                            <p class="text-gray-800 font-semibold"><span class="bg-blue-50 text-blue-600 px-2 py-0.5 rounded text-sm border border-blue-100"><?php echo htmlspecialchars($app['app_type']); ?></span> - <?php echo htmlspecialchars($app['applicant_type']); ?></p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 font-bold uppercase tracking-wider mb-1">Applicant Name</p>
                            <p class="text-gray-800 font-semibold"><?php echo htmlspecialchars($app['firstname'] . ' ' . $app['mid_name'] . ' ' . $app['lastname']); ?></p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 font-bold uppercase tracking-wider mb-1">TIN Number</p>
                            <p class="text-gray-800 font-semibold"><?php echo htmlspecialchars($app['tin_number'] ?? 'N/A'); ?></p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 font-bold uppercase tracking-wider mb-1">Contact Info</p>
                            <p class="text-gray-800 text-sm"><i class="fas fa-envelope text-gray-400 mr-1"></i> <?php echo htmlspecialchars($app['email']); ?></p>
                            <p class="text-gray-800 text-sm mt-1"><i class="fas fa-phone text-gray-400 mr-1"></i> <?php echo htmlspecialchars($app['mobilenum']); ?></p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 font-bold uppercase tracking-wider mb-1">Location & Office</p>
                            <p class="text-gray-800 text-sm font-semibold"><?php echo htmlspecialchars($app['muncity_name'] ?? 'N/A'); ?></p>
                            <p class="text-emerald-600 text-xs font-bold mt-1"><?php echo htmlspecialchars($app['office_cover'] ?? 'N/A'); ?></p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 bg-emerald-50 text-emerald-800">
                        <h3 class="font-bold"><i class="fas fa-tasks mr-2"></i> Update Evaluation Status</h3>
                    </div>
                    <form method="POST" action="" class="p-6">
                        <div class="mb-4">
                            <label class="block text-sm font-bold text-gray-700 mb-2">New Status</label>
                            <select name="status" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-emerald-500 focus:border-emerald-500 text-gray-700">
                                <option value="" disabled selected>-- Select Action --</option>
                                <option value="Under Evaluation" <?php echo ($app['status'] == 'Under Evaluation') ? 'selected' : ''; ?>>Under Evaluation</option>
                                <option value="Returned" <?php echo ($app['status'] == 'Returned') ? 'selected' : ''; ?>>Returned (Lacks Requirements / Corrections)</option>
                                <option value="For Inspection" <?php echo ($app['status'] == 'For Inspection') ? 'selected' : ''; ?>>Approved for Inspection</option>
                                <option value="Approved" <?php echo ($app['status'] == 'Approved') ? 'selected' : ''; ?>>Approved / Issued</option>
                                <option value="Rejected" <?php echo ($app['status'] == 'Rejected') ? 'selected' : ''; ?>>Rejected</option>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-bold text-gray-700 mb-2">Remarks / Note to Applicant</label>
                            <textarea name="remarks" rows="4" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-emerald-500 focus:border-emerald-500" placeholder="State reason for return, instructions, or evaluation remarks..."></textarea>
                            <p class="text-xs text-gray-500 mt-1">This remark will be recorded in the application logs and viewable by the applicant.</p>
                        </div>
                        <div class="flex justify-end">
                            <button type="submit" name="update_application" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2.5 px-6 rounded-lg transition shadow-sm">
                                Submit Update
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="space-y-8">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                        <h3 class="font-bold text-gray-800"><i class="fas fa-file-pdf text-red-500 mr-2"></i> Submitted Documents</h3>
                    </div>
                    <div class="p-0">
                        <?php if(count($requirements) > 0): ?>
                            <ul class="divide-y divide-gray-100">
                                <?php foreach($requirements as $index => $req): ?>
                                    <li class="p-4 hover:bg-gray-50 flex items-start gap-3 transition">
                                        <i class="fas fa-file-alt text-gray-400 mt-1 text-lg"></i>
                                        <div class="flex-1">
                                            <p id="doc-title-<?php echo $index; ?>" class="text-sm font-semibold text-gray-800 mb-1 leading-tight flex items-center flex-wrap gap-2">
                                                <?php echo htmlspecialchars($req['requirement_name']); ?>
                                                <?php if(isset($req['status']) && $req['status'] === 'OK'): ?>
                                                    <span class="px-2 py-0.5 bg-green-100 text-green-700 text-[10px] uppercase tracking-wider font-bold rounded ok-badge">
                                                        <i class="fas fa-check-circle mr-1"></i> OK
                                                    </span>
                                                <?php endif; ?>
                                            </p>
                                            <div class="mt-1.5 flex items-center gap-3">
                                                <button type="button" onclick="openDocumentModal(<?php echo $index; ?>)" class="inline-flex items-center text-xs font-bold text-emerald-600 hover:text-emerald-800 transition">
                                                    <i class="fas fa-search-plus mr-1"></i> Preview
                                                </button>
                                                <a href="../<?php echo htmlspecialchars($req['file_path']); ?>" target="_blank" class="inline-flex items-center text-xs font-bold text-blue-600 hover:text-blue-800 transition">
                                                    <i class="fas fa-external-link-alt mr-1"></i> Open Direct
                                                </a>
                                            </div>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <div class="p-6 text-center text-gray-500 text-sm">
                                No requirements uploaded.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                        <h3 class="font-bold text-gray-800"><i class="fas fa-history text-blue-500 mr-2"></i> Action History</h3>
                    </div>
                    <div class="p-6">
                        <?php if(count($logs) > 0): ?>
                            <div class="relative border-l-2 border-gray-200 ml-3 space-y-6">
                                <?php foreach($logs as $log): ?>
                                    <div class="relative pl-6">
                                        <div class="absolute -left-[9px] top-1 h-4 w-4 rounded-full bg-emerald-500 border-4 border-white shadow-sm"></div>
                                        <div>
                                            <p class="text-xs font-bold text-gray-400 mb-0.5">
                                                <i class="far fa-clock mr-1"></i> <?php echo date('M d, Y h:i A', strtotime($log['created_at'])); ?>
                                            </p>
                                            <p class="text-sm font-bold text-gray-800"><?php echo htmlspecialchars($log['action']); ?></p>
                                            <?php if(!empty($log['remarks'])): ?>
                                                <div class="mt-2 text-xs text-gray-600 bg-gray-50 border border-gray-100 p-3 rounded-lg leading-relaxed">
                                                    <?php echo nl2br(htmlspecialchars($log['remarks'])); ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p class="text-sm text-gray-500 text-center">No logs available for this application.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="documentModal" class="fixed inset-0 z-50 hidden bg-black bg-opacity-75 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-5xl h-[90vh] flex flex-col overflow-hidden">
            <div class="px-6 py-4 border-b flex justify-between items-center bg-gray-50">
                <h3 id="modalDocTitle" class="font-bold text-lg text-gray-800">Document Title</h3>
                <button onclick="closeDocumentModal()" class="text-gray-400 hover:text-red-500 transition focus:outline-none">
                    <i class="fas fa-times text-2xl"></i>
                </button>
            </div>
            
            <div class="flex-1 bg-gray-200 relative p-4 flex items-center justify-center overflow-auto">
                <div id="modalAlert" class="hidden absolute top-4 left-1/2 transform -translate-x-1/2 bg-green-500 text-white px-4 py-2 rounded shadow-lg font-bold text-sm z-10 transition">
                    <i class="fas fa-check mr-2"></i> Marked as OK
                </div>
                
                <iframe id="modalPdfViewer" src="" class="w-full h-full border-0 rounded bg-white shadow-inner hidden"></iframe>
                <img id="modalImgViewer" src="" alt="Document Preview" class="max-w-full max-h-full object-contain hidden rounded shadow-sm">
                
                <div id="modalUnsupported" class="hidden text-center">
                    <i class="fas fa-file-download text-5xl text-gray-400 mb-4"></i>
                    <p class="text-gray-600 font-semibold mb-2">Preview not available for this file type.</p>
                    <a id="modalDownloadLink" href="" target="_blank" class="inline-block bg-emerald-600 text-white px-4 py-2 rounded shadow hover:bg-emerald-700 transition">Open / Download File</a>
                </div>
            </div>
            
            <div class="px-6 py-4 border-t flex justify-between items-center bg-white">
                <button id="prevDocBtn" onclick="prevDocument()" class="px-4 py-2 bg-gray-100 text-gray-700 font-semibold rounded-lg hover:bg-gray-200 transition disabled:opacity-50 disabled:cursor-not-allowed">
                    <i class="fas fa-chevron-left mr-2"></i> Previous
                </button>
                
                <form id="markOkForm" onsubmit="markDocumentOk(event)" class="m-0 flex items-center">
                    <input type="hidden" name="file_id" id="modalFileId" value="">
                    <button type="submit" id="markOkBtn" class="px-6 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 font-bold transition shadow-sm flex items-center">
                        <i class="fas fa-check-circle mr-2"></i> Mark as OK
                    </button>
                </form>

                <button id="nextDocBtn" onclick="nextDocument()" class="px-4 py-2 bg-gray-100 text-gray-700 font-semibold rounded-lg hover:bg-gray-200 transition disabled:opacity-50 disabled:cursor-not-allowed">
                    Next <i class="fas fa-chevron-right ml-2"></i>
                </button>
            </div>
        </div>
    </div>

    <script>
        // Data parsed directly from PHP
        const documentsList = <?php echo json_encode($requirements); ?>;
        let currentIndex = 0;

        const modal = document.getElementById('documentModal');
        
        // Viewer Elements
        const pdfViewer = document.getElementById('modalPdfViewer');
        const imgViewer = document.getElementById('modalImgViewer');
        const unsupportedViewer = document.getElementById('modalUnsupported');
        const downloadLink = document.getElementById('modalDownloadLink');
        
        const title = document.getElementById('modalDocTitle');
        const fileIdInput = document.getElementById('modalFileId');
        const prevBtn = document.getElementById('prevDocBtn');
        const nextBtn = document.getElementById('nextDocBtn');
        const markOkBtn = document.getElementById('markOkBtn');
        const modalAlert = document.getElementById('modalAlert');

        function openDocumentModal(index) {
            currentIndex = index;
            updateModalContent();
            modal.classList.remove('hidden');
            document.body.classList.add('modal-active');
        }

        function closeDocumentModal() {
            modal.classList.add('hidden');
            pdfViewer.src = ""; 
            imgViewer.src = "";
            document.body.classList.remove('modal-active');
            modalAlert.classList.add('hidden');
        }

        function updateModalContent() {
            if (documentsList.length === 0) return;
            
            const currentDoc = documentsList[currentIndex];
            title.innerText = currentDoc.requirement_name;
            
            // Hide all viewers initially
            pdfViewer.classList.add('hidden');
            imgViewer.classList.add('hidden');
            unsupportedViewer.classList.add('hidden');
            pdfViewer.src = "";
            imgViewer.src = "";

            // ADDING "../" HERE: If your file is inside a folder (e.g., /admin/), 
            // it steps out to find the "uploads/" folder.
            const filePath = "../" + currentDoc.file_path;

            // Display logic based on file extension
            if (filePath) {
                const ext = filePath.split('.').pop().toLowerCase();
                
                if (ext === 'pdf') {
                    pdfViewer.src = filePath;
                    pdfViewer.classList.remove('hidden');
                } else if (['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(ext)) {
                    imgViewer.src = filePath;
                    imgViewer.classList.remove('hidden');
                } else {
                    downloadLink.href = filePath;
                    unsupportedViewer.classList.remove('hidden');
                }
            } else {
                unsupportedViewer.classList.remove('hidden');
            }

            // Set File ID for the "Mark OK" form
            fileIdInput.value = currentDoc.file_id;

            // Handle "Mark OK" button visual state
            if (currentDoc.status && currentDoc.status === 'OK') {
                setButtonAsMarked();
            } else {
                setButtonAsUnmarked();
            }

            // Handle Navigation Buttons Status
            prevBtn.disabled = (currentIndex === 0);
            nextBtn.disabled = (currentIndex === (documentsList.length - 1));
        }

        function nextDocument() {
            if (currentIndex < documentsList.length - 1) {
                currentIndex++;
                updateModalContent();
                modalAlert.classList.add('hidden');
            }
        }

        function prevDocument() {
            if (currentIndex > 0) {
                currentIndex--;
                updateModalContent();
                modalAlert.classList.add('hidden');
            }
        }

        function setButtonAsMarked() {
            markOkBtn.innerHTML = '<i class="fas fa-check-double mr-2"></i> Already Marked OK';
            markOkBtn.classList.remove('bg-emerald-600', 'hover:bg-emerald-700');
            markOkBtn.classList.add('bg-gray-400', 'cursor-not-allowed');
            markOkBtn.disabled = true;
        }

        function setButtonAsUnmarked() {
            markOkBtn.innerHTML = '<i class="fas fa-check-circle mr-2"></i> Mark as OK';
            markOkBtn.classList.add('bg-emerald-600', 'hover:bg-emerald-700');
            markOkBtn.classList.remove('bg-gray-400', 'cursor-not-allowed');
            markOkBtn.disabled = false;
        }

        // Handle Form Submission via AJAX Fetch API
        async function markDocumentOk(e) {
            e.preventDefault();
            
            const formData = new FormData();
            formData.append('ajax_mark_ok', '1');
            formData.append('file_id', fileIdInput.value);

            try {
                // Keep the button text as "Saving..."
                markOkBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Saving...';
                markOkBtn.disabled = true;

                const response = await fetch('', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success) {
                    // Update Javascript Object memory
                    documentsList[currentIndex].status = 'OK';
                    
                    // Update Modal Button View
                    setButtonAsMarked();

                    // Show success banner temporarily inside modal
                    modalAlert.classList.remove('hidden');
                    setTimeout(() => modalAlert.classList.add('hidden'), 3000);

                    // Dynamically update the background list item badge
                    const titleElement = document.getElementById('doc-title-' + currentIndex);
                    if (titleElement && !titleElement.querySelector('.ok-badge')) {
                        titleElement.innerHTML += ' <span class="px-2 py-0.5 bg-green-100 text-green-700 text-[10px] uppercase tracking-wider font-bold rounded ok-badge"><i class="fas fa-check-circle mr-1"></i> OK</span>';
                    }
                } else {
                    alert("Error marking document: " + (data.error || "Unknown Error"));
                    setButtonAsUnmarked();
                }
            } catch (error) {
                console.error('Error:', error);
                alert("A connection error occurred. Please try again.");
                setButtonAsUnmarked();
            }
        }
    </script>
</body>
</html>