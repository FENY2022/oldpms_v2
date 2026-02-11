<?php
// Handle User Registration Submission
$register_msg = "";
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_register'])) {
    
    // Create an uploads directory if it doesn't exist
    $upload_dir = 'uploads/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    // Sanitize Inputs
    $fname = htmlspecialchars($_POST['firstname']);
    $mname = htmlspecialchars($_POST['mid_name']);
    $lname = htmlspecialchars($_POST['lastname']);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $unhashed_password = $_POST['password']; 
    $hashed_password = password_hash($unhashed_password, PASSWORD_DEFAULT);
    $mobile = htmlspecialchars($_POST['mobilenum']);
    $province = htmlspecialchars($_POST['province']);
    $citymun = htmlspecialchars($_POST['citymun']);
    $brgy = htmlspecialchars($_POST['brgy']);
    $zips = htmlspecialchars($_POST['zips']);
    $status = 0; // Default status (e.g., Pending/Unverified)

    // Generate a secure verification token
    $verification_token = bin2hex(random_bytes(32));

    // Handle File Uploads
    function uploadFile($input_name, $dir) {
        if (isset($_FILES[$input_name]) && $_FILES[$input_name]['error'] == 0) {
            $filename = time() . '_' . basename($_FILES[$input_name]['name']);
            $target_file = $dir . $filename;
            if (move_uploaded_file($_FILES[$input_name]['tmp_name'], $target_file)) {
                return $target_file;
            }
        }
        return "";
    }

    $comp_id_path = uploadFile('comp_id_upload', $upload_dir);
    $govt_id_path = uploadFile('govt_id_upload', $upload_dir);
    $auth_letter_path = uploadFile('auth_letter', $upload_dir);

    // Insert into Database (Updated with verification_token)
    $stmt = $pdo->prepare("INSERT INTO user_client 
        (firstname, mid_name, lastname, email, verification_token, password, mobilenum, comp_id_upload, govt_id_upload, auth_letter, password_unhashed, Status, province, citymun, brgy, zips) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    try {
        if ($stmt->execute([$fname, $mname, $lname, $email, $verification_token, $hashed_password, $mobile, $comp_id_path, $govt_id_path, $auth_letter_path, $unhashed_password, $status, $province, $citymun, $brgy, $zips])) {
            
            // ==========================================
            // EMAIL TRIGGER LOGIC
            // ==========================================
            
            // 1. Build Base URL & Verification Link
            $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
            $domainName = $_SERVER['HTTP_HOST'];
            $base_dir = dirname($_SERVER['PHP_SELF']); 
            
            $verify_link = $protocol . $domainName . $base_dir . "/verify.php?token=" . $verification_token . "&email=" . urlencode($email);

            // 2. Prepare the email parameters
            $subject = "Verify Your Account";
            $message = "Hello $fname,\n\nYour registration was successful. Please click the link below to verify your email address:\n\n" . $verify_link . "\n\nThank you!";
            $yourname = "Admin";

            // 3. Construct the full GET request string URL-encoding the variables
            $email_url = $protocol . $domainName . $base_dir . "/sendemail/send.php" 
                       . "?send=1" 
                       . "&email=" . urlencode($email) 
                       . "&Subject=" . urlencode($subject) 
                       . "&message=" . urlencode($message) 
                       . "&yourname=" . urlencode($yourname);

            // 4. Silently trigger the script in the background
            @file_get_contents($email_url);
            
            // ==========================================
            // TOAST NOTIFICATION (SweetAlert2)
            // ==========================================
            $register_msg = "
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 5000,
                        timerProgressBar: true,
                    });
                    Toast.fire({
                        icon: 'success',
                        title: 'Registration Successful! Check your email for the verification link.'
                    });
                });
            </script>";

        } else {
            $register_msg = "
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({ toast: true, position: 'top-end', icon: 'error', title: 'Registration Failed. Please try again.', showConfirmButton: false, timer: 3000 });
                });
            </script>";
        }
    } catch (PDOException $e) {
        $register_msg = "
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({ toast: true, position: 'top-end', icon: 'error', title: 'Error: Email might already exist.', showConfirmButton: false, timer: 3000 });
            });
        </script>";
    }
}

// Output the Toast notification script if set
echo $register_msg; 
?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<form action="index.php" method="POST" enctype="multipart/form-data" class="p-8 space-y-6 max-h-[75vh] overflow-y-auto">
    
    <h4 class="font-bold text-gray-800 border-b pb-2">Personal Information</h4>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <label class="block text-xs font-bold text-gray-700 mb-1">First Name *</label>
            <input type="text" name="firstname" required class="w-full p-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 outline-none">
        </div>
        <div>
            <label class="block text-xs font-bold text-gray-700 mb-1">Middle Name</label>
            <input type="text" name="mid_name" class="w-full p-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 outline-none">
        </div>
        <div>
            <label class="block text-xs font-bold text-gray-700 mb-1">Last Name *</label>
            <input type="text" name="lastname" required class="w-full p-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 outline-none">
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-xs font-bold text-gray-700 mb-1">Email Address *</label>
            <input type="email" name="email" required class="w-full p-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 outline-none">
        </div>
        <div>
            <label class="block text-xs font-bold text-gray-700 mb-1">Mobile Number *</label>
            <input type="text" name="mobilenum" required class="w-full p-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 outline-none">
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-xs font-bold text-gray-700 mb-1">Password *</label>
            <input type="password" name="password" required class="w-full p-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 outline-none">
        </div>
    </div>

    <h4 class="font-bold text-gray-800 border-b pb-2 mt-6">Address Information</h4>
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
            <label class="block text-xs font-bold text-gray-700 mb-1">Province *</label>
            <input type="text" name="province" required class="w-full p-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 outline-none">
        </div>
        <div>
            <label class="block text-xs font-bold text-gray-700 mb-1">City/Municipality *</label>
            <input type="text" name="citymun" required class="w-full p-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 outline-none">
        </div>
        <div>
            <label class="block text-xs font-bold text-gray-700 mb-1">Barangay *</label>
            <input type="text" name="brgy" required class="w-full p-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 outline-none">
        </div>
        <div>
            <label class="block text-xs font-bold text-gray-700 mb-1">ZIP Code *</label>
            <input type="text" name="zips" required class="w-full p-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 outline-none">
        </div>
    </div>

    <h4 class="font-bold text-gray-800 border-b pb-2 mt-6">Required Documents (PDF/Images)</h4>
    <div class="space-y-4">
        <div>
            <label class="block text-xs font-bold text-gray-700 mb-1">Company ID Upload *</label>
            <input type="file" name="comp_id_upload" required class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100">
        </div>
        <div>
            <label class="block text-xs font-bold text-gray-700 mb-1">Government ID Upload *</label>
            <input type="file" name="govt_id_upload" required class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100">
        </div>
        <div>
            <label class="block text-xs font-bold text-gray-700 mb-1">Authorization Letter *</label>
            <input type="file" name="auth_letter" required class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100">
        </div>
    </div>

    <button type="submit" name="submit_register" class="w-full bg-emerald-700 text-white font-bold py-4 rounded-xl hover:bg-emerald-800 shadow-lg mt-6 transition-all">
        Complete Registration
    </button>
</form>