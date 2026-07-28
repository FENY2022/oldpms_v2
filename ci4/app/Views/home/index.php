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
    <link rel="icon" href="<?= base_url('logo/denr_logo.png') ?>" type="image/x-icon">
    
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
                    <img src="<?= base_url('logo/denr_logo.png') ?>" alt="DENR Logo" class="h-12 w-12">
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
                            "The DENR shall be the primary government agency responsible for the conservation, management, development, and proper use of the country's environment and natural resources... in order to ensure equitable sharing of the benefits derived therefrom for the welfare of the present and future generations of Filipinos."
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
                            $counter = 1;
                            if(!empty($requirements)):
                                foreach ($requirements as $req): 
                            ?>
                            <tr class="hover:bg-slate-50 transition">
                                <td class="px-8 py-4 font-bold text-gray-500">
                                    <?= $counter++ ?>
                                </td>
                                
                                <td class="px-8 py-4">
                                    <div class="flex items-center justify-between">
                                        <span class="font-medium text-gray-900"><?= esc($req['requirement_name']) ?></span>
                                        <?php if (!empty($req['download_link'])): ?>
                                        <a href="<?= esc($req['download_link']) ?>" download class="ml-4 inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white px-3 py-1.5 rounded-lg text-sm font-semibold transition">
                                            <i class="fas fa-download"></i> Download
                                        </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="px-8 py-4">
                                    <?php if (strtolower($req['new_app_status']) == 'not required'): ?>
                                        <i class="fas fa-times text-red-500 text-xl ml-1"></i>
                                    <?php else: ?>
                                        <i class="fas fa-check text-indigo-500 mr-2 text-lg"></i> <?= esc($req['new_app_status']) ?>
                                    <?php endif; ?>
                                </td>
                                <td class="px-8 py-4">
                                    <?php if (strtolower($req['renewal_app_status']) == 'not required'): ?>
                                        <i class="fas fa-times text-red-500 text-xl ml-1"></i>
                                    <?php else: ?>
                                        <i class="fas fa-check text-indigo-500 mr-2 text-lg"></i> <?= esc($req['renewal_app_status']) ?>
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

                <?php if (session()->getFlashdata('contact_error')): ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-xl mb-4">
                        <?= session()->getFlashdata('contact_error') ?>
                    </div>
                <?php endif; ?>
                <?php if (session()->getFlashdata('contact_success')): ?>
                    <div class="bg-emerald-100 border border-emerald-400 text-emerald-700 px-4 py-3 rounded-xl mb-4">
                        <?= session()->getFlashdata('contact_success') ?>
                    </div>
                <?php endif; ?>

            <form id="contactForm" method="POST" action="<?= base_url('/contact') ?>" class="space-y-4">
                <?= csrf_field() ?>
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
                <p class="text-xs text-emerald-500">&copy; Copyright 2022 - 2026. All Rights Reserved.</p>
            </div>
            <div class="flex gap-6 text-2xl">
                <a href="#" class="hover:text-white"><i class="fab fa-facebook"></i></a>
                <a href="#" class="hover:text-white"><i class="fab fa-twitter"></i></a>
                <a href="#" class="hover:text-white"><i class="fas fa-globe"></i></a>
            </div>
        </div>
    </footer>

    <!-- ==================== LOGIN MODAL ==================== -->
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
            <form id="loginForm" method="POST" action="<?= base_url('/login') ?>" class="p-8 space-y-6">
                <?= csrf_field() ?>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Email or Username</label>
                    <div class="relative">
                        <i class="fas fa-user absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        <input type="text" name="email" required 
                            class="w-full pl-12 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition" 
                            placeholder="Email Address or Username">
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
                        <a href="<?= base_url('/forgot-password') ?>" class="text-sm text-blue-500 hover:text-blue-800">Forgot Password?</a>
                    </div>                
                </div>
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

    <!-- ==================== REGISTER MODAL ==================== -->
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
            
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

            <form id="registerForm" action="<?= base_url('/register') ?>" method="POST" enctype="multipart/form-data" class="p-8 space-y-6 max-h-[75vh] overflow-y-auto">
                <?= csrf_field() ?>

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
                            <?php if (!empty($provinces)): ?>
                            <?php foreach($provinces as $prov): ?>
                                <option value="<?= esc($prov['prov_code']) ?>"><?= esc($prov['prov_name']) ?></option>
                            <?php endforeach; ?>
                            <?php endif; ?>
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
            document.body.style.overflow = 'hidden';
        } else {
            modal.classList.add('hidden');
            document.body.style.overflow = 'auto';
        }
    }

    function confirmCloseRegister() {
        if (confirm("Are you sure you want to cancel your registration? All entered data will be lost.")) {
            toggleModal('registerModal');
        }
    }

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

        requestAnimationFrame(() => {
            toast.classList.add('toast-enter-active');
        });

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

                // --- HANDLE CONTACT FORM (AJAX) ---
                if (this.id === 'contactForm') {
                    e.preventDefault();
                    
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.classList.add('opacity-75', 'cursor-not-allowed');
                        submitBtn.innerHTML = `<span class="loader"></span> Sending...`;
                    }

                    const formData = new FormData(this);

                    fetch('<?= base_url('/contact') ?>', {
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

                // --- HANDLE REGISTRATION FORM (AJAX) ---
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

                // --- HANDLE LOGIN FORM (AJAX) ---
                if (this.id === 'loginForm') {
                    e.preventDefault();

                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.classList.add('opacity-75', 'cursor-not-allowed');
                        submitBtn.innerHTML = `<span class="loader"></span> Authenticating...`;
                    }

                    const formData = new FormData(this);

                    fetch('<?= base_url('/login') ?>', {
                        method: 'POST',
                        body: formData,
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            showToast(data.message, "success");
                            setTimeout(() => {
                                window.location.href = data.redirect;
                            }, 1000);
                        } else {
                            showToast(data.message, "error");
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showToast("Authentication error. Please try again.", "error");
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
            });
        });
    });
</script>

<!-- ==================== REGISTER FORM JS ==================== -->
<script>
    // --- UI Logic: Address Cascading Dropdowns ---
    document.getElementById('prov_select').addEventListener('change', function() {
        const provCode = this.value;
        const munSelect = document.getElementById('mun_select');
        const brgySelect = document.getElementById('brgy_select');
        const zipInput = document.getElementById('zips_input');

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
        formData.append('prov_code', provCode);

        fetch('<?= base_url('/api/address/municipalities') ?>', { method: 'POST', body: formData })
            .then(response => response.json())
            .then(data => {
                data.forEach(mun => {
                    const option = document.createElement('option');
                    option.value = mun.mun_code;
                    option.textContent = mun.muncity_name;
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
        
        const selectedOption = this.options[this.selectedIndex];
        zipInput.value = selectedOption.getAttribute('data-zip') || '';

        if (!munCode) {
            brgySelect.disabled = true;
            return;
        }

        brgySelect.disabled = false;

        const formData = new FormData();
        formData.append('mun_code', munCode);

        fetch('<?= base_url('/api/address/barangays') ?>', { method: 'POST', body: formData })
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
            authInput.value = '';
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

    // --- Register Form Submission Logic ---
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

</body>
</html>
