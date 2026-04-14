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
$client_id = $_SESSION['client_id'] ?? null;
$profile_picture = null;

// Fetch the latest profile picture from the database
if ($client_id) {
    try {
        $user_stmt = $pdo->prepare("SELECT profile_picture FROM user_client WHERE client_id = ?");
        $user_stmt->execute([$client_id]);
        if ($user_data = $user_stmt->fetch(PDO::FETCH_ASSOC)) {
            $profile_picture = $user_data['profile_picture'];
        }
    } catch (PDOException $e) {
        // Silently fallback if error
    }
}

// Fetch Dashboard Statistics
$total_apps = 0;
$pending_apps = 0;
$approved_apps = 0;

if ($client_id) {
    try {
        $stats_stmt = $pdo->prepare("
            SELECT 
                COUNT(*) as total_apps,
                SUM(CASE WHEN status IN ('Under Evaluation', 'Pending Review') THEN 1 ELSE 0 END) as pending_apps,
                SUM(CASE WHEN status IN ('Approved', 'Issued', 'Completed') THEN 1 ELSE 0 END) as approved_apps
            FROM permit_applications 
            WHERE client_id = ?
        ");
        $stats_stmt->execute([$client_id]);
        $stats = $stats_stmt->fetch(PDO::FETCH_ASSOC);

        $total_apps = $stats['total_apps'] ?? 0;
        $pending_apps = $stats['pending_apps'] ?? 0;
        $approved_apps = $stats['approved_apps'] ?? 0;
    } catch (PDOException $e) {
        // Fallback to 0
    }
}

// Fetch Notifications (Apps that are 'Returned')
$notifications = [];
if ($client_id) {
    try {
        $notif_stmt = $pdo->prepare("
            SELECT app_id, business_name, date_submitted, status 
            FROM permit_applications 
            WHERE client_id = ? AND status = 'Returned'
            ORDER BY date_submitted DESC
        ");
        $notif_stmt->execute([$client_id]);
        $notifications = $notif_stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        // Fallback empty array
    }
}
$notification_count = count($notifications);

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

    <aside id="sidebar" class="w-64 bg-emerald-900 text-white flex flex-col absolute md:relative z-[100] h-full shadow-2xl transform -translate-x-full md:translate-x-0 transition-transform duration-300 ease-in-out">
        <div class="h-20 flex items-center justify-between px-6 bg-emerald-950 border-b border-emerald-800">
            <div class="flex items-center gap-3">
                <img src="logo/denr_logo.png" alt="DENR Logo" class="h-10 w-10">
                <div>
                    <span class="text-xl font-black tracking-tighter block leading-none">O-LDPMS</span>
                    <span class="text-[9px] uppercase font-bold text-emerald-400 tracking-widest">CARAGA REGION</span>
                </div>
            </div>
            <button onclick="toggleSidebar()" class="md:hidden text-gray-400 hover:text-white focus:outline-none">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <nav class="flex-1 py-6 px-4 space-y-2 overflow-y-auto">
            <a href="#" id="nav-dashboard" onclick="showDashboard(this)" class="flex items-center gap-3 bg-emerald-800 text-white px-4 py-3 rounded-xl font-semibold transition nav-item active-nav">
                <i class="fas fa-home w-5 text-emerald-400"></i> Dashboard
            </a>
            
            <a href="#" onclick="loadIframe('my_applications.php', this)" class="flex items-center gap-3 text-emerald-100 hover:bg-emerald-800 hover:text-white px-4 py-3 rounded-xl font-medium transition nav-item">
                <i class="fas fa-file-alt w-5 text-emerald-400"></i> My Applications
            </a>
            
            <a href="#" class="flex items-center gap-3 text-emerald-100 hover:bg-emerald-800 hover:text-white px-4 py-3 rounded-xl font-medium transition nav-item">
                <i class="fas fa-folder-open w-5 text-emerald-400"></i> Documents
            </a>
            <a href="#" onclick="loadIframe('profile_settings.php', this)" class="flex items-center gap-3 text-emerald-100 hover:bg-emerald-800 hover:text-white px-4 py-3 rounded-xl font-medium transition nav-item">
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
        
<header class="h-20 bg-white shadow-sm flex items-center justify-between px-6 lg:px-10 relative z-40 border-b border-gray-200">            <div class="flex items-center gap-4">
                <button onclick="toggleSidebar()" class="md:hidden text-gray-500 hover:text-emerald-700 focus:outline-none">
                    <i class="fas fa-bars text-xl"></i>
                </button>
                <h1 class="text-xl font-bold text-gray-800 hidden sm:block">Client Portal</h1>
            </div>
            
            <div class="flex items-center gap-6">
                <div class="relative">
                    <button onclick="toggleNotifications()" class="text-gray-400 hover:text-emerald-600 transition relative focus:outline-none">
                        <i class="fas fa-bell text-xl"></i>
                        <?php if ($notification_count > 0): ?>
                            <span class="absolute -top-1 -right-1 h-4 w-4 bg-red-500 rounded-full border-2 border-white text-[8px] text-white flex items-center justify-center font-bold">
                                <?= $notification_count ?>
                            </span>
                        <?php endif; ?>
                    </button>

                    <div id="notificationDropdown" class="hidden absolute right-0 mt-3 w-80 sm:w-96 bg-white rounded-xl shadow-xl border border-gray-100 z-50 overflow-hidden transform opacity-0 scale-95 transition-all duration-200 origin-top-right">
                        <div class="bg-slate-50 px-4 py-3 border-b border-gray-100 flex justify-between items-center">
                            <h3 class="text-sm font-bold text-gray-800">Notifications</h3>
                            <?php if ($notification_count > 0): ?>
                                <span class="bg-red-100 text-red-800 text-[10px] uppercase tracking-wider px-2 py-0.5 rounded-full font-bold">
                                    <?= $notification_count ?> Action Required
                                </span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="max-h-80 overflow-y-auto">
                            <?php if ($notification_count > 0): ?>
                                <ul class="divide-y divide-gray-50">
                                    <?php foreach ($notifications as $notif): ?>
                                        <li class="p-4 hover:bg-gray-50 transition cursor-pointer" onclick="goToApplication()">
                                            <div class="flex gap-3">
                                                <div class="h-10 w-10 rounded-full bg-red-50 text-red-500 flex items-center justify-center shrink-0 border border-red-100">
                                                    <i class="fas fa-exclamation-triangle"></i>
                                                </div>
                                                <div>
                                                    <p class="text-sm text-gray-800 font-medium leading-tight">
                                                        Application <span class="font-bold text-gray-900">#<?= str_pad($notif['app_id'], 5, '0', STR_PAD_LEFT) ?></span> has been returned.
                                                    </p>
                                                    <p class="text-xs text-gray-500 mt-1 truncate w-48 sm:w-64">
                                                        Business: <?= htmlspecialchars($notif['business_name']) ?>
                                                    </p>
                                                    <span class="text-[10px] text-red-600 font-bold uppercase tracking-wider block mt-2">
                                                        <i class="fas fa-arrow-right"></i> Click to Fix Issues
                                                    </span>
                                                </div>
                                            </div>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php else: ?>
                                <div class="p-8 text-center text-gray-400">
                                    <i class="far fa-check-circle text-4xl mb-3 text-gray-200"></i>
                                    <p class="text-sm font-medium text-gray-500">You're all caught up!</p>
                                    <p class="text-xs mt-1">No action required on your applications.</p>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="border-t border-gray-100 px-4 py-3 bg-gray-50 text-center">
                            <a href="#" onclick="loadIframe('my_applications.php')" class="text-xs font-bold text-emerald-600 hover:text-emerald-800 transition">View All My Applications</a>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-3 pl-6 border-l border-gray-200">
                    <?php if (!empty($profile_picture) && file_exists($profile_picture)): ?>
                        <img src="<?= htmlspecialchars($profile_picture) ?>" alt="Profile" class="h-10 w-10 rounded-full object-cover shadow-inner border border-gray-200">
                    <?php else: ?>
                        <div class="h-10 w-10 bg-emerald-100 text-emerald-700 rounded-full flex items-center justify-center font-bold text-lg shadow-inner">
                            <?= strtoupper(substr($firstName, 0, 1)) ?>
                        </div>
                    <?php endif; ?>

                    <div class="hidden md:block">
                        <p class="text-sm font-bold text-gray-800 leading-tight"><?= $firstName . ' ' . $lastName ?></p>
                        <p class="text-xs text-gray-500"><?= $email ?></p>
                    </div>
                </div>
            </div>
        </header>

<main class="flex-1 relative flex flex-col min-h-0 bg-slate-50 z-0">
                
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
                            <h3 class="text-3xl font-black text-gray-800"><?= number_format($total_apps) ?></h3>
                        </div>
                    </div>
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-5">
                        <div class="h-14 w-14 rounded-full bg-yellow-50 text-yellow-600 flex items-center justify-center text-2xl">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Pending Review</p>
                            <h3 class="text-3xl font-black text-gray-800"><?= number_format($pending_apps) ?></h3>
                        </div>
                    </div>
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-5">
                        <div class="h-14 w-14 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center text-2xl">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Approved Permits</p>
                            <h3 class="text-3xl font-black text-gray-800"><?= number_format($approved_apps) ?></h3>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center bg-slate-50">
                        <h3 class="font-bold text-gray-800 text-lg">Start New Request</h3>
                        
                        <button onclick="openModal('appTypeModal')" class="bg-emerald-700 text-white px-4 py-2 rounded-lg text-sm font-bold hover:bg-emerald-800 transition shadow-sm">
                            <i class="fas fa-plus mr-1"></i> Lumber Application
                        </button>

                    </div>
                    <div class="p-8 text-center">
                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-emerald-50 text-emerald-500 mb-4">
                            <i class="fas fa-file-signature text-2xl"></i>
                        </div>
                        <h4 class="text-gray-900 font-bold mb-1">Apply for a Permit</h4>
                        <p class="text-gray-500 text-sm max-w-sm mx-auto">Click the button above to submit a new application or to renew your existing lumber dealer permit.</p>
                    </div>
                </div>
            </div>

<iframe id="appIframe" class="hidden w-full h-full flex-1 border-none bg-slate-50 relative z-0" src=""></iframe>
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

    <div id="docViewerModal" class="fixed inset-0 z-[70] hidden bg-black/80 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-5xl h-[85vh] overflow-hidden flex flex-col transition-all transform modal-enter" id="docViewerModalContent">
            <div class="bg-gray-900 p-4 text-white flex justify-between items-center shrink-0">
                <h3 class="text-lg font-bold flex items-center gap-2"><i class="fas fa-file-alt text-blue-400"></i> Document Viewer</h3>
                <div class="flex items-center gap-3">
                    <a id="docViewerDownloadBtn" href="#" download class="hover:bg-gray-700 p-2 rounded-lg transition text-sm font-bold flex items-center gap-2">
                        <i class="fas fa-download"></i> Download
                    </a>
                    <button onclick="closeDocViewer()" class="hover:bg-red-600 bg-gray-800 p-2 h-8 w-8 flex items-center justify-center rounded-full transition"><i class="fas fa-times"></i></button>
                </div>
            </div>
            <div class="flex-1 bg-gray-100 relative w-full h-full">
                <div class="absolute inset-0 flex items-center justify-center text-gray-400 -z-10 pointer-events-none">
                    <div class="text-center">
                        <i class="fas fa-circle-notch fa-spin text-3xl mb-2"></i>
                        <p>Loading document viewer...</p>
                    </div>
                </div>
                <iframe id="docViewerIframe" class="w-full h-full border-0 bg-white" src=""></iframe>
            </div>
        </div>
    </div>

    <script>
        // Pass PHP requirements array into a Javascript Variable
        const dbRequirements = <?php echo json_encode($requirements ?: []); ?>;

        // --- Notifications Toggle Logic ---
        function toggleNotifications() {
            const dropdown = document.getElementById('notificationDropdown');
            if (dropdown.classList.contains('hidden')) {
                dropdown.classList.remove('hidden');
                setTimeout(() => {
                    dropdown.classList.remove('opacity-0', 'scale-95');
                    dropdown.classList.add('opacity-100', 'scale-100');
                }, 10);
            } else {
                dropdown.classList.remove('opacity-100', 'scale-100');
                dropdown.classList.add('opacity-0', 'scale-95');
                setTimeout(() => {
                    dropdown.classList.add('hidden');
                }, 200);
            }
        }

        // Close notification dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const dropdown = document.getElementById('notificationDropdown');
            const bellBtn = event.target.closest('button[onclick="toggleNotifications()"]');
            
            if (dropdown && !dropdown.contains(event.target) && !bellBtn) {
                dropdown.classList.remove('opacity-100', 'scale-100');
                dropdown.classList.add('opacity-0', 'scale-95');
                setTimeout(() => {
                    dropdown.classList.add('hidden');
                }, 200);
            }
        });

        // Quick shortcut to go to applications from a notification click
        function goToApplication() {
            const dropdown = document.getElementById('notificationDropdown');
            dropdown.classList.add('hidden');
            dropdown.classList.remove('opacity-100', 'scale-100');
            dropdown.classList.add('opacity-0', 'scale-95');
            loadIframe('my_applications.php');
        }

        // --- Sidebar Logic ---
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('-translate-x-full');
        }

        function closeSidebarOnMobile() {
            if (window.innerWidth < 768) {
                const sidebar = document.getElementById('sidebar');
                if (!sidebar.classList.contains('-translate-x-full')) {
                    sidebar.classList.add('-translate-x-full');
                }
            }
        }

        document.addEventListener('click', function(event) {
            if (window.innerWidth < 768) {
                const sidebar = document.getElementById('sidebar');
                const isToggleBtn = event.target.closest('button[onclick="toggleSidebar()"]');
                
                if (sidebar && !sidebar.contains(event.target) && !isToggleBtn) {
                    if (!sidebar.classList.contains('-translate-x-full')) {
                        sidebar.classList.add('-translate-x-full');
                    }
                }
            }
        });

        // Function to show the main dashboard content and hide the iframe
        function showDashboard(element = null) {
            document.getElementById('appIframe').classList.add('hidden');
            document.getElementById('appIframe').src = ''; // Clear iframe memory
            document.getElementById('dashboardContent').classList.remove('hidden');
            
            const navElement = element || document.getElementById('nav-dashboard');
            updateActiveNav(navElement);
            
            sessionStorage.setItem('currentView', 'dashboard');
            closeSidebarOnMobile(); 
        }

        // Function to hide the dashboard content and load a URL into the iframe
        function loadIframe(url, element = null) {
            document.getElementById('dashboardContent').classList.add('hidden');
            const iframe = document.getElementById('appIframe');
            iframe.src = url;
            iframe.classList.remove('hidden');
            
            if(element) {
                updateActiveNav(element);
            } else {
                const matchingNav = document.querySelector(`nav a[onclick*="${url}"]`);
                updateActiveNav(matchingNav); 
            }

            sessionStorage.setItem('currentView', url);
            closeSidebarOnMobile(); 
        }

        document.addEventListener("DOMContentLoaded", () => {
            const savedView = sessionStorage.getItem('currentView');
            
            if (savedView && savedView !== 'dashboard') {
                loadIframe(savedView);
            } else {
                showDashboard();
            }
        });

        function updateActiveNav(activeElement) {
            const navItems = document.querySelectorAll('.nav-item');
            navItems.forEach(item => {
                item.className = "flex items-center gap-3 text-emerald-100 hover:bg-emerald-800 hover:text-white px-4 py-3 rounded-xl font-medium transition nav-item";
            });
            
            if(activeElement) {
                activeElement.className = "flex items-center gap-3 bg-emerald-800 text-white px-4 py-3 rounded-xl font-semibold transition nav-item active-nav";
            }
        }

        function openModal(id) {
            const modal = document.getElementById(id);
            const content = document.getElementById(id + 'Content');
            
            modal.classList.remove('hidden');
            
            if(id !== 'docViewerModal') {
                document.body.style.overflow = 'hidden'; 
            }
            
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
                if (!document.getElementById('docViewerModal').classList.contains('hidden') === false) {
                    document.body.style.overflow = 'auto'; 
                }
            }, 200); 
        }

        // Document Viewer Logic
        function viewDocumentOnline(url) {
            const iframe = document.getElementById('docViewerIframe');
            const downloadBtn = document.getElementById('docViewerDownloadBtn');
            
            iframe.src = url;
            downloadBtn.href = url;
            
            openModal('docViewerModal');
        }

        function closeDocViewer() {
            closeModal('docViewerModal');
            setTimeout(() => {
                document.getElementById('docViewerIframe').src = '';
            }, 200);
        }

        function goBackToAppType() {
            closeModal('requirementsModal');
            setTimeout(() => {
                openModal('appTypeModal');
            }, 200);
        }

        // Generate and Show Requirements based on Type
        function showRequirements(type) {
            closeModal('appTypeModal');
            
            document.getElementById('reqModalTitle').innerText = `${type} Application Requirements`;
            const listContainer = document.getElementById('requirementsList');
            listContainer.innerHTML = ''; 
            
            let counter = 1;
            let hasRequirements = false;
            
            if (dbRequirements && dbRequirements.length > 0) {
                dbRequirements.forEach(req => {
                    let status = (type === 'New') ? req.new_app_status : req.renewal_app_status;
                    
                    if (status && status.toLowerCase() !== 'not required') {
                        hasRequirements = true;
                        
                        const li = document.createElement('li');
                        li.className = "flex items-start gap-4 p-4 bg-white rounded-xl shadow-sm border border-gray-200 transition hover:border-emerald-300";
                        
                        let downloadLinkHTML = '';
                        if (req.download_link && req.download_link.trim() !== '') {
                            downloadLinkHTML = `
                                <div class="flex flex-wrap gap-2 mt-3">
                                    <button onclick="viewDocumentOnline('${req.download_link}')" type="button" class="inline-flex items-center text-xs font-bold text-blue-600 hover:text-blue-800 bg-blue-50 px-3 py-1.5 rounded-lg transition border border-blue-100">
                                        <i class="fas fa-eye mr-1.5"></i> View Online
                                    </button>
                                    <a href="${req.download_link}" download class="inline-flex items-center text-xs font-bold text-emerald-600 hover:text-emerald-800 bg-emerald-50 px-3 py-1.5 rounded-lg transition border border-emerald-100">
                                        <i class="fas fa-download mr-1.5"></i> Download Template
                                    </a>
                                </div>`;
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
            
            if (!hasRequirements) {
                listContainer.innerHTML = `
                    <div class="text-center p-10">
                        <i class="fas fa-folder-open text-gray-300 text-4xl mb-3"></i>
                        <p class="text-gray-500 font-medium">No specific requirements found in the database for this application type.</p>
                    </div>`;
            }

            const proceedBtn = document.getElementById('proceedBtn');
            proceedBtn.onclick = () => {
                closeModal('requirementsModal');
                loadIframe(`create_application.php?type=${type.toLowerCase()}`);
            };

            setTimeout(() => {
                openModal('requirementsModal');
            }, 250);
        }
    </script>
</body>
</html>