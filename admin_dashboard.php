<?php
session_start();

// PREVENT UNAUTHORIZED ACCESS
// Ensure the user is logged in AND is specifically a 'denr_user'
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || $_SESSION['user_type'] !== 'denr_user') {
    header("Location: index.php");
    exit;
}

// Get user details from the session we created during login
$user_name = $_SESSION['name'];
$user_role = $_SESSION['usertype'] ?? 'Staff'; 
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
        
        <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
            <a href="admin_dashboard.php" class="flex items-center gap-3 px-4 py-3 bg-emerald-800 rounded-xl text-white font-semibold transition">
                <i class="fas fa-chart-pie w-5"></i> Dashboard
            </a>
            <a href="#" class="flex items-center gap-3 px-4 py-3 text-emerald-100 hover:bg-emerald-800 hover:text-white rounded-xl transition">
                <i class="fas fa-folder-open w-5"></i> Applications
            </a>
            <a href="#" class="flex items-center gap-3 px-4 py-3 text-emerald-100 hover:bg-emerald-800 hover:text-white rounded-xl transition">
                <i class="fas fa-users w-5"></i> Manage Users
            </a>
            <a href="manage_requirements.php" class="flex items-center gap-3 px-4 py-3 text-emerald-100 hover:bg-emerald-800 hover:text-white rounded-xl transition">
                <i class="fas fa-list-check w-5"></i> Requirements
            </a>
            <a href="#" class="flex items-center gap-3 px-4 py-3 text-emerald-100 hover:bg-emerald-800 hover:text-white rounded-xl transition">
                <i class="fas fa-file-invoice w-5"></i> Reports
            </a>
        </nav>

        <div class="p-4 border-t border-emerald-800">
            <a href="logout.php" class="flex items-center gap-3 px-4 py-3 text-red-300 hover:bg-red-900 hover:text-white rounded-xl transition">
                <i class="fas fa-sign-out-alt w-5"></i> Logout
            </a>
        </div>
    </aside>

    <div class="flex-1 flex flex-col h-screen overflow-hidden">
        
        <header class="h-20 bg-white shadow-sm flex items-center justify-between px-8 z-10 border-b border-gray-200">
            <div class="flex items-center gap-4">
                <button class="md:hidden text-gray-500 hover:text-emerald-700 focus:outline-none">
                    <i class="fas fa-bars text-xl"></i>
                </button>
                <h2 class="text-2xl font-bold text-gray-800 tracking-tight">Overview</h2>
            </div>
            
            <div class="flex items-center gap-4">
                <div class="text-right hidden sm:block">
                    <p class="text-sm font-bold text-gray-800"><?php echo htmlspecialchars($user_name); ?></p>
                    <p class="text-xs text-emerald-600 font-bold uppercase tracking-wider"><?php echo htmlspecialchars($user_role); ?></p>
                </div>
                <div class="h-10 w-10 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center text-lg border border-emerald-200 shadow-sm">
                    <i class="fas fa-user-shield"></i>
                </div>
            </div>
        </header>

        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-slate-50 p-8">
            
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
                        <h3 class="text-3xl font-black text-gray-900 mt-1">0</h3>
                    </div>
                </div>

                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex items-center gap-6 hover:shadow-md transition">
                    <div class="h-14 w-14 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-2xl border border-amber-100">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Pending Review</p>
                        <h3 class="text-3xl font-black text-gray-900 mt-1">0</h3>
                    </div>
                </div>

                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex items-center gap-6 hover:shadow-md transition">
                    <div class="h-14 w-14 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-2xl border border-emerald-100">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Approved Permits</p>
                        <h3 class="text-3xl font-black text-gray-900 mt-1">0</h3>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-8 py-6 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                    <h3 class="font-bold text-lg text-gray-800">Recent Applications</h3>
                    <a href="#" class="text-sm bg-white border border-gray-200 px-4 py-2 rounded-lg text-emerald-700 font-semibold hover:bg-emerald-50 transition shadow-sm">View All</a>
                </div>
                
                <div class="p-12 text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-slate-100 mb-4">
                        <i class="fas fa-inbox text-2xl text-slate-400"></i>
                    </div>
                    <h4 class="text-lg font-bold text-gray-700 mb-1">No Applications Yet</h4>
                    <p class="text-gray-500">There are currently no new lumber dealer applications to review.</p>
                </div>
            </div>

        </main>
    </div>

</body>
</html>