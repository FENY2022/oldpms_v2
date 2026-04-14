<?php
session_start();
require 'db.php';

// PREVENT UNAUTHORIZED ACCESS (Ensure it's a client)
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || $_SESSION['user_type'] !== 'client') {
    die("<div style='text-align:center; padding: 20px; font-family: sans-serif; color: red;'>Unauthorized access. Please login as a client.</div>");
}

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("<div style='text-align:center; padding: 20px; font-family: sans-serif; color: red;'>Invalid Application ID.</div>");
}

$app_id = intval($_GET['id']);
$client_id = $_SESSION['user_id']; // Adjust this if your session variable for client ID is different

// Verify the application belongs to the logged-in client
$stmt_app = $pdo->prepare("SELECT * FROM permit_applications WHERE app_id = :app_id AND client_id = :client_id");
$stmt_app->execute([':app_id' => $app_id, ':client_id' => $client_id]);
$app = $stmt_app->fetch(PDO::FETCH_ASSOC);

if (!$app) {
    die("<div style='text-align:center; padding: 20px; font-family: sans-serif; color: red;'>Application not found or you do not have permission to view it.</div>");
}

// -------------------------------------------------------------------------
// HANDLE FORM RESUBMISSION (When client uploads new files)
// -------------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['resubmit_application'])) {
    $upload_dir = 'uploads/applications/'; // Make sure this folder exists
    if (!is_dir($upload_dir)) { mkdir($upload_dir, 0777, true); }
    
    $files_updated = 0;

    try {
        $pdo->beginTransaction();

        // Loop through uploaded files
        if (isset($_FILES['requirements'])) {
            foreach ($_FILES['requirements']['name'] as $req_id => $filename) {
                if ($_FILES['requirements']['error'][$req_id] === UPLOAD_ERR_OK) {
                    $tmp_name = $_FILES['requirements']['tmp_name'][$req_id];
                    $ext = pathinfo($filename, PATHINFO_EXTENSION);
                    
                    // Generate new secure filename: appID_reqID_timestamp.ext
                    $new_filename = $app_id . '_' . $req_id . '_' . time() . '.' . $ext;
                    $destination = $upload_dir . $new_filename;

                    if (move_uploaded_file($tmp_name, $destination)) {
                        // Update the specific file in the database, RESET status and remarks
                        $stmt_update_file = $pdo->prepare("
                            UPDATE permit_requirements_files 
                            SET file_path = :file_path, status = 'Pending', remarks = NULL 
                            WHERE app_id = :app_id AND requirement_id = :req_id
                        ");
                        $stmt_update_file->execute([
                            ':file_path' => $destination,
                            ':app_id' => $app_id,
                            ':req_id' => $req_id
                        ]);
                        $files_updated++;
                    }
                }
            }
        }

        // Only update the application status if it was "Returned"
        if ($app['status'] === 'Returned') {
            $stmt_update_app = $pdo->prepare("UPDATE permit_applications SET status = 'Under Evaluation' WHERE app_id = :app_id");
            $stmt_update_app->execute([':app_id' => $app_id]);

            // Add to logs
            $stmt_log = $pdo->prepare("INSERT INTO application_logs (app_id, action, remarks) VALUES (:app_id, :action, :remarks)");
            $stmt_log->execute([
                ':app_id' => $app_id,
                ':action' => 'Application Resubmitted',
                ':remarks' => "Client updated $files_updated incorrect file(s) and resubmitted the application."
            ]);
        }

        $pdo->commit();
        $_SESSION['success_msg'] = "Application successfully updated and resubmitted!";
        header("Location: update_application.php?id=" . $app_id);
        exit;

    } catch (Exception $e) {
        $pdo->rollBack();
        $error_msg = "Error updating files: " . $e->getMessage();
    }
}

// FETCH LATEST LOG FOR RETURN REASON
$stmt_log = $pdo->prepare("SELECT remarks, created_at FROM application_logs WHERE app_id = :app_id AND action LIKE '%Returned%' ORDER BY created_at DESC LIMIT 1");
$stmt_log->execute([':app_id' => $app_id]);
$return_log = $stmt_log->fetch(PDO::FETCH_ASSOC);

// FETCH SUBMITTED REQUIREMENTS & THEIR STATUS
$query_reqs = "SELECT prf.*, r.requirement_name 
               FROM permit_requirements_files prf
               JOIN requirements r ON prf.requirement_id = r.id
               WHERE prf.app_id = :app_id";
$stmt_reqs = $pdo->prepare($query_reqs);
$stmt_reqs->execute([':app_id' => $app_id]);
$requirements = $stmt_reqs->fetchAll(PDO::FETCH_ASSOC);

// Check if there are any incorrect files
$has_incorrect_files = false;
foreach ($requirements as $req) {
    if ($req['status'] === 'Incorrect') {
        $has_incorrect_files = true;
        break;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Application #<?php echo str_pad($app_id, 5, '0', STR_PAD_LEFT); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;800&display=swap');
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 p-8">

    <div class="max-w-4xl mx-auto">
        <div class="mb-6 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="my_applications.php" class="h-10 w-10 bg-white rounded-full flex items-center justify-center text-gray-500 hover:text-blue-600 hover:bg-blue-50 border border-gray-200 transition shadow-sm">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Application Details</h1>
                    <p class="text-sm text-gray-500">Business: <?php echo htmlspecialchars($app['business_name']); ?></p>
                </div>
            </div>
            <div>
                <?php 
                    $statusColor = 'bg-yellow-100 text-yellow-800';
                    if($app['status'] == 'Returned') $statusColor = 'bg-red-100 text-red-800';
                    if($app['status'] == 'Approved') $statusColor = 'bg-green-100 text-green-800';
                ?>
                <span class="px-4 py-1.5 rounded-full text-sm font-bold border <?php echo $statusColor; ?>">
                    Status: <?php echo htmlspecialchars($app['status']); ?>
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

        <?php if($app['status'] === 'Returned' && $return_log): ?>
            <div class="mb-8 bg-red-50 border-l-4 border-red-500 p-6 rounded-r-lg shadow-sm">
                <h3 class="text-red-800 font-bold text-lg flex items-center mb-2">
                    <i class="fas fa-exclamation-circle mr-2"></i> Action Required: Application Returned
                </h3>
                <p class="text-red-700 text-sm mb-4">Your application was returned for the following reason:</p>
                <div class="bg-white p-4 rounded border border-red-200 text-gray-800 text-sm italic">
                    "<?php echo nl2br(htmlspecialchars($return_log['remarks'])); ?>"
                </div>
                <p class="text-red-700 text-xs mt-3">Please review the documents marked as <strong>Incorrect</strong> below, upload the correct files, and resubmit.</p>
            </div>
        <?php endif; ?>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                <h3 class="font-bold text-gray-800"><i class="fas fa-folder-open text-blue-500 mr-2"></i> Submitted Requirements</h3>
            </div>
            
            <form action="" method="POST" enctype="multipart/form-data" class="p-0 m-0">
                <ul class="divide-y divide-gray-100">
                    <?php foreach($requirements as $req): ?>
                        <li class="p-6 hover:bg-gray-50 transition <?php echo ($req['status'] === 'Incorrect') ? 'bg-red-50/30' : ''; ?>">
                            <div class="flex flex-col md:flex-row md:items-start justify-between gap-4">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-1">
                                        <h4 class="font-semibold text-gray-800 text-sm"><?php echo htmlspecialchars($req['requirement_name']); ?></h4>
                                        
                                        <?php if($req['status'] === 'OK'): ?>
                                            <span class="px-2 py-0.5 bg-green-100 text-green-700 text-[10px] uppercase tracking-wider font-bold rounded"><i class="fas fa-check-circle mr-1"></i> Verified OK</span>
                                        <?php elseif($req['status'] === 'Incorrect'): ?>
                                            <span class="px-2 py-0.5 bg-red-100 text-red-700 text-[10px] uppercase tracking-wider font-bold rounded"><i class="fas fa-times-circle mr-1"></i> Incorrect File</span>
                                        <?php else: ?>
                                            <span class="px-2 py-0.5 bg-gray-100 text-gray-600 text-[10px] uppercase tracking-wider font-bold rounded"><i class="fas fa-clock mr-1"></i> Under Review</span>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="text-xs text-gray-500 mb-2">
                                        Current File: <a href="<?php echo htmlspecialchars($req['file_path']); ?>" target="_blank" class="text-blue-600 hover:underline">View Uploaded File</a>
                                    </div>

                                    <?php if($req['status'] === 'Incorrect'): ?>
                                        <div class="mt-3 p-3 bg-red-100/50 border border-red-200 rounded text-sm text-red-800">
                                            <strong class="block text-xs uppercase tracking-wider text-red-600 mb-1">Evaluator Remark:</strong>
                                            <?php echo htmlspecialchars($req['remarks'] ?? 'File is incorrect. Please upload the correct document.'); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <?php if($app['status'] === 'Returned' && $req['status'] === 'Incorrect'): ?>
                                    <div class="md:w-1/3 mt-2 md:mt-0">
                                        <label class="block text-xs font-bold text-gray-700 mb-1">Upload Replacement File (PDF/Image)</label>
                                        <input type="file" name="requirements[<?php echo $req['requirement_id']; ?>]" accept=".pdf,.png,.jpg,.jpeg" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 border border-gray-200 rounded cursor-pointer bg-white" required>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>

                <?php if($app['status'] === 'Returned' && $has_incorrect_files): ?>
                    <div class="px-6 py-5 bg-gray-50 border-t border-gray-200 flex justify-end">
                        <button type="submit" name="resubmit_application" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 px-8 rounded-lg transition shadow-md flex items-center">
                            <i class="fas fa-paper-plane mr-2"></i> Resubmit Application
                        </button>
                    </div>
                <?php endif; ?>
            </form>
        </div>
    </div>

</body>
</html>