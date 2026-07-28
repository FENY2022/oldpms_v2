<?php
// Handle User Registration Submission
$register_msg = "";
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_register'])) {
    
    // Create an uploads directory if it doesn't exist
    $upload_dir = 'uploads/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    // Sanitize Inputs (Safely checking if keys exist to prevent undefined warnings)
    $fname = htmlspecialchars($_POST['firstname'] ?? '');
    $mname = htmlspecialchars($_POST['mid_name'] ?? '');
    $lname = htmlspecialchars($_POST['lastname'] ?? '');
    $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
    $unhashed_password = $_POST['password'] ?? ''; 
    $confirm_password = $_POST['confirm_password'] ?? '';
    $mobile = htmlspecialchars($_POST['mobilenum'] ?? '');
    
    // Address Inputs (Will now contain codes from the database)
    $province = htmlspecialchars($_POST['province'] ?? '');
    $citymun = htmlspecialchars($_POST['citymun'] ?? '');
    $brgy = htmlspecialchars($_POST['brgy'] ?? '');
    $zips = htmlspecialchars($_POST['zips'] ?? '');
    
    $status = 0; // Default status (e.g., Pending/Unverified)

    // Backend Password Strength Check Logic
    $score = 0;
    if (strlen($unhashed_password) >= 8) $score++;
    if (preg_match('@[a-z]@', $unhashed_password)) $score++;
    if (preg_match('@[A-Z]@', $unhashed_password)) $score++;
    if (preg_match('@[0-9]@', $unhashed_password)) $score++;
    if (preg_match('@[^\w]@', $unhashed_password)) $score++;

    // Backend Password Validation
    if ($unhashed_password !== $confirm_password) {
        $register_msg = "
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({ toast: true, position: 'top-end', icon: 'error', title: 'Passwords do not match.', showConfirmButton: false, timer: 3000 });
            });
        </script>";
    } elseif (strlen($unhashed_password) < 8) {
        $register_msg = "
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({ toast: true, position: 'top-end', icon: 'error', title: 'Password must be at least 8 characters.', showConfirmButton: false, timer: 3000 });
            });
        </script>";
    } elseif ($score < 3) { // Block weak passwords on the backend
        $register_msg = "
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({ toast: true, position: 'top-end', icon: 'error', title: 'Password is too weak. Please use a stronger password.', showConfirmButton: false, timer: 3000 });
            });
        </script>";
    } else {
        // Check if email already exists
        $stmt_check = $pdo->prepare("SELECT COUNT(*) FROM user_client WHERE email = ?");
        $stmt_check->execute([$email]);
        $email_count = $stmt_check->fetchColumn();

        if ($email_count > 0) {
            $register_msg = "
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({ toast: true, position: 'top-end', icon: 'error', title: 'Email already registered. Please use a different email.', showConfirmButton: false, timer: 3000 });
                });
            </script>";
        } else {
            $hashed_password = password_hash($unhashed_password, PASSWORD_DEFAULT);
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

            // Insert into Database
            $stmt = $pdo->prepare("INSERT INTO user_client 
                (firstname, mid_name, lastname, email, verification_token, password, mobilenum, comp_id_upload, govt_id_upload, auth_letter, password_unhashed, Status, province, citymun, brgy, zips) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            
            try {
                if ($stmt->execute([$fname, $mname, $lname, $email, $verification_token, $hashed_password, $mobile, $comp_id_path, $govt_id_path, $auth_letter_path, $unhashed_password, $status, $province, $citymun, $brgy, $zips])) {
                    
                    // EMAIL TRIGGER LOGIC
                    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
                    $domainName = $_SERVER['HTTP_HOST'];
                    $base_dir = dirname($_SERVER['PHP_SELF']); 
                    
                    $verify_link = $protocol . $domainName . $base_dir . "/verify.php?token=" . $verification_token . "&email=" . urlencode($email);

                    $subject = "Verify Your Account";
                    $message = "Hello $fname,\n\nYour registration was successful. Please click the link below to verify your email address:\n\n" . $verify_link . "\n\nThank you!";
                    $yourname = "Admin";

                    $email_url = $protocol . $domainName . $base_dir . "/sendemail/send.php" 
                               . "?send=1" 
                               . "&email=" . urlencode($email) 
                               . "&Subject=" . urlencode($subject) 
                               . "&message=" . urlencode($message) 
                               . "&yourname=" . urlencode($yourname);

                    @file_get_contents($email_url);
                    
                    $register_msg = "
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const Toast = Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 5000, timerProgressBar: true });
                            Toast.fire({ icon: 'success', title: 'Registration Successful! Check your email for the verification link.' });
                            document.getElementById('registerForm').reset();
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
    }
}

// Fetch Initial List of Provinces to populate the first dropdown
$prov_stmt = $pdo->query("SELECT prov_code, prov_name FROM province ORDER BY prov_name ASC");
$provinces = $prov_stmt->fetchAll(PDO::FETCH_ASSOC);

echo $register_msg; 
?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<form id="registerForm" action="index.php" method="POST" enctype="multipart/form-data" class="p-8 space-y-6 max-h-[75vh] overflow-y-auto">
    <input type="hidden" name="submit_register" value="1">

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
            <input
            type="tel"
            name="mobilenum"
            required
            maxlength="11"
            pattern="09[0-9]{9}"
            placeholder="09XXXXXXXXX"
            class="w-full p-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 outline-none"
            oninput="this.value = this.value.replace(/[^0-9]/g, '')"
            >

        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-xs font-bold text-gray-700 mb-1">Password *</label>
            <input type="password" id="reg_password" name="password" required class="w-full p-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 outline-none">
            
            <div class="flex items-center justify-between mt-1">
                <p class="text-[10px] text-gray-500">Must be at least 8 characters long.</p>
                <span id="strength_text" class="text-[10px] font-bold hidden"></span>
            </div>
            <div id="strength_container" class="w-full bg-gray-200 rounded-full h-1 mt-1 hidden">
                <div id="strength_bar" class="h-1 rounded-full w-0 transition-all duration-300"></div>
            </div>

        </div>
        <div>
            <label class="block text-xs font-bold text-gray-700 mb-1">Confirm Password *</label>
            <input type="password" id="reg_confirm_password" name="confirm_password" required class="w-full p-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 outline-none">
            <p id="password_match_msg" class="text-[10px] mt-1 hidden"></p>
        </div>
    </div>

    <h4 class="font-bold text-gray-800 border-b pb-2 mt-6">Address Information</h4>
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
            <label class="block text-xs font-bold text-gray-700 mb-1">Province *</label>
            <select id="prov_select" name="province" required class="w-full p-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 outline-none bg-white">
                <option value="" disabled selected>Select Province</option>
                <?php foreach($provinces as $prov): ?>
                    <option value="<?= htmlspecialchars($prov['prov_code']) ?>"><?= htmlspecialchars($prov['prov_name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label class="block text-xs font-bold text-gray-700 mb-1">City/Municipality *</label>
            <select id="mun_select" name="citymun" required disabled class="w-full p-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 outline-none disabled:bg-gray-100 disabled:cursor-not-allowed transition bg-white">
                <option value="" disabled selected>Select City/Mun</option>
            </select>
        </div>
        <div>
            <label class="block text-xs font-bold text-gray-700 mb-1">Barangay *</label>
            <select id="brgy_select" name="brgy" required disabled class="w-full p-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 outline-none disabled:bg-gray-100 disabled:cursor-not-allowed transition bg-white">
                <option value="" disabled selected>Select Barangay</option>
            </select>
        </div>
        <div>
            <label class="block text-xs font-bold text-gray-700 mb-1">ZIP Code *</label>
            <input type="text" id="zips_input" name="zips" required readonly placeholder="Auto-fills" class="w-full p-3 border border-gray-200 rounded-xl bg-gray-100 outline-none cursor-not-allowed text-gray-600 font-semibold">
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
        
        <div class="border border-gray-200 p-4 rounded-xl bg-gray-50">
            <div class="flex items-center justify-between">
                <div>
                    <h5 class="text-sm font-bold text-gray-800">Applying as a Representative?</h5>
                    <p class="text-xs text-gray-500">Enable this to upload an Authorization Letter.</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" id="auth_switch" class="sr-only peer">
                    <div class="w-11 h-6 bg-gray-300 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
                </label>
            </div>
            
            <div id="auth_letter_container" class="hidden mt-4 pt-4 border-t border-gray-200 transition-all">
                <label class="block text-xs font-bold text-gray-700 mb-1">Authorization Letter *</label>
                <input type="file" id="auth_letter_input" name="auth_letter" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100">
            </div>
        </div>
    </div>

    <button type="submit" id="submitRegisterBtn" class="w-full bg-emerald-700 text-white font-bold py-4 rounded-xl hover:bg-emerald-800 shadow-lg mt-6 transition-all flex items-center justify-center gap-2">
        Complete Registration
    </button>
</form>

<script>
    // --- UI Logic: Address Cascading Dropdowns ---
    document.getElementById('prov_select').addEventListener('change', function() {
        const provCode = this.value;
        const munSelect = document.getElementById('mun_select');
        const brgySelect = document.getElementById('brgy_select');
        const zipInput = document.getElementById('zips_input');

        // Reset lower fields
        munSelect.innerHTML = '<option value="" disabled selected>Select City/Mun</option>';
        brgySelect.innerHTML = '<option value="" disabled selected>Select Barangay</option>';
        zipInput.value = '';
        
        if (!provCode) {
            munSelect.disabled = true;
            brgySelect.disabled = true;
            return;
        }

        munSelect.disabled = false;
        brgySelect.disabled = true;

        const formData = new FormData();
        formData.append('action', 'get_mun');
        formData.append('prov_code', provCode);

        fetch('ajax_address.php', { method: 'POST', body: formData })
            .then(response => response.json())
            .then(data => {
                data.forEach(mun => {
                    const option = document.createElement('option');
                    option.value = mun.mun_code;
                    option.textContent = mun.muncity_name;
                    // Store ZIP code inside the option element securely
                    option.setAttribute('data-zip', mun.zip_code);
                    munSelect.appendChild(option);
                });
            });
    });

    document.getElementById('mun_select').addEventListener('change', function() {
        const munCode = this.value;
        const brgySelect = document.getElementById('brgy_select');
        const zipInput = document.getElementById('zips_input');

        brgySelect.innerHTML = '<option value="" disabled selected>Select Barangay</option>';
        
        // Auto-fill the ZIP code
        const selectedOption = this.options[this.selectedIndex];
        zipInput.value = selectedOption.getAttribute('data-zip') || '';

        if (!munCode) {
            brgySelect.disabled = true;
            return;
        }

        brgySelect.disabled = false;

        const formData = new FormData();
        formData.append('action', 'get_brgy');
        formData.append('mun_code', munCode);

        fetch('ajax_address.php', { method: 'POST', body: formData })
            .then(response => response.json())
            .then(data => {
                data.forEach(brgy => {
                    const option = document.createElement('option');
                    option.value = brgy.brgy_code;
                    option.textContent = brgy.brgy_name;
                    brgySelect.appendChild(option);
                });
            });
    });

    // --- UI Logic: Authorization Letter Toggle ---
    const authSwitch = document.getElementById('auth_switch');
    const authContainer = document.getElementById('auth_letter_container');
    const authInput = document.getElementById('auth_letter_input');

    authSwitch.addEventListener('change', function() {
        if (this.checked) {
            authContainer.classList.remove('hidden');
            authInput.setAttribute('required', 'required');
        } else {
            authContainer.classList.add('hidden');
            authInput.removeAttribute('required');
            authInput.value = ''; // Reset file input if turned off
        }
    });

    // --- UI Logic: Password Strength Checker ---
    const passInput = document.getElementById('reg_password');
    const confirmInput = document.getElementById('reg_confirm_password');
    const matchMsg = document.getElementById('password_match_msg');
    
    const strengthText = document.getElementById('strength_text');
    const strengthContainer = document.getElementById('strength_container');
    const strengthBar = document.getElementById('strength_bar');
    
    let isPasswordWeak = true; 

    passInput.addEventListener('input', function() {
        const val = passInput.value;
        let score = 0;

        if (val.length === 0) {
            strengthContainer.classList.add('hidden');
            strengthText.classList.add('hidden');
            isPasswordWeak = true;
            return;
        }

        strengthContainer.classList.remove('hidden');
        strengthText.classList.remove('hidden');

        if (val.length >= 8) score++;
        if (/[a-z]/.test(val)) score++;
        if (/[A-Z]/.test(val)) score++;
        if (/[0-9]/.test(val)) score++;
        if (/[^A-Za-z0-9]/.test(val)) score++;

        if (score < 3) {
            strengthText.textContent = 'Weak';
            strengthText.className = 'text-[10px] font-bold text-red-500';
            strengthBar.className = 'h-1 rounded-full transition-all duration-300 bg-red-500 w-1/3';
            isPasswordWeak = true;
        } else if (score >= 3 && score < 5) {
            strengthText.textContent = 'Medium';
            strengthText.className = 'text-[10px] font-bold text-yellow-500';
            strengthBar.className = 'h-1 rounded-full transition-all duration-300 bg-yellow-500 w-2/3';
            isPasswordWeak = false;
        } else if (score === 5) {
            strengthText.textContent = 'Strong';
            strengthText.className = 'text-[10px] font-bold text-emerald-500';
            strengthBar.className = 'h-1 rounded-full transition-all duration-300 bg-emerald-500 w-full';
            isPasswordWeak = false;
        }
        
        checkPasswords(); 
    });

    // --- UI Logic: Password Matching Visual Feedback ---
    function checkPasswords() {
        if (confirmInput.value.length > 0) {
            matchMsg.classList.remove('hidden');
            if (passInput.value === confirmInput.value) {
                matchMsg.textContent = 'Passwords match.';
                matchMsg.className = 'text-[10px] mt-1 text-emerald-600 font-bold';
            } else {
                matchMsg.textContent = 'Passwords do not match.';
                matchMsg.className = 'text-[10px] mt-1 text-red-500 font-bold';
            }
        } else {
            matchMsg.classList.add('hidden');
        }
    }
    confirmInput.addEventListener('input', checkPasswords);

    // --- Form Submission Logic ---
    document.getElementById('registerForm').addEventListener('submit', function(e) {
        
        const pass = passInput.value;
        const confirmPass = confirmInput.value;

        if (isPasswordWeak) {
            e.preventDefault();
            Swal.fire({ toast: true, position: 'top-end', icon: 'error', title: 'Password is too weak. Please use a stronger password.', showConfirmButton: false, timer: 3000 });
            return;
        }

        if (pass.length < 8) {
            e.preventDefault();
            Swal.fire({ toast: true, position: 'top-end', icon: 'error', title: 'Password must be at least 8 characters.', showConfirmButton: false, timer: 3000 });
            return;
        }

        if (pass !== confirmPass) {
            e.preventDefault();
            Swal.fire({ toast: true, position: 'top-end', icon: 'error', title: 'Passwords do not match.', showConfirmButton: false, timer: 3000 });
            return;
        }

        const submitBtn = document.getElementById('submitRegisterBtn');
        submitBtn.disabled = true;
        submitBtn.classList.add('opacity-75', 'cursor-not-allowed');
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing Registration...';

        Swal.fire({
            title: 'Uploading Documents...',
            html: 'Please do not close this window.',
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
    });
</script>