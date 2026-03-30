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
$app_type = isset($_GET['type']) ? ucfirst($_GET['type']) : 'New';

// 1. Fetch User Data
$stmt = $pdo->prepare("SELECT * FROM user_client WHERE client_id = ?");
$stmt->execute([$client_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("User not found.");
}

// Fetch Location Data for Dropdowns
$provinces = $pdo->query("SELECT prov_code, prov_name FROM province ORDER BY prov_name")->fetchAll(PDO::FETCH_ASSOC);
$muncities = $pdo->query("SELECT mun_code, muncity_name, prov_code, zip_code FROM muncity ORDER BY muncity_name")->fetchAll(PDO::FETCH_ASSOC);
$barangays = $pdo->query("SELECT brgy_code, brgy_name, mun_code FROM brgy ORDER BY brgy_name")->fetchAll(PDO::FETCH_ASSOC);

// 2. Define Requirements based on App Type (New vs Renewal)
$requirements = [];

if ($app_type == 'New') {
    $requirements = [
        ['id' => 1, 'requirement_name' => 'Application form duly accomplished & sworn/notarized.'],
        ['id' => 2, 'requirement_name' => 'Lumber Supply Contract/Agreement from legitimate suppliers/subsisting lumber dealers.'],
        ['id' => 3, 'requirement_name' => "Mayor's Permit/Business Permit"],
        ['id' => 4, 'requirement_name' => 'Annual Business Plan/Program'],
        ['id' => 5, 'requirement_name' => 'Latest Income Tax Return'],
        ['id' => 6, 'requirement_name' => 'Ending stocked inventory report duly subscribed/sworn'], // Made Optional via Switch UI
        ['id' => 7, 'requirement_name' => 'Proof of ownership of the lumberyard or consent/agreement with the owner']
    ];
} elseif ($app_type == 'Renewal') {
    $requirements = [
        ['id' => 1, 'requirement_name' => 'Application form duly accomplished & sworn/notarized.'],
        ['id' => 2, 'requirement_name' => 'Lumber Supply Contract/Agreement from legitimate suppliers/subsisting lumber dealers.'],
        ['id' => 3, 'requirement_name' => "Mayor's Permit/Business Permit"],
        ['id' => 4, 'requirement_name' => 'Annual Business Plan/Program'],
        ['id' => 5, 'requirement_name' => 'Latest Income Tax Return'],
        ['id' => 6, 'requirement_name' => 'Ending stocked inventory report duly subscribed/sworn'],
        ['id' => 7, 'requirement_name' => 'Summary reports showing the monthly lumber purchases, production, disposition/sales ending inventory report and other relevant information within the tenure of the permit duly attested by the CENRO concerned.']
    ];
}

// 3. Handle Form Submission & File Uploads
$success_msg = '';
$error_msg = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_application'])) {
    // Capture New Fields
    $applicant_type = htmlspecialchars($_POST['applicant_type']);
    $business_name = htmlspecialchars($_POST['business_name']);
    $tin_number = htmlspecialchars($_POST['tin_number']);
    $province_id = htmlspecialchars($_POST['province_id']);
    $muncity_id = htmlspecialchars($_POST['muncity_id']);
    $brgy_id = htmlspecialchars($_POST['brgy_id']);
    $zip_code = htmlspecialchars($_POST['zip_code']);
    $street_address = htmlspecialchars($_POST['street_address']);
    
    // Capture Reference Number if it's a renewal, otherwise set to null
    $reference_number = isset($_POST['reference_number']) ? htmlspecialchars($_POST['reference_number']) : null;
    
    try {
        $pdo->beginTransaction();

        // Insert Application Record
        $stmtApp = $pdo->prepare("INSERT INTO permit_applications (client_id, app_type, applicant_type, business_name, tin_number, reference_number, province_id, muncity_id, brgy_id, zip_code, street_address) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmtApp->execute([$client_id, $app_type, $applicant_type, $business_name, $tin_number, $reference_number, $province_id, $muncity_id, $brgy_id, $zip_code, $street_address]);
        $new_app_id = $pdo->lastInsertId();

        // Create uploads directory if it doesn't exist
        $upload_dir = 'uploads/applications/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $stmtFile = $pdo->prepare("INSERT INTO permit_requirements_files (app_id, requirement_id, file_path) VALUES (?, ?, ?)");

        // Process File Uploads (Now handles arrays of files)
        foreach ($requirements as $req) {
            $input_name = 'req_' . $req['id'];
            
            // --- Custom Logic for Optional Requirement 6 in New Applications ---
            $is_req_6_new = ($app_type == 'New' && $req['id'] == 6);
            $user_opted_in = isset($_POST['include_req_6']) && $_POST['include_req_6'] === 'yes';

            // If it's the optional req 6 and the user turned the switch OFF, skip validation entirely
            if ($is_req_6_new && !$user_opted_in) {
                continue; 
            }
            // -------------------------------------------------------------------

            // Check if any files were uploaded for this requirement
            if (isset($_FILES[$input_name]) && !empty($_FILES[$input_name]['name'][0])) {
                
                $file_count = count($_FILES[$input_name]['name']);
                
                // Loop through all uploaded files for this specific requirement
                for ($i = 0; $i < $file_count; $i++) {
                    if ($_FILES[$input_name]['error'][$i] == 0) {
                        $file_tmp = $_FILES[$input_name]['tmp_name'][$i];
                        $original_name = $_FILES[$input_name]['name'][$i];
                        $file_ext = strtolower(pathinfo($original_name, PATHINFO_EXTENSION));
                        
                        // Security check: Only allow PDFs
                        if ($file_ext !== 'pdf') {
                            throw new Exception("Invalid file type for '" . $req['requirement_name'] . "'. Only PDF files are allowed.");
                        }
                        
                        // Generate a unique, safe filename: AppID_ReqID_Index_Timestamp.pdf
                        $new_file_name = $new_app_id . '_' . $req['id'] . '_' . $i . '_' . time() . '.' . $file_ext;
                        $destination = $upload_dir . $new_file_name;

                        if (move_uploaded_file($file_tmp, $destination)) {
                            $stmtFile->execute([$new_app_id, $req['id'], $destination]);
                        } else {
                            throw new Exception("Failed to upload a file for " . $req['requirement_name']);
                        }
                    }
                }
            } else {
                // If NO files were uploaded for a requirement
                throw new Exception("Missing required document: " . $req['requirement_name']);
            }
        }

        // --- NEW: Add initial Audit Trail Log ---
        $action = "Application Submitted";
        $remarks = "Application successfully submitted subject for evaluation. Note: Your application will be evaluated. Complete and correct documents will be officially received and processed, while incomplete documents will be returned and end the transaction. You will be notified of the status of your application thru SMS and to your O-LDPMS registered account. For the return application, it is indicated in the notification either lacks requirements or correction of the wrong data entry in the required documents. Upon compliance, you may reapply using the registered O-LDPMS account.";        $stmtLog = $pdo->prepare("INSERT INTO application_logs (app_id, action, remarks) VALUES (?, ?, ?)");
        $stmtLog->execute([$new_app_id, $action, $remarks]);
        // ----------------------------------------

        $pdo->commit();
        $success_msg = "Application submitted successfully!";
        
    } catch (Exception $e) {
        $pdo->rollBack(); // Now this will work properly if an exception is thrown
        $error_msg = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $app_type ?> Application | O-LDPMS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="icon" href="logo/denr_logo.png" type="image/x-icon">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;800&display=swap');
        body { font-family: 'Inter', sans-serif; }
        
        /* Toast Animations */
        .toast-enter { transform: translateY(-100%); opacity: 0; }
        .toast-enter-active { transform: translateY(0); opacity: 1; transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); }
        .toast-exit { transform: translateY(-100%); opacity: 0; transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); }
        
        /* Custom scrollbar for file lists */
        .file-list::-webkit-scrollbar { width: 4px; }
        .file-list::-webkit-scrollbar-track { background: transparent; }
        .file-list::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
    </style>
</head>
<body class="bg-slate-50 min-h-screen pb-20">

    <div id="toast-container" class="fixed top-5 left-1/2 -translate-x-1/2 z-[100] flex flex-col gap-3 pointer-events-none w-full max-w-md"></div>

    <nav class="bg-emerald-900 text-white h-16 flex items-center justify-between px-6 sticky top-0 z-50 shadow-md">
        <a href="dashboard.php" target="_top" class="flex items-center gap-2 text-emerald-200 hover:text-white font-semibold transition">
            <i class="fas fa-arrow-left"></i> <span class="hidden sm:inline">Back to Dashboard</span>
        </a>
        <div class="font-black tracking-tight text-lg uppercase flex items-center gap-3">
            <img src="logo/denr_logo.png" class="h-8 w-8" alt="Logo">
            O-LDPMS <span class="text-emerald-400 font-medium text-sm hidden sm:inline">| <?= $app_type ?> Application</span>
        </div>
        <div class="w-24 sm:w-32"></div> 
    </nav>

    <form method="POST" enctype="multipart/form-data" id="applicationForm" class="max-w-5xl mx-auto mt-10 px-4">
        
        <input type="hidden" name="submit_application" value="1">

        <div class="mb-8">
            <h2 class="text-3xl font-extrabold text-gray-900">Create <?= $app_type ?> Application</h2>
            <p class="text-gray-500 mt-2">Complete the information below and upload your scanned PDF documents to submit your application. You may upload multiple PDFs per requirement.</p>
        </div>

        <div class="bg-white p-6 sm:p-10 rounded-2xl shadow-sm border border-gray-100 mb-8">
            <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-3">
                <span class="bg-emerald-100 text-emerald-700 h-8 w-8 rounded-full flex items-center justify-center text-sm">1</span> 
                Applicant & Business Information
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Full Name</label>
                    <div class="relative">
                        <i class="fas fa-user absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        <input type="text" readonly value="<?= htmlspecialchars($user['firstname'] . ' ' . $user['mid_name'] . ' ' . $user['lastname']) ?>" class="w-full bg-gray-50 border border-gray-200 text-gray-500 rounded-xl pl-10 pr-4 py-3 cursor-not-allowed select-none">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Email Address</label>
                    <div class="relative">
                        <i class="fas fa-envelope absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        <input type="text" readonly value="<?= htmlspecialchars($user['email']) ?>" class="w-full bg-gray-50 border border-gray-200 text-gray-500 rounded-xl pl-10 pr-4 py-3 cursor-not-allowed select-none">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Mobile Number</label>
                    <div class="relative">
                        <i class="fas fa-phone absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        <input type="text" readonly value="<?= htmlspecialchars($user['mobilenum']) ?>" class="w-full bg-gray-50 border border-gray-200 text-gray-500 rounded-xl pl-10 pr-4 py-3 cursor-not-allowed select-none">
                    </div>
                </div>
                
                <div class="md:col-span-2 border-t border-gray-100 pt-6 mt-2">
                    <h4 class="text-sm font-bold text-gray-800 mb-4 bg-yellow-50 text-yellow-800 border border-yellow-200 inline-block px-4 py-1.5 rounded-lg"><i class="fas fa-exclamation-circle mr-2"></i> Action Required: Fill out remaining details</h4>
                </div>

                <?php if ($app_type == 'Renewal'): ?>
                <div class="md:col-span-2 bg-blue-50 border border-blue-200 p-5 rounded-xl mb-2">
                    <label class="block text-xs font-bold text-blue-800 uppercase tracking-wider mb-2">Existing Permit Reference No. / Lumber Dealer No. <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <i class="fas fa-sync-alt absolute left-4 top-1/2 -translate-y-1/2 text-blue-400"></i>
                        <input type="text" id="reference_number" name="reference_number" required placeholder="Enter your previous permit or reference number" class="w-full bg-white border border-blue-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 text-gray-800 rounded-xl pl-10 pr-4 py-3 transition outline-none shadow-sm">
                    </div>
                </div>
                <?php endif; ?>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Type of Application <span class="text-red-500">*</span></label>
                    <select id="applicant_type" name="applicant_type" required class="w-full bg-white border border-gray-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 text-gray-800 rounded-xl px-4 py-3 transition outline-none shadow-sm cursor-pointer">
                        <option value="" disabled selected>Select Type</option>
                        <option value="Individual">Individual</option>
                        <option value="Association">Association</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Business / Trade Name <span class="text-red-500">*</span></label>
                    <input type="text" id="business_name" name="business_name" required placeholder="Enter registered business name" class="w-full bg-white border border-gray-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 text-gray-800 rounded-xl px-4 py-3 transition outline-none shadow-sm">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">TIN Number <span class="text-red-500">*</span></label>
                    <input type="text" id="tin_number" name="tin_number" required placeholder="000-000-000-000" class="w-full bg-white border border-gray-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 text-gray-800 rounded-xl px-4 py-3 transition outline-none shadow-sm">
                </div>
            </div>

            <div class="mt-8 border-t border-gray-100 pt-6">
                <h4 class="text-sm font-bold text-gray-800 mb-4 uppercase tracking-wider"><i class="fas fa-map-marker-alt text-emerald-600 mr-2"></i> Business Address Location</h4>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                    <div class="lg:col-span-2">
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Province <span class="text-red-500">*</span></label>
                        <select id="province" name="province_id" required class="w-full bg-white border border-gray-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 text-gray-800 rounded-xl px-4 py-3 transition outline-none shadow-sm cursor-pointer">
                            <option value="" disabled selected>Select Province</option>
                            <?php foreach ($provinces as $prov): ?>
                                <option value="<?= htmlspecialchars($prov['prov_code']) ?>"><?= htmlspecialchars($prov['prov_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="lg:col-span-2">
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">City/Municipality <span class="text-red-500">*</span></label>
                        <select id="muncity" name="muncity_id" required disabled class="w-full bg-gray-50 border border-gray-300 text-gray-500 rounded-xl px-4 py-3 transition outline-none shadow-sm disabled:cursor-not-allowed">
                            <option value="" disabled selected>Select Municipality first</option>
                        </select>
                    </div>

                    <div class="lg:col-span-2">
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Barangay <span class="text-red-500">*</span></label>
                        <select id="brgy" name="brgy_id" required disabled class="w-full bg-gray-50 border border-gray-300 text-gray-500 rounded-xl px-4 py-3 transition outline-none shadow-sm disabled:cursor-not-allowed">
                            <option value="" disabled selected>Select City/Municipality first</option>
                        </select>
                    </div>

                    <div class="lg:col-span-2">
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Zip Code <span class="text-red-500">*</span></label>
                        <input type="text" id="zip_code" name="zip_code" required placeholder="0000" class="w-full bg-white border border-gray-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 text-gray-800 rounded-xl px-4 py-3 transition outline-none shadow-sm">
                    </div>

                    <div class="md:col-span-2 lg:col-span-4">
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Street Address (Optional)</label>
                        <input type="text" id="street_address" name="street_address" placeholder="Bldg/House No., Street Name, Subdivision, etc." class="w-full bg-white border border-gray-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 text-gray-800 rounded-xl px-4 py-3 transition outline-none shadow-sm">
                    </div>
                </div>
            </div>

        </div>

        <div class="bg-white p-6 sm:p-10 rounded-2xl shadow-sm border border-gray-100 mb-8">
            <h3 class="text-xl font-bold text-gray-900 mb-2 flex items-center gap-3">
                <span class="bg-emerald-100 text-emerald-700 h-8 w-8 rounded-full flex items-center justify-center text-sm">2</span> 
                Upload Requirements
            </h3>
            <p class="text-gray-500 text-sm mb-8 sm:ml-11">Please upload the required documents below. All files must be strictly in <strong class="text-red-500">PDF format</strong>. You can select multiple files at once.</p>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 sm:ml-11">
                <?php foreach($requirements as $req): ?>
                    <?php 
                        // Flag to check if this is the specific optional requirement for New applications
                        $is_optional_req6 = ($app_type == 'New' && $req['id'] == 6); 
                    ?>
                    
                    <div class="relative">
                        <?php if ($is_optional_req6): ?>
                            <div class="absolute top-2 right-2 z-20 flex items-center gap-2 bg-white px-3 py-1.5 rounded-lg shadow-sm border border-gray-200">
                                <span class="text-xs font-bold text-gray-600">Comply?</span>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="include_req_6" value="yes" id="toggle_req_6" class="sr-only peer" onchange="toggleReq6()">
                                    <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-emerald-600"></div>
                                </label>
                            </div>
                        <?php endif; ?>

                        <div id="upload_box_<?= $req['id'] ?>" class="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center hover:border-emerald-500 transition bg-slate-50 relative group flex flex-col items-center justify-center min-h-[160px] w-full <?= $is_optional_req6 ? 'opacity-50 grayscale pointer-events-none' : '' ?>">
                            <input type="file" name="req_<?= $req['id'] ?>[]" id="input_req_<?= $req['id'] ?>" accept=".pdf" multiple <?= $is_optional_req6 ? '' : 'required' ?> 
                                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" 
                                onchange="updateFileName(this, 'file_name_<?= $req['id'] ?>')">
                            
                            <i class="upload-icon fas fa-cloud-upload-alt text-3xl text-gray-400 group-hover:text-emerald-500 transition mb-3"></i>
                            <p class="font-bold text-gray-800 text-sm mb-1 leading-snug px-2"><?= htmlspecialchars($req['requirement_name']) ?> <span class="text-red-500 <?= $is_optional_req6 ? 'hidden' : '' ?>" id="asterisk_<?= $req['id'] ?>">*</span></p>
                            
                            <?php if($req['id'] == 2): ?>
                                <p class="text-[10px] text-gray-400 px-2 leading-tight mb-2">(Not required if applicant is a mini-sawmill permittee)</p>
                            <?php else: ?>
                                <p class="text-xs text-gray-500 mb-2">Click or drag PDF(s) to upload</p>
                            <?php endif; ?>
                            
                            <div id="file_name_<?= $req['id'] ?>" class="text-xs font-bold text-emerald-700 mt-2 w-full px-2 max-h-24 overflow-y-auto file-list text-left space-y-1"></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="flex justify-end pt-4">
            <button type="submit" id="submitBtn" class="w-full sm:w-auto bg-emerald-700 text-white px-10 py-4 rounded-xl font-bold hover:bg-emerald-800 shadow-xl shadow-emerald-900/20 transition active:scale-[0.98] text-lg flex items-center justify-center gap-3">
                Submit Application <i class="fas fa-paper-plane"></i>
            </button>
        </div>
    </form>

    <div id="confirmationModal" class="fixed inset-0 z-[100] hidden flex items-center justify-center bg-black/50 backdrop-blur-sm transition-opacity duration-300 opacity-0">
        <div id="modalInner" class="bg-white rounded-2xl shadow-xl w-full max-w-2xl mx-4 transform scale-95 transition-transform duration-300">
            <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                <h3 class="text-xl font-bold text-gray-900">Confirm Application Details</h3>
                <button type="button" id="closeModalBtn" class="text-gray-400 hover:text-gray-600 transition"><i class="fas fa-times text-xl"></i></button>
            </div>
            
            <div class="p-6 space-y-4 text-sm text-gray-700 max-h-[60vh] overflow-y-auto file-list">
                <p class="mb-2 text-gray-500">Please review your information before submitting. Once confirmed, your application will be forwarded to CENRO FUU for review.</p>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-gray-50 p-4 rounded-xl border border-gray-100">
                    <div>
                        <span class="font-bold text-gray-900 block text-xs uppercase tracking-wide mb-1">Applicant Type</span>
                        <span id="summary_applicant_type" class="text-emerald-700 font-semibold"></span>
                    </div>
                    <div>
                        <span class="font-bold text-gray-900 block text-xs uppercase tracking-wide mb-1">Business/Trade Name</span>
                        <span id="summary_business_name" class="text-emerald-700 font-semibold"></span>
                    </div>
                    <div>
                        <span class="font-bold text-gray-900 block text-xs uppercase tracking-wide mb-1">TIN Number</span>
                        <span id="summary_tin_number" class="text-emerald-700 font-semibold"></span>
                    </div>
                    <?php if ($app_type == 'Renewal'): ?>
                    <div>
                        <span class="font-bold text-gray-900 block text-xs uppercase tracking-wide mb-1">Reference No.</span>
                        <span id="summary_reference_number" class="text-emerald-700 font-semibold"></span>
                    </div>
                    <?php endif; ?>
                </div>
                
                <div class="mt-4 pt-4">
                    <span class="font-bold text-gray-900 block text-xs uppercase tracking-wide mb-2"><i class="fas fa-map-marker-alt text-emerald-600 mr-1"></i> Business Address</span>
                    <p id="summary_full_address" class="bg-gray-50 p-3 rounded-lg border border-gray-100 text-gray-600 leading-relaxed"></p>
                </div>
            </div>

            <div class="p-6 border-t border-gray-100 bg-gray-50 rounded-b-2xl flex flex-col-reverse sm:flex-row justify-end gap-3">
                <button type="button" id="cancelSubmitBtn" class="px-6 py-2.5 rounded-xl font-semibold text-gray-600 bg-white border border-gray-300 hover:bg-gray-100 transition w-full sm:w-auto">Cancel</button>
                <button type="button" id="confirmSubmitBtn" class="px-6 py-2.5 rounded-xl font-bold text-white bg-emerald-700 hover:bg-emerald-800 shadow-md transition flex items-center justify-center gap-2 w-full sm:w-auto">Confirm & Submit <i class="fas fa-check"></i></button>
            </div>
        </div>
    </div>

    <script>
        // --- Optional Requirement Toggle Logic ---
        function toggleReq6() {
            const toggle = document.getElementById('toggle_req_6');
            const box = document.getElementById('upload_box_6');
            const input = document.getElementById('input_req_6');
            const asterisk = document.getElementById('asterisk_6');
            const fileList = document.getElementById('file_name_6');
            const icon = box.querySelector('.upload-icon');

            if (toggle.checked) {
                // Enable the box
                box.classList.remove('opacity-50', 'grayscale', 'pointer-events-none');
                input.required = true;
                asterisk.classList.remove('hidden');
                
                // ADDED: Toast Notification
                showToast("Requirement included. Please upload the document.", "success");
            } else {
                // Disable the box
                box.classList.add('opacity-50', 'grayscale', 'pointer-events-none');
                input.required = false;
                asterisk.classList.add('hidden');
                
                // Clear selected files & reset styles
                input.value = '';
                fileList.innerHTML = '';
                box.classList.remove('border-emerald-500', 'bg-emerald-50');
                box.classList.add('border-gray-300', 'bg-slate-50');
                icon.classList.replace('text-emerald-500', 'text-gray-400');
                
                // ADDED: Toast Notification
                showToast("Requirement marked as optional and skipped.", "success");
            }
        }

        // --- Embed Database Location Data into JavaScript ---
        const muncitiesData = <?= json_encode($muncities) ?>;
        const barangaysData = <?= json_encode($barangays) ?>;

        const provinceSelect = document.getElementById('province');
        const muncitySelect = document.getElementById('muncity');
        const brgySelect = document.getElementById('brgy');
        const zipCodeInput = document.getElementById('zip_code');

        // Handle Province Change -> Load Municipalities
        provinceSelect.addEventListener('change', function() {
            const selectedProv = this.value;
            
            // Reset downstream selects
            muncitySelect.innerHTML = '<option value="" disabled selected>Select Municipality</option>';
            brgySelect.innerHTML = '<option value="" disabled selected>Select City/Municipality first</option>';
            brgySelect.disabled = true;
            brgySelect.classList.add('bg-gray-50', 'text-gray-500', 'cursor-not-allowed');
            brgySelect.classList.remove('bg-white', 'text-gray-800', 'cursor-pointer');
            zipCodeInput.value = '';

            // Filter municipalities
            const filteredMuncities = muncitiesData.filter(m => m.prov_code === selectedProv);
            
            if (filteredMuncities.length > 0) {
                muncitySelect.disabled = false;
                muncitySelect.classList.remove('bg-gray-50', 'text-gray-500', 'cursor-not-allowed');
                muncitySelect.classList.add('bg-white', 'text-gray-800', 'cursor-pointer');
                
                filteredMuncities.forEach(m => {
                    const option = document.createElement('option');
                    option.value = m.mun_code;
                    option.dataset.zipcode = m.zip_code; 
                    option.textContent = m.muncity_name;
                    muncitySelect.appendChild(option);
                });
            } else {
                muncitySelect.disabled = true;
            }
        });

        // Handle Municipality Change -> Load Barangays & Set Zip Code
        muncitySelect.addEventListener('change', function() {
            const selectedMun = this.value;
            
            // Auto-fill zip code
            const selectedOption = this.options[this.selectedIndex];
            if(selectedOption.dataset.zipcode) {
                zipCodeInput.value = selectedOption.dataset.zipcode;
            }

            // Reset Barangay select
            brgySelect.innerHTML = '<option value="" disabled selected>Select Barangay</option>';
            
            // Filter barangays
            const filteredBarangays = barangaysData.filter(b => b.mun_code === selectedMun);

            if (filteredBarangays.length > 0) {
                brgySelect.disabled = false;
                brgySelect.classList.remove('bg-gray-50', 'text-gray-500', 'cursor-not-allowed');
                brgySelect.classList.add('bg-white', 'text-gray-800', 'cursor-pointer');
                
                filteredBarangays.forEach(b => {
                    const option = document.createElement('option');
                    option.value = b.brgy_code;
                    option.textContent = b.brgy_name;
                    brgySelect.appendChild(option);
                });
            } else {
                brgySelect.disabled = true;
            }
        });


        // --- 1. Dynamic Multiple File Name Display System ---
        function updateFileName(input, textId) {
            const listContainer = document.getElementById(textId);
            const containerBox = input.parentElement;
            const icon = containerBox.querySelector('.upload-icon');

            if (input.files && input.files.length > 0) {
                let htmlList = '';
                let isValid = true;

                // Loop through all selected files to display them and validate
                for(let i = 0; i < input.files.length; i++) {
                    const fileName = input.files[i].name;
                    
                    // Validate PDF
                    if(!fileName.toLowerCase().endsWith('.pdf')) {
                        isValid = false;
                        break;
                    }
                    htmlList += `<div class="truncate bg-emerald-100/50 rounded px-2 py-1"><i class="fas fa-check-circle mr-1"></i> ${fileName}</div>`;
                }

                if(!isValid) {
                    showToast("Please select strictly PDF files only.", "error");
                    input.value = ''; // Reset input
                    listContainer.innerHTML = '';
                    containerBox.classList.remove('border-emerald-500', 'bg-emerald-50');
                    containerBox.classList.add('border-gray-300', 'bg-slate-50');
                    icon.classList.replace('text-emerald-500', 'text-gray-400');
                    return;
                }

                listContainer.innerHTML = htmlList;
                
                // Change Styles to indicate success
                containerBox.classList.add('border-emerald-500', 'bg-emerald-50');
                containerBox.classList.remove('border-gray-300', 'bg-slate-50');
                icon.classList.replace('text-gray-400', 'text-emerald-500');
            } else {
                listContainer.innerHTML = '';
                containerBox.classList.remove('border-emerald-500', 'bg-emerald-50');
                containerBox.classList.add('border-gray-300', 'bg-slate-50');
                icon.classList.replace('text-emerald-500', 'text-gray-400');
            }
        }

        // --- 2. Form Submission Intercept & Modal Logic ---
        let isConfirmed = false;
        const form = document.getElementById('applicationForm');
        const modal = document.getElementById('confirmationModal');
        const modalInner = document.getElementById('modalInner');

        form.addEventListener('submit', function(e) {
            // If the user hasn't confirmed via the modal yet, intercept the submission
            if (!isConfirmed) {
                e.preventDefault(); 
                
                // Populate Modal Summary Details
                document.getElementById('summary_applicant_type').textContent = document.getElementById('applicant_type').value || 'N/A';
                document.getElementById('summary_business_name').textContent = document.getElementById('business_name').value || 'N/A';
                document.getElementById('summary_tin_number').textContent = document.getElementById('tin_number').value || 'N/A';
                
                <?php if ($app_type == 'Renewal'): ?>
                const refInput = document.getElementById('reference_number');
                if(refInput) {
                    document.getElementById('summary_reference_number').textContent = refInput.value || 'N/A';
                }
                <?php endif; ?>

                // Construct full address by grabbing the text of the selected options
                const provText = provinceSelect.options[provinceSelect.selectedIndex]?.text || '';
                const munText = muncitySelect.options[muncitySelect.selectedIndex]?.text || '';
                const brgyText = brgySelect.options[brgySelect.selectedIndex]?.text || '';
                const zipText = zipCodeInput.value || '';
                const streetText = document.getElementById('street_address').value || '';
                
                let addressParts = [];
                if(streetText) addressParts.push(streetText);
                if(brgyText && !brgySelect.disabled) addressParts.push(brgyText);
                if(munText && !muncitySelect.disabled) addressParts.push(munText);
                if(provText && provinceSelect.value) addressParts.push(provText);
                if(zipText) addressParts.push(zipText);

                document.getElementById('summary_full_address').textContent = addressParts.join(', ') || 'N/A';

                // Display the Modal with Tailwind Transitions
                modal.classList.remove('hidden');
                // Force browser reflow to enable transition
                void modal.offsetWidth; 
                modal.classList.remove('opacity-0');
                modalInner.classList.remove('scale-95');
                modalInner.classList.add('scale-100');
            }
        });

        // Close Modal Function
        function closeConfirmationModal() {
            modal.classList.add('opacity-0');
            modalInner.classList.remove('scale-100');
            modalInner.classList.add('scale-95');
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 300);
        }

        document.getElementById('closeModalBtn').addEventListener('click', closeConfirmationModal);
        document.getElementById('cancelSubmitBtn').addEventListener('click', closeConfirmationModal);

        // Final Confirm Submit Button
        document.getElementById('confirmSubmitBtn').addEventListener('click', function() {
            isConfirmed = true; 
            
            // Visual feedback on the modal's confirm button
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting...';
            this.classList.add('opacity-75', 'cursor-not-allowed');
            this.style.pointerEvents = "none";
            document.getElementById('cancelSubmitBtn').style.pointerEvents = "none";
            document.getElementById('closeModalBtn').style.pointerEvents = "none";
            
            // Visual feedback on the main form button behind the modal
            const mainBtn = document.getElementById('submitBtn');
            mainBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Processing Uploads...';
            mainBtn.classList.add('opacity-75', 'cursor-not-allowed');
            
            // Programmatically submit the form to bypass the event listener
            form.submit(); 
        });

        // --- 3. Toast Notification System ---
        function showToast(message, type = "success") {
            const container = document.getElementById('toast-container');
            const toast = document.createElement('div');
            
            const bgColor = type === 'success' ? 'bg-emerald-600' : 'bg-red-600';
            const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';

            toast.className = `toast-enter flex items-center gap-3 text-white px-6 py-4 rounded-xl shadow-2xl pointer-events-auto ${bgColor}`;
            toast.innerHTML = `<i class="fas ${icon} text-xl"></i><span class="font-semibold tracking-wide">${message}</span>`;
            
            container.appendChild(toast);

            requestAnimationFrame(() => toast.classList.add('toast-enter-active'));

            setTimeout(() => {
                toast.classList.remove('toast-enter-active');
                toast.classList.add('toast-exit');
                setTimeout(() => toast.remove(), 400); 
            }, 3000);
        }

        // --- Trigger Toasts from PHP responses ---
        <?php if (!empty($success_msg)): ?>
            showToast("<?= addslashes($success_msg) ?>", "success");
            setTimeout(() => {
                // CHANGED window.parent.location.href to window.top.location.href
                window.top.location.href = 'dashboard.php';
            }, 2000);
        <?php endif; ?>

        <?php if (!empty($error_msg)): ?>
            showToast("<?= addslashes($error_msg) ?>", "error");
        <?php endif; ?>
    </script>
</body>
</html>