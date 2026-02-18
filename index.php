<?php
// --- 1. PREVENT CACHING (Clear page on refresh) ---
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

require_once 'db.php';

// Create user_client table if it doesn't exist
$tableQuery = "CREATE TABLE IF NOT EXISTS `user_client` (
  `client_id` int(11) NOT NULL AUTO_INCREMENT,
  `firstname` varchar(50) NOT NULL,
  `mid_name` varchar(50) NOT NULL,
  `lastname` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` text NOT NULL,
  `mobilenum` text NOT NULL,
  `comp_id_upload` longtext NOT NULL,
  `govt_id_upload` longtext NOT NULL,
  `auth_letter` longtext NOT NULL,
  `password_unhashed` text NOT NULL,
  `Status` int(11) NOT NULL DEFAULT 0,
  `province` text NOT NULL,
  `citymun` text NOT NULL,
  `brgy` text NOT NULL,
  `zips` text NOT NULL,
  PRIMARY KEY (`client_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";

try {
    $pdo->exec($tableQuery);
} catch (PDOException $e) {
    die("DB Setup Error: " . $e->getMessage());
}

// Handle Contact Form Submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_contact'])) {
    // Set header to return JSON
    header('Content-Type: application/json');
    
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $subject = htmlspecialchars($_POST['subject']);
    $message = htmlspecialchars($_POST['message']);

    $response = [];

    if (!empty($name) && !empty($email) && !empty($message)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)");
            if ($stmt->execute([$name, $email, $subject, $message])) {
                $response = [
                    'status' => 'success', 
                    'message' => 'Message sent successfully! We will get back to you shortly.'
                ];
            } else {
                $response = [
                    'status' => 'error', 
                    'message' => 'Failed to save message to database.'
                ];
            }
        } catch (PDOException $e) {
            $response = [
                'status' => 'error', 
                'message' => 'Database error: ' . $e->getMessage()
            ];
        }
    } else {
        $response = [
            'status' => 'error', 
            'message' => 'Please fill out all required fields.'
        ];
    }

    // Output JSON and STOP execution to prevent HTML rendering
    echo json_encode($response);
    exit; 
}

// Fetch Requirements from Database
$req_stmt = $pdo->query("SELECT * FROM requirements ORDER BY sequence ASC");
$requirements = $req_stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">

    <title>O-LDPMS | Online Lumber Dealer Permitting & Monitoring System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="icon" href="logo/denr_logo.png" type="image/x-icon">
    
    <meta name="theme-color" content="#064e3b">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;800&display=swap');
        body { font-family: 'Inter', sans-serif; }
        .bg-denr { background-color: #064e3b; }
        .text-denr { color: #064e3b; }
        .hero-pattern {
            background-image: linear-gradient(rgba(6, 78, 59, 0.9), rgba(6, 78, 59, 0.8)), url('https://images.unsplash.com/photo-1589939705384-5185137a7f0f?auto=format&fit=crop&q=80&w=2000');
            background-size: cover;
            background-position: center;
        }

        /* --- 3. LOADING SPINNER CSS --- */
        .loader {
            border: 2px solid #f3f3f3;
            border-top: 2px solid currentColor;
            border-radius: 50%;
            width: 1rem;
            height: 1rem;
            animation: spin 1s linear infinite;
            display: inline-block;
            margin-right: 8px;
            vertical-align: middle;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .cursor-not-allowed {
            pointer-events: none;
        }

        /* --- TOAST ANIMATIONS --- */
        .toast-enter {
            transform: translateX(100%);
            opacity: 0;
        }
        .toast-enter-active {
            transform: translateX(0);
            opacity: 1;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .toast-exit {
            transform: translateX(100%);
            opacity: 0;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }
    </style>
</head>
<body class="bg-slate-50 relative">

    <div id="toast-container" class="fixed top-5 right-5 z-[100] flex flex-col gap-3 pointer-events-none"></div>

    <nav class="sticky top-0 z-50 bg-white shadow-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20 items-center">
                <div class="flex items-center gap-3">
                    <img src="logo/denr_logo.png" alt="DENR Logo" class="h-12 w-12">
                    <div>
                        <span class="text-2xl font-black text-emerald-900 tracking-tighter block leading-none">O-LDPMS</span>
                        <span class="text-[10px] uppercase font-bold text-emerald-700 tracking-widest">DENR CARAGA REGION</span>
                    </div>
                </div>
                <div class="hidden lg:flex items-center space-x-8 font-semibold text-gray-600">
                    <a href="#home" class="hover:text-emerald-700 transition">HOME</a>
                    <a href="#about" class="hover:text-emerald-700 transition">ABOUT</a>
                    <a href="#requirements" class="hover:text-emerald-700 transition">REQUIREMENTS</a>
                    <a href="#contact" class="hover:text-emerald-700 transition">CONTACT US</a>
                </div>
                <div class="flex items-center gap-4">
                    <button onclick="toggleModal('loginModal')" class="text-sm font-bold text-emerald-900 hover:bg-emerald-50 px-4 py-2 rounded-lg transition">LOGIN</button>
                    <button onclick="toggleModal('registerModal')" class="bg-emerald-700 text-white text-sm font-bold px-6 py-2.5 rounded-lg hover:bg-emerald-800 shadow-md transition">REGISTER</button>
                </div>
            </div>
        </div>
    </nav>

    <header id="home" class="hero-pattern text-white py-24 px-6">
        <div class="max-w-5xl mx-auto text-center">
            <h1 class="text-4xl md:text-6xl font-extrabold mb-6 leading-tight">
                Online Lumber Dealer Permitting & <span class="text-emerald-400">Monitoring System</span>
            </h1>
            <p class="text-lg md:text-xl text-emerald-50/90 mb-10 max-w-3xl mx-auto leading-relaxed">
                Streamlining the registration and monitoring of lumber dealers across the CARAGA region for sustainable forest management.
            </p>
            <div class="flex flex-col sm:flex-row justify-center gap-4">
                <a href="#requirements" class="bg-white text-emerald-900 px-8 py-4 rounded-xl font-bold hover:bg-emerald-50 transition shadow-lg">View Requirements</a>
                <a href="#about" class="bg-emerald-600/30 backdrop-blur-md border border-emerald-400/50 px-8 py-4 rounded-xl font-bold hover:bg-emerald-600/50 transition">Learn More</a>
            </div>
        </div>
    </header>

    <section id="about" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-6">
            <div class="grid lg:grid-cols-2 gap-16 items-center">
                <div>
                    <h2 class="text-sm font-bold text-emerald-600 tracking-widest uppercase mb-2">Our Objectives</h2>
                    <h3 class="text-4xl font-extrabold text-gray-900 mb-8 leading-tight">Standardizing the Future of Lumber Management</h3>
                    <ul class="space-y-4">
                        <li class="flex items-start gap-4 p-4 rounded-xl bg-slate-50 border-l-4 border-emerald-600 shadow-sm">
                            <i class="fas fa-check-circle text-emerald-600 mt-1"></i>
                            <p class="text-gray-700 font-medium leading-relaxed">Standardize and streamline the process flow regionwide.</p>
                        </li>
                        <li class="flex items-start gap-4 p-4 rounded-xl bg-slate-50 border-l-4 border-emerald-600 shadow-sm">
                            <i class="fas fa-tree text-emerald-600 mt-1"></i>
                            <p class="text-gray-700 font-medium leading-relaxed">Strengthen the protection and conservation of naturally grown timber.</p>
                        </li>
                        <li class="flex items-start gap-4 p-4 rounded-xl bg-slate-50 border-l-4 border-emerald-600 shadow-sm">
                            <i class="fas fa-bolt text-emerald-600 mt-1"></i>
                            <p class="text-gray-700 font-medium leading-relaxed">Improve access to service delivery and efficient processing.</p>
                        </li>
                        <li class="flex items-start gap-4 p-4 rounded-xl bg-slate-50 border-l-4 border-emerald-600 shadow-sm">
                            <i class="fas fa-chart-line text-emerald-600 mt-1"></i>
                            <p class="text-gray-700 font-medium leading-relaxed">Update statistics of log/lumber supply contracts and wood volume analysis.</p>
                        </li>
                    </ul>
                </div>
                <div class="bg-emerald-900 p-10 rounded-3xl text-white shadow-2xl relative overflow-hidden">
                    <div class="relative z-10">
                        <i class="fas fa-quote-left text-5xl text-emerald-500 mb-6 opacity-50"></i>
                        <h4 class="text-2xl font-bold mb-6">DENR MANDATE</h4>
                        <p class="text-emerald-100/90 italic leading-loose text-lg">
                            "The DENR shall be the primary government agency responsible for the conservation, management, development, and proper use of the country’s environment and natural resources... in order to ensure equitable sharing of the benefits derived therefrom for the welfare of the present and future generations of Filipinos."
                        </p>
                    </div>
                    <div class="absolute -bottom-10 -right-10 w-40 h-40 bg-emerald-800 rounded-full blur-3xl opacity-50"></div>
                </div>
            </div>
        </div>
    </section>

    <section id="requirements" class="py-20 bg-slate-100">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-extrabold text-gray-900 mb-4">List of Requirements</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">Please ensure all documents are scanned and uploaded in <span class="text-red-600 font-bold">PDF Format</span>.</p>
            </div>
            
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-200">
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-gray-800 text-white uppercase text-xs tracking-wider">
                                <th class="px-8 py-5 w-16">#</th>
                                <th class="px-8 py-5">Requirement</th>
                                <th class="px-8 py-5">New Application</th>
                                <th class="px-8 py-5">Renewal Application</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-gray-800">
                            <?php 
                            $counter = 1; // Initialize the counter
                            if(!empty($requirements)):
                                foreach ($requirements as $req): 
                            ?>
                            <tr class="hover:bg-slate-50 transition">
                                <td class="px-8 py-4 font-bold text-gray-500">
                                    <?= $counter++ ?>
                                </td>
                                
                                <td class="px-8 py-4">
                                    <div class="flex items-center justify-between">
                                        <span class="font-medium text-gray-900"><?= htmlspecialchars($req['requirement_name']) ?></span>
                                        <?php if (!empty($req['download_link'])): ?>
                                        <a href="<?= htmlspecialchars($req['download_link']) ?>" download class="ml-4 inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white px-3 py-1.5 rounded-lg text-sm font-semibold transition">
                                            <i class="fas fa-download"></i> Download
                                        </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="px-8 py-4">
                                    <?php if (strtolower($req['new_app_status']) == 'not required'): ?>
                                        <i class="fas fa-times text-red-500 text-xl ml-1"></i>
                                    <?php else: ?>
                                        <i class="fas fa-check text-indigo-500 mr-2 text-lg"></i> <?= htmlspecialchars($req['new_app_status']) ?>
                                    <?php endif; ?>
                                </td>
                                <td class="px-8 py-4">
                                    <?php if (strtolower($req['renewal_app_status']) == 'not required'): ?>
                                        <i class="fas fa-times text-red-500 text-xl ml-1"></i>
                                    <?php else: ?>
                                        <i class="fas fa-check text-indigo-500 mr-2 text-lg"></i> <?= htmlspecialchars($req['renewal_app_status']) ?>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php 
                                endforeach; 
                            else:
                            ?>
                            <tr>
                                <td colspan="4" class="px-8 py-4 text-center text-gray-500">No requirements loaded from the database yet.</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>

    <section id="contact" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-6 grid md:grid-cols-2 gap-12">
            <div>
                <h3 class="text-3xl font-bold mb-6">Contact Us</h3>
                
                <?= $contact_msg ?>

            <!-- Locate your contact form in index.php and update the ID -->
            <form id="contactForm" method="POST" action="index.php" class="space-y-4">
                <input type="hidden" name="submit_contact" value="1"> <!-- Hidden field to trigger PHP -->
                <input type="text" name="name" required placeholder="Your Name *" class="w-full p-4 border border-gray-200 rounded-xl outline-none focus:ring-2 focus:ring-emerald-500 transition">
                <input type="email" name="email" required placeholder="Your Email address *" class="w-full p-4 border border-gray-200 rounded-xl outline-none focus:ring-2 focus:ring-emerald-500 transition">
                <input type="text" name="subject" placeholder="Subject" class="w-full p-4 border border-gray-200 rounded-xl outline-none focus:ring-2 focus:ring-emerald-500 transition">
                <textarea name="message" required rows="4" placeholder="Message *" class="w-full p-4 border border-gray-200 rounded-xl outline-none focus:ring-2 focus:ring-emerald-500 transition"></textarea>
                <button type="submit" class="w-full md:w-auto bg-blue-600 text-white font-bold px-10 py-4 rounded-xl hover:bg-blue-700 shadow-lg transition">Send Message</button>
            </form>


            </div>
            <div class="flex flex-col justify-center">
                <div class="bg-slate-50 p-8 rounded-3xl border border-slate-100">
                    <h4 class="text-2xl font-bold mb-6 text-emerald-900">DENR CARAGA Region</h4>
                    <div class="space-y-6">
                        <div class="flex gap-4 items-start">
                            <div class="bg-emerald-100 p-3 rounded-lg text-emerald-700"><i class="fas fa-map-marker-alt"></i></div>
                            <p class="text-gray-600 leading-relaxed">Ambago, Butuan City, Philippines, 8600</p>
                        </div>
                        <div class="flex gap-4 items-start">
                            <div class="bg-emerald-100 p-3 rounded-lg text-emerald-700"><i class="fas fa-envelope"></i></div>
                            <p class="text-gray-600">fuscaraga@yahoo.com</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer class="bg-emerald-950 text-emerald-50 py-12 px-6">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row justify-between items-center gap-8">
            <div class="text-center md:text-left">
                <p class="font-bold text-lg">Department of Environment and Natural Resources</p>
                <p class="text-emerald-400 font-semibold mb-2">RICTU CARAGA Region</p>
                <p class="text-xs text-emerald-500">© Copyright 2022 - 2026. All Rights Reserved.</p>
            </div>
            <div class="flex gap-6 text-2xl">
                <a href="#" class="hover:text-white"><i class="fab fa-facebook"></i></a>
                <a href="#" class="hover:text-white"><i class="fab fa-twitter"></i></a>
                <a href="#" class="hover:text-white"><i class="fas fa-globe"></i></a>
            </div>
        </div>
    </footer>

    <div id="loginModal" class="fixed inset-0 z-[60] hidden bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden transition-all transform">
            <div class="bg-emerald-900 p-6 text-white flex justify-between items-center">
                <div>
                    <h3 class="text-xl font-bold">Account Login</h3>
                    <p class="text-emerald-300 text-xs uppercase tracking-widest font-semibold mt-1">O-LDPMS Portal</p>
                </div>
                <button onclick="toggleModal('loginModal')" class="h-10 w-10 rounded-full hover:bg-emerald-800 transition flex items-center justify-center">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>
            <form action="login_auth.php" method="POST" class="p-8 space-y-6">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Email Address</label>
                    <div class="relative">
                        <i class="fas fa-envelope absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        <input type="email" name="email" required 
                            class="w-full pl-12 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition" 
                            placeholder="name@email.com">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Password</label>
                    <div class="relative">
                        <i class="fas fa-lock absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        <input type="password" name="password" required 
                            class="w-full pl-12 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition" 
                            placeholder="••••••••">
                    </div>
                </div>
                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" class="rounded text-emerald-600 focus:ring-emerald-500">
                        <span class="text-sm text-gray-600">Remember me</span>
                    </label>
                    <div class="flex items-center justify-between mt-4">
                        <a href="forgot_password.php" class="text-sm text-blue-500 hover:text-blue-800">Forgot Password?</a>
                    </div>                </div>
                <button type="submit" class="w-full bg-emerald-700 text-white font-bold py-4 rounded-xl hover:bg-emerald-800 shadow-lg shadow-emerald-900/20 transition-all active:scale-[0.98]">
                    Sign In
                </button>
                <p class="text-center text-gray-500 text-sm">
                    Don't have an account? 
                    <a href="#" onclick="toggleModal('loginModal'); toggleModal('registerModal');" class="text-emerald-700 font-bold hover:underline">Register Here</a>
                </p>
            </form>
        </div>
    </div>

    <div id="registerModal" class="fixed inset-0 z-[60] hidden bg-black/60 backdrop-blur-sm flex items-center justify-center p-4 overflow-y-auto">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl my-8 relative transition-all transform">
            <div class="bg-emerald-900 p-6 text-white flex justify-between items-center rounded-t-2xl sticky top-0 z-10">
                <div>
                    <h3 class="text-xl font-bold">Client Registration</h3>
                    <p class="text-emerald-300 text-xs uppercase tracking-widest font-semibold mt-1">O-LDPMS Portal</p>
                </div>
                <button onclick="confirmCloseRegister()" class="h-10 w-10 rounded-full hover:bg-emerald-800 transition flex items-center justify-center">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>
            
            <?php include 'register.php'; ?>
            
        </div>
    </div>
<script>
    /**
     * MODAL MANAGEMENT
     */
    function toggleModal(id) {
        const modal = document.getElementById(id);
        if (modal.classList.contains('hidden')) {
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden'; // Prevent scroll
        } else {
            modal.classList.add('hidden');
            document.body.style.overflow = 'auto'; // Enable scroll
        }
    }

    function confirmCloseRegister() {
        if (confirm("Are you sure you want to cancel your registration? All entered data will be lost.")) {
            toggleModal('registerModal');
        }
    }

    // Close login modal when clicking outside the box
    window.onclick = function(event) {
        const loginModal = document.getElementById('loginModal');
        if (event.target == loginModal) {
            toggleModal('loginModal');
        }
    }

    /**
     * TOAST NOTIFICATION SYSTEM
     */
    function showToast(message, type = "success") {
        const container = document.getElementById('toast-container');
        const toast = document.createElement('div');
        
        const bgColor = type === 'success' ? 'bg-emerald-600' : (type === 'error' ? 'bg-red-600' : 'bg-yellow-600');
        const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';

        toast.className = `toast-enter flex items-center gap-3 text-white px-6 py-4 rounded-xl shadow-xl pointer-events-auto ${bgColor}`;
        toast.innerHTML = `<i class="fas ${icon} text-xl"></i><span class="font-semibold tracking-wide">${message}</span>`;
        
        container.appendChild(toast);

        // Trigger animation in
        requestAnimationFrame(() => {
            toast.classList.add('toast-enter-active');
        });

        // Trigger animation out and remove
        setTimeout(() => {
            toast.classList.remove('toast-enter-active');
            toast.classList.add('toast-exit');
            setTimeout(() => toast.remove(), 400); 
        }, 3000);
    }

    /**
     * FORM INTERCEPTION & AJAX SUBMISSION
     */
    document.addEventListener('DOMContentLoaded', () => {
        const forms = document.querySelectorAll('form');
        
        forms.forEach(form => {
            form.addEventListener('submit', function(e) {
                if (!this.checkValidity()) return;

                const submitBtn = this.querySelector('button[type="submit"]');
                const originalBtnText = submitBtn ? submitBtn.innerHTML : 'Submit';

                // --- 1. HANDLE CONTACT FORM (AJAX) ---
                if (this.id === 'contactForm' || this.getAttribute('action')?.includes('#contact')) {
                    e.preventDefault();
                    
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.classList.add('opacity-75', 'cursor-not-allowed');
                        submitBtn.innerHTML = `<span class="loader"></span> Sending...`;
                    }

                    const formData = new FormData(this);
                    // Append the submit button name manually for PHP isset()
                    formData.append('submit_contact', '1');

                    fetch('index.php', {
                        method: 'POST',
                        body: formData,
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            showToast(data.message, "success");
                            this.reset();
                        } else {
                            showToast(data.message, "error");
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showToast("Failed to send message. Please try again.", "error");
                    })
                    .finally(() => {
                        if (submitBtn) {
                            submitBtn.disabled = false;
                            submitBtn.classList.remove('opacity-75', 'cursor-not-allowed');
                            submitBtn.innerHTML = originalBtnText;
                        }
                    });
                    
                    return;
                }

                // --- 2. HANDLE REGISTRATION FORM (AJAX) ---
                if (this.closest('#registerModal')) {
                    e.preventDefault(); 
                    
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.classList.add('opacity-75', 'cursor-not-allowed');
                        submitBtn.innerHTML = `<span class="loader"></span> Processing...`;
                    }

                    const formData = new FormData(this);
                    const actionUrl = this.getAttribute('action') || window.location.href;

                    fetch(actionUrl, {
                        method: 'POST',
                        body: formData,
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    })
                    .then(response => response.text())
                    .then(html => {
                        // Close SweetAlert if register.php uses it
                        if (typeof Swal !== 'undefined') Swal.close();

                        if (html.includes('Registration Successful')) {
                            showToast("Registration successful! Check your email for verification.", "success");
                            this.reset();
                            setTimeout(() => {
                                toggleModal('registerModal');
                            }, 3000);
                        } else if (html.includes('Email already registered')) {
                            showToast("Email already registered. Please use a different email.", "error");
                        } else {
                            showToast("Registration failed. Please check your inputs.", "error");
                        }
                    })
                    .catch(error => {
                        showToast("Network error. Please try again.", "error");
                    })
                    .finally(() => {
                        if (submitBtn) {
                            submitBtn.disabled = false;
                            submitBtn.classList.remove('opacity-75', 'cursor-not-allowed');
                            submitBtn.innerHTML = originalBtnText;
                        }
                    });

                    return;
                }

                // --- 3. DEFAULT BEHAVIOR (LOGIN FORM) ---
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.classList.add('opacity-75', 'cursor-not-allowed');
                    submitBtn.innerHTML = `<span class="loader"></span> Processing...`;
                }
            });
        });
    });
</script>

</body>
</html>