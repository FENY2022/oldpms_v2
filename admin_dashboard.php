<?php
session_start();
require_once 'db.php'; // Needed for dynamic stats

// PREVENT UNAUTHORIZED ACCESS
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || $_SESSION['user_type'] !== 'denr_user') {
    header("Location: index.php");
    exit;
}

// Get user details from the session
$user_name = $_SESSION['name'] ?? 'User';
$user_role_name = $_SESSION['usertype'] ?? 'Staff'; 
$user_role_id = (string)($_SESSION['user_role_id'] ?? '');

$admin_role_ids = ['12', '13', '14', '15', '27', '28', '29', '30', 'Admin'];
$is_admin = in_array($user_role_id, $admin_role_ids);

// --- FETCH ADMIN DASHBOARD STATS ---
$total_apps = 0;
$pending_apps = 0;
$approved_apps = 0;

try {
    $stats_stmt = $pdo->query("
        SELECT 
            COUNT(*) as total_apps,
            SUM(CASE WHEN status IN ('Under Evaluation', 'Pending Review') THEN 1 ELSE 0 END) as pending_apps,
            SUM(CASE WHEN status IN ('Approved', 'Issued', 'Completed') THEN 1 ELSE 0 END) as approved_apps
        FROM permit_applications
    ");
    if ($stats = $stats_stmt->fetch(PDO::FETCH_ASSOC)) {
        $total_apps = $stats['total_apps'] ?? 0;
        $pending_apps = $stats['pending_apps'] ?? 0;
        $approved_apps = $stats['approved_apps'] ?? 0;
    }
} catch (PDOException $e) {}

// --- FETCH NOTIFICATIONS (Apps pending or just resubmitted) ---
$notifications = [];
try {
    $notif_stmt = $pdo->query("
        SELECT app_id, business_name, date_submitted, status 
        FROM permit_applications 
        WHERE status IN ('Under Evaluation', 'Pending Review')
        ORDER BY date_submitted DESC
    ");
    $notifications = $notif_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {}
$notification_count = count($notifications);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | O-LDPMS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="icon" href="logo/denr_logo.png" type="image/x-icon">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;800&display=swap');
        body { font-family: 'Inter', sans-serif; }
        
        .fade-in { animation: fadeIn 0.3s ease-in-out; }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
    </style>
</head>
<body class="bg-slate-50 flex h-screen overflow-hidden">

    <aside class="w-64 bg-emerald-900 text-white flex flex-col hidden md:flex shadow-xl z-20">
        <div class="h-20 flex items-center justify-center border-b border-emerald-800">
            <img src="logo/denr_logo.png" alt="Logo" class="h-10 w-10 mr-3">
            <div class="font-bold tracking-wider">
                <span class="block text-lg leading-none">O-LDPMS</span>
                <span class="text-[10px] text-emerald-400 font-bold uppercase">Admin Portal</span>
            </div>
        </div>
        
        <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto" id="sidebar-menu">
            <p class="px-4 text-xs font-bold text-emerald-500 uppercase tracking-widest mb-2 mt-4">Main Menu</p>
            
            <a href="#" onclick="showDashboard(this); return false;" class="nav-link flex items-center gap-3 px-4 py-3 bg-emerald-800 rounded-xl text-white font-semibold transition">
                <i class="fas fa-chart-pie w-5"></i> Dashboard
            </a>
            <a href="applications.php" target="content-frame" onclick="showIframe(this, 'Applications');" class="nav-link flex items-center gap-3 px-4 py-3 text-emerald-100 hover:bg-emerald-800 hover:text-white rounded-xl transition">
                <i class="fas fa-folder-open w-5"></i> Applications
            </a>
            <a href="reports.php" target="content-frame" onclick="showIframe(this, 'Reports');" class="nav-link flex items-center gap-3 px-4 py-3 text-emerald-100 hover:bg-emerald-800 hover:text-white rounded-xl transition">
                <i class="fas fa-file-invoice w-5"></i> Reports
            </a>

            <?php if($is_admin): ?>
            <p class="px-4 text-xs font-bold text-emerald-500 uppercase tracking-widest mb-2 mt-6">System Config</p>
            
            <a href="manage_users.php" target="content-frame" onclick="showIframe(this, 'Manage Users');" class="nav-link flex items-center gap-3 px-4 py-3 text-emerald-100 hover:bg-emerald-800 hover:text-white rounded-xl transition">
                <i class="fas fa-users w-5"></i> Manage Users
            </a>
            
            <?php if ($user_role_id === '15' || strtolower($user_role_id) === 'admin'): ?>
            <a href="manage_requirements.php" target="content-frame" onclick="showIframe(this, 'Manage Requirements');" class="nav-link flex items-center gap-3 px-4 py-3 text-emerald-100 hover:bg-emerald-800 hover:text-white rounded-xl transition">
                <i class="fas fa-list-check w-5"></i> Requirements
            </a>
            <?php endif; ?>
            
            <?php endif; ?>
        </nav>

        <div class="p-4 border-t border-emerald-800">
            <a href="logout.php" class="flex items-center gap-3 px-4 py-3 text-red-300 hover:bg-red-900 hover:text-white rounded-xl transition">
                <i class="fas fa-sign-out-alt w-5"></i> Logout
            </a>
        </div>
    </aside>

    <div class="flex-1 flex flex-col h-screen overflow-hidden">
        
        <header class="h-20 bg-white shadow-sm flex items-center justify-between px-8 relative z-40 border-b border-gray-200">
            <div class="flex items-center gap-4">
                <button class="md:hidden text-gray-500 hover:text-emerald-700 focus:outline-none">
                    <i class="fas fa-bars text-xl"></i>
                </button>
                <h2 id="header-title" class="text-2xl font-bold text-gray-800 tracking-tight">Overview</h2>
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
                            <h3 class="text-sm font-bold text-gray-800">Review Required</h3>
                            <?php if ($notification_count > 0): ?>
                                <span class="bg-amber-100 text-amber-800 text-[10px] uppercase tracking-wider px-2 py-0.5 rounded-full font-bold">
                                    <?= $notification_count ?> Pending
                                </span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="max-h-80 overflow-y-auto">
                            <?php if ($notification_count > 0): ?>
                                <ul class="divide-y divide-gray-50">
                                    <?php foreach ($notifications as $notif): ?>
                                        <li class="p-4 hover:bg-gray-50 transition cursor-pointer" onclick="document.querySelector('a[href=\'applications.php\']').click(); toggleNotifications();">
                                            <div class="flex gap-3">
                                                <div class="h-10 w-10 rounded-full bg-amber-50 text-amber-500 flex items-center justify-center shrink-0 border border-amber-100">
                                                    <i class="fas fa-file-signature"></i>
                                                </div>
                                                <div>
                                                    <p class="text-sm text-gray-800 font-medium leading-tight">
                                                        App <span class="font-bold text-gray-900">#<?= str_pad($notif['app_id'], 5, '0', STR_PAD_LEFT) ?></span> is <strong><?= htmlspecialchars($notif['status']) ?></strong>.
                                                    </p>
                                                    <p class="text-xs text-gray-500 mt-1 truncate w-48 sm:w-64">
                                                        <?= htmlspecialchars($notif['business_name']) ?>
                                                    </p>
                                                </div>
                                            </div>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php else: ?>
                                <div class="p-8 text-center text-gray-400">
                                    <i class="far fa-check-circle text-4xl mb-3 text-gray-200"></i>
                                    <p class="text-sm font-medium text-gray-500">Inbox Zero!</p>
                                    <p class="text-xs mt-1">No applications pending review.</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-3 pl-6 border-l border-gray-200">
                    <div class="text-right hidden sm:block">
                        <p class="text-sm font-bold text-gray-800"><?php echo htmlspecialchars($user_name); ?></p>
                        <p class="text-xs text-emerald-600 font-bold uppercase tracking-wider"><?php echo htmlspecialchars($user_role_name); ?></p>
                    </div>
                    <div class="h-10 w-10 rounded-full <?php echo $is_admin ? 'bg-purple-100 text-purple-700 border-purple-200' : 'bg-emerald-100 text-emerald-700 border-emerald-200'; ?> flex items-center justify-center text-lg border shadow-sm">
                        <i class="fas <?php echo $is_admin ? 'fa-user-cog' : 'fa-user-shield'; ?>"></i>
                    </div>
                </div>
            </div>
        </header>

        <main class="flex-1 relative flex flex-col min-h-0 bg-slate-50 z-0">
            
            <div id="dashboard-content" class="absolute inset-0 overflow-y-auto p-8 fade-in">
                <div class="mb-8">
                    <h1 class="text-3xl font-extrabold text-gray-900">Welcome back, <?php echo htmlspecialchars($user_name); ?>! 👋</h1>
                    <p class="text-gray-500 mt-2 text-lg">Here is the current status of Lumber Dealer Permitting in the Caraga Region.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex items-center gap-6 hover:shadow-md transition">
                        <div class="h-14 w-14 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-2xl border border-blue-100">
                            <i class="fas fa-folder"></i>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Total Applications</p>
                            <h3 class="text-3xl font-black text-gray-900 mt-1"><?= number_format($total_apps) ?></h3>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex items-center gap-6 hover:shadow-md transition cursor-pointer" onclick="document.querySelector('a[href=\'applications.php\']').click();">
                        <div class="h-14 w-14 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-2xl border border-amber-100">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Pending Review</p>
                            <h3 class="text-3xl font-black text-gray-900 mt-1"><?= number_format($pending_apps) ?></h3>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex items-center gap-6 hover:shadow-md transition">
                        <div class="h-14 w-14 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-2xl border border-emerald-100">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Approved Permits</p>
                            <h3 class="text-3xl font-black text-gray-900 mt-1"><?= number_format($approved_apps) ?></h3>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-8 py-6 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                        <h3 class="font-bold text-lg text-gray-800">Quick Links</h3>
                        <a href="#" onclick="document.querySelector('a[href=\'applications.php\']').click();" class="text-sm bg-white border border-gray-200 px-4 py-2 rounded-lg text-emerald-700 font-semibold hover:bg-emerald-50 transition shadow-sm">View All</a>
                    </div>
                    
                    <div class="p-12 text-center">
                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-slate-100 mb-4">
                            <i class="fas fa-inbox text-2xl text-slate-400"></i>
                        </div>
                        <h4 class="text-lg font-bold text-gray-700 mb-1">Check Notifications</h4>
                        <p class="text-gray-500">Click the bell icon on the top right or the 'Applications' tab to process pending items.</p>
                    </div>
                </div>
            </div>

            <iframe name="content-frame" id="content-frame" class="w-full h-full border-0 hidden relative z-0 fade-in" title="Main Content Area"></iframe>

        </main>
    </div>

    <script>
        const dashboardContent = document.getElementById('dashboard-content');
        const iframeContent = document.getElementById('content-frame');
        const headerTitle = document.getElementById('header-title');
        const navLinks = document.querySelectorAll('.nav-link');

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

        function setActiveLink(clickedElement) {
            // Remove active classes from all links
            navLinks.forEach(link => {
                link.classList.remove('bg-emerald-800', 'text-white', 'font-semibold');
                link.classList.add('text-emerald-100');
            });
            
            // Add active classes to the clicked link
            clickedElement.classList.remove('text-emerald-100');
            clickedElement.classList.add('bg-emerald-800', 'text-white', 'font-semibold');
        }

        function showDashboard(element) {
            setActiveLink(element);
            headerTitle.innerText = "Overview";
            
            // Show Dashboard, Hide Iframe
            iframeContent.classList.add('hidden');
            dashboardContent.classList.remove('hidden');
        }

        function showIframe(element, title) {
            setActiveLink(element);
            headerTitle.innerText = title;
            
            // Show Iframe, Hide Dashboard
            dashboardContent.classList.add('hidden');
            iframeContent.classList.remove('hidden');
        }
    </script>
</body>
</html>