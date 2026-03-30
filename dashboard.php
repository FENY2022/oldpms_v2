<?php
// Start Session
session_start();

// Check if user is logged in, if not redirect to index
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: index.php");
    exit;
}

require_once 'db.php';

// Handle Logout
if (isset($_GET['action']) && $_GET['action'] == 'logout') {
    session_unset();
    session_destroy();
    header("Location: index.php");
    exit;
}

$firstName = htmlspecialchars($_SESSION['firstname']);
$lastName = htmlspecialchars($_SESSION['lastname']);
$email = htmlspecialchars($_SESSION['email']);

// Fetch Requirements from Database to use in the Modal
try {
    $req_stmt = $pdo->query("SELECT * FROM requirements ORDER BY sequence ASC");
    $requirements = $req_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $requirements = []; // Fallback if table doesn't exist yet
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | O-LDPMS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="icon" href="logo/denr_logo.png" type="image/x-icon">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;800&display=swap');
        body { font-family: 'Inter', sans-serif; }
        
        /* Modal Animations */
        .modal-enter { opacity: 0; transform: scale(0.95); }
        .modal-enter-active { opacity: 1; transform: scale(1); transition: all 0.2s ease-out; }
    </style>
</head>
<body class="bg-slate-50 flex h-screen overflow-hidden">

    <aside class="w-64 bg-emerald-900 text-white flex flex-col hidden md:flex shadow-2xl z-20">
        <div class="h-20 flex items-center gap-3 px-6 bg-emerald-950 border-b border-emerald-800">
            <img src="logo/denr_logo.png" alt="DENR Logo" class="h-10 w-10">
            <div>
                <span class="text-xl font-black tracking-tighter block leading-none">O-LDPMS</span>
                <span class="text-[9px] uppercase font-bold text-emerald-400 tracking-widest">CARAGA REGION</span>
            </div>
        </div>

        <nav class="flex-1 py-6 px-4 space-y-2 overflow-y-auto">
            <a href="#" onclick="showDashboard()" class="flex items-center gap-3 bg-emerald-800 text-white px-4 py-3 rounded-xl font-semibold transition nav-item active-nav">
                <i class="fas fa-home w-5 text-emerald-400"></i> Dashboard
            </a>
            
            <a href="#" onclick="loadIframe('my_applications.php', this)" class="flex items-center gap-3 text-emerald-100 hover:bg-emerald-800 hover:text-white px-4 py-3 rounded-xl font-medium transition nav-item">
                <i class="fas fa-file-alt w-5 text-emerald-400"></i> My Applications
            </a>
            
            <a href="#" class="flex items-center gap-3 text-emerald-100 hover:bg-emerald-800 hover:text-white px-4 py-3 rounded-xl font-medium transition nav-item">
                <i class="fas fa-folder-open w-5 text-emerald-400"></i> Documents
            </a>
            <a href="#" class="flex items-center gap-3 text-emerald-100 hover:bg-emerald-800 hover:text-white px-4 py-3 rounded-xl font-medium transition nav-item">
                <i class="fas fa-user-circle w-5 text-emerald-400"></i> Profile Settings
            </a>
        </nav>

        <div class="p-4 border-t border-emerald-800 bg-emerald-950">
            <a href="?action=logout" class="flex items-center gap-3 text-red-400 hover:bg-red-500 hover:text-white px-4 py-3 rounded-xl font-bold transition">
                <i class="fas fa-sign-out-alt w-5"></i> Logout
            </a>
        </div>
    </aside>

    <div class="flex-1 flex flex-col min-w-0">
        
        <header class="h-20 bg-white shadow-sm flex items-center justify-between px-6 lg:px-10 z-10 border-b border-gray-200">
            <div class="flex items-center gap-4">
                <button class="md:hidden text-gray-500 hover:text-emerald-700 focus:outline-none">
                    <i class="fas fa-bars text-xl"></i>
                </button>
                <h1 class="text-xl font-bold text-gray-800 hidden sm:block">Client Portal</h1>
            </div>
            
            <div class="flex items-center gap-6">
                <button class="text-gray-400 hover:text-emerald-600 transition relative">
                    <i class="fas fa-bell text-xl"></i>
                    <span class="absolute -top-1 -right-1 h-4 w-4 bg-red-500 rounded-full border-2 border-white text-[8px] text-white flex items-center justify-center font-bold">2</span>
                </button>
                <div class="flex items-center gap-3 pl-6 border-l border-gray-200">
                    <div class="h-10 w-10 bg-emerald-100 text-emerald-700 rounded-full flex items-center justify-center font-bold text-lg shadow-inner">
                        <?= strtoupper(substr($firstName, 0, 1)) ?>
                    </div>
                    <div class="hidden md:block">
                        <p class="text-sm font-bold text-gray-800 leading-tight"><?= $firstName . ' ' . $lastName ?></p>
                        <p class="text-xs text-gray-500"><?= $email ?></p>
                    </div>
                </div>
            </div>
        </header>

        <main class="flex-1 relative flex flex-col min-h-0 bg-slate-50">
            
            <div id="dashboardContent" class="p-6 lg:p-10 overflow-y-auto flex-1">
                <div class="mb-8">
                    <h2 class="text-3xl font-extrabold text-gray-900">Welcome back, <?= $firstName ?>! 👋</h2>
                    <p class="text-gray-500 mt-1">Here is the overview of your lumber dealer applications.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-5">
                        <div class="h-14 w-14 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center text-2xl">
                            <i class="fas fa-file-invoice"></i>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Total Applications</p>
                            <h3 class="text-3xl font-black text-gray-800">0</h3>
                        </div>
                    </div>
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-5">
                        <div class="h-14 w-14 rounded-full bg-yellow-50 text-yellow-600 flex items-center justify-center text-2xl">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Pending Review</p>
                            <h3 class="text-3xl font-black text-gray-800">0</h3>
                        </div>
                    </div>
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-5">
                        <div class="h-14 w-14 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center text-2xl">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Approved Permits</p>
                            <h3 class="text-3xl font-black text-gray-800">0</h3>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center bg-slate-50">
                        <h3 class="font-bold text-gray-800 text-lg">Recent Applications</h3>
                        
                        <button onclick="openModal('appTypeModal')" class="bg-emerald-700 text-white px-4 py-2 rounded-lg text-sm font-bold hover:bg-emerald-800 transition shadow-sm">
                            <i class="fas fa-plus mr-1"></i> Lumber Application
                        </button>

                    </div>
                    <div class="p-8 text-center">
                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 text-gray-400 mb-4">
                            <i class="fas fa-folder-open text-2xl"></i>
                        </div>
                        <h4 class="text-gray-900 font-bold mb-1">No applications found</h4>
                        <p class="text-gray-500 text-sm max-w-sm mx-auto">You haven't submitted any lumber dealer permit applications yet. Click the button above to start your first application.</p>
                    </div>
                </div>
            </div>

            <iframe id="appIframe" class="hidden w-full h-full flex-1 border-none bg-slate-50 z-10" src=""></iframe>

        </main>
    </div>

    <div id="appTypeModal" class="fixed inset-0 z-[60] hidden bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden transition-all transform modal-enter" id="appTypeModalContent">
            <div class="bg-emerald-900 p-6 text-white flex justify-between items-center">
                <h3 class="text-xl font-bold">Select Application Type</h3>
                <button onclick="closeModal('appTypeModal')" class="hover:bg-emerald-800 p-2 h-8 w-8 flex items-center justify-center rounded-full transition"><i class="fas fa-times"></i></button>
            </div>
            <div class="p-8 space-y-4">
                <button onclick="showRequirements('New')" class="w-full flex items-center justify-between p-4 border-2 border-gray-100 rounded-xl hover:border-emerald-500 hover:bg-emerald-50 transition group shadow-sm">
                    <div class="flex items-center gap-4">
                        <div class="bg-emerald-100 h-12 w-12 flex items-center justify-center rounded-full text-emerald-600 group-hover:bg-emerald-600 group-hover:text-white transition text-lg"><i class="fas fa-file-signature"></i></div>
                        <div class="text-left">
                            <h4 class="font-bold text-gray-800 text-base">New Application</h4>
                            <p class="text-xs text-gray-500 mt-0.5">Apply for a new lumber dealer permit</p>
                        </div>
                    </div>
                    <i class="fas fa-chevron-right text-gray-300 group-hover:text-emerald-500 transition"></i>
                </button>
                <button onclick="showRequirements('Renewal')" class="w-full flex items-center justify-between p-4 border-2 border-gray-100 rounded-xl hover:border-blue-500 hover:bg-blue-50 transition group shadow-sm">
                    <div class="flex items-center gap-4">
                        <div class="bg-blue-100 h-12 w-12 flex items-center justify-center rounded-full text-blue-600 group-hover:bg-blue-600 group-hover:text-white transition text-lg"><i class="fas fa-sync-alt"></i></div>
                        <div class="text-left">
                            <h4 class="font-bold text-gray-800 text-base">Renewal Application</h4>
                            <p class="text-xs text-gray-500 mt-0.5">Renew an existing lumber dealer permit</p>
                        </div>
                    </div>
                    <i class="fas fa-chevron-right text-gray-300 group-hover:text-blue-500 transition"></i>
                </button>
            </div>
        </div>
    </div>

    <div id="requirementsModal" class="fixed inset-0 z-[60] hidden bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-3xl overflow-hidden transition-all transform flex flex-col max-h-[90vh] modal-enter" id="requirementsModalContent">
            <div class="bg-emerald-900 p-6 text-white flex justify-between items-center shrink-0">
                <div>
                    <h3 class="text-xl font-bold" id="reqModalTitle">Requirements</h3>
                    <p class="text-emerald-200 text-sm mt-1">Please prepare all scanned documents in <span class="text-red-300 font-bold">PDF Format</span></p>
                </div>
                <button onclick="closeModal('requirementsModal')" class="hover:bg-emerald-800 p-2 h-8 w-8 flex items-center justify-center rounded-full transition"><i class="fas fa-times"></i></button>
            </div>
            
            <div class="p-6 overflow-y-auto flex-1 bg-slate-50">
                <ul id="requirementsList" class="space-y-3">
                    </ul>
            </div>
            
            <div class="p-6 border-t border-gray-200 shrink-0 flex justify-between items-center bg-white">
                <button onclick="goBackToAppType()" class="text-gray-500 hover:text-gray-800 font-bold text-sm transition px-4 py-2 rounded-lg hover:bg-gray-100">
                    <i class="fas fa-arrow-left mr-2"></i>Back
                </button>
                <button id="proceedBtn" class="bg-emerald-700 text-white px-8 py-3 rounded-xl font-bold hover:bg-emerald-800 shadow-md shadow-emerald-900/20 transition active:scale-[0.98]">
                    Proceed to Application <i class="fas fa-arrow-right ml-2"></i>
                </button>
            </div>
        </div>
    </div>

    <script>
        // Pass PHP requirements array into a Javascript Variable
        const dbRequirements = <?php echo json_encode($requirements ?: []); ?>;

        // Function to show the main dashboard content and hide the iframe
        function showDashboard() {
            document.getElementById('appIframe').classList.add('hidden');
            document.getElementById('appIframe').src = ''; // Clear iframe memory
            document.getElementById('dashboardContent').classList.remove('hidden');
            updateActiveNav(event ? event.currentTarget : document.querySelector('nav a:first-child'));
        }

        // Function to hide the dashboard content and load a URL into the iframe
        function loadIframe(url, element = null) {
            document.getElementById('dashboardContent').classList.add('hidden');
            const iframe = document.getElementById('appIframe');
            iframe.src = url;
            iframe.classList.remove('hidden');
            if(element) updateActiveNav(element);
        }

        // Function to handle active states on sidebar navigation
        function updateActiveNav(activeElement) {
            const navItems = document.querySelectorAll('.nav-item');
            navItems.forEach(item => {
                // Reset to inactive state classes
                item.className = "flex items-center gap-3 text-emerald-100 hover:bg-emerald-800 hover:text-white px-4 py-3 rounded-xl font-medium transition nav-item";
            });
            // Set active state classes for clicked element
            if(activeElement) {
                activeElement.className = "flex items-center gap-3 bg-emerald-800 text-white px-4 py-3 rounded-xl font-semibold transition nav-item active-nav";
            }
        }

        // Modal Open/Close functionality
        function openModal(id) {
            const modal = document.getElementById(id);
            const content = document.getElementById(id + 'Content');
            
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden'; // Prevent background scrolling
            
            // Add slight animation
            requestAnimationFrame(() => {
                content.classList.add('modal-enter-active');
            });
        }

        function closeModal(id) {
            const modal = document.getElementById(id);
            const content = document.getElementById(id + 'Content');
            
            content.classList.remove('modal-enter-active');
            
            setTimeout(() => {
                modal.classList.add('hidden');
                document.body.style.overflow = 'auto'; // Re-enable background scrolling
            }, 200); // match transition duration
        }

        // Navigate back to the App Type selection
        function goBackToAppType() {
            closeModal('requirementsModal');
            setTimeout(() => {
                openModal('appTypeModal');
            }, 200);
        }

        // Generate and Show Requirements based on Type
        function showRequirements(type) {
            // Hide App Type Modal
            closeModal('appTypeModal');
            
            // Update Title
            document.getElementById('reqModalTitle').innerText = `${type} Application Requirements`;
            
            const listContainer = document.getElementById('requirementsList');
            listContainer.innerHTML = ''; // Clear previous contents
            
            let counter = 1;
            let hasRequirements = false;
            
            if (dbRequirements && dbRequirements.length > 0) {
                dbRequirements.forEach(req => {
                    // Check logic based on New or Renewal column in database
                    let status = (type === 'New') ? req.new_app_status : req.renewal_app_status;
                    
                    // Only show if it's NOT marked as "Not Required"
                    if (status && status.toLowerCase() !== 'not required') {
                        hasRequirements = true;
                        
                        const li = document.createElement('li');
                        li.className = "flex items-start gap-4 p-4 bg-white rounded-xl shadow-sm border border-gray-200 transition hover:border-emerald-300";
                        
                        let downloadLinkHTML = '';
                        if (req.download_link && req.download_link.trim() !== '') {
                            downloadLinkHTML = `
                                <a href="${req.download_link}" download class="mt-3 inline-flex items-center text-xs font-bold text-emerald-600 hover:text-emerald-800 bg-emerald-50 px-3 py-1.5 rounded-lg transition">
                                    <i class="fas fa-download mr-1.5"></i> Download Template
                                </a>`;
                        }

                        li.innerHTML = `
                            <div class="flex-shrink-0 w-8 h-8 bg-emerald-100 text-emerald-800 rounded-full flex items-center justify-center font-black text-sm border border-emerald-200 shadow-inner">
                                ${counter++}
                            </div>
                            <div class="flex-1 pt-1">
                                <p class="font-bold text-gray-800 text-sm leading-snug">${req.requirement_name}</p>
                                <p class="text-xs text-gray-500 mt-1"><i class="fas fa-info-circle text-emerald-500 mr-1"></i> ${status}</p>
                                ${downloadLinkHTML}
                            </div>
                        `;
                        listContainer.appendChild(li);
                    }
                });
            }
            
            // Fallback if the database has nothing or no requirements matched
            if (!hasRequirements) {
                listContainer.innerHTML = `
                    <div class="text-center p-10">
                        <i class="fas fa-folder-open text-gray-300 text-4xl mb-3"></i>
                        <p class="text-gray-500 font-medium">No specific requirements found in the database for this application type.</p>
                    </div>`;
            }

            // Updated Proceed Button Action to open inside the iframe
            const proceedBtn = document.getElementById('proceedBtn');
            proceedBtn.onclick = () => {
                closeModal('requirementsModal');
                loadIframe(`create_application.php?type=${type.toLowerCase()}`);
            };

            // Open Requirements Modal (with a slight delay so the App Type modal closes smoothly)
            setTimeout(() => {
                openModal('requirementsModal');
            }, 250);
        }
    </script>
</body>
</html>