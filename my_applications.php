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

// 1. Fetch Applications for the Current Client
$stmt = $pdo->prepare("SELECT * FROM permit_applications WHERE client_id = ? ORDER BY app_id DESC");
$stmt->execute([$client_id]);
$applications = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 2. Fetch Audit Trail Logs grouped by App ID
$logs = [];
if (!empty($applications)) {
    $app_ids = array_column($applications, 'app_id');
    $placeholders = implode(',', array_fill(0, count($app_ids), '?'));
    
    $log_stmt = $pdo->prepare("SELECT * FROM application_logs WHERE app_id IN ($placeholders) ORDER BY created_at DESC");
    $log_stmt->execute($app_ids);
    $all_logs = $log_stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($all_logs as $log) {
        $logs[$log['app_id']][] = $log;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Applications</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;800&display=swap');
        body { font-family: 'Inter', sans-serif; }
        
        /* Modal Animations */
        .modal-enter { opacity: 0; transform: scale(0.95); }
        .modal-enter-active { opacity: 1; transform: scale(1); transition: all 0.2s ease-out; }
    </style>
</head>
<body class="bg-slate-50 min-h-screen p-6 lg:p-10">

    <div class="mb-8 flex justify-between items-center">
        <div>
            <h2 class="text-3xl font-extrabold text-gray-900">My Applications</h2>
            <p class="text-gray-500 mt-1">Track the status and history of your submitted permits.</p>
        </div>
        
        <button onclick="window.location.reload();" class="bg-white border border-gray-200 text-gray-600 px-4 py-2 rounded-lg text-sm font-bold hover:bg-gray-50 transition shadow-sm">
            <i class="fas fa-sync-alt mr-1"></i> Refresh
        </button>
    </div>

    <?php if (empty($applications)): ?>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-10 text-center">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 text-gray-400 mb-4">
                <i class="fas fa-folder-open text-2xl"></i>
            </div>
            <h4 class="text-gray-900 font-bold mb-1">No applications found</h4>
            <p class="text-gray-500 text-sm max-w-sm mx-auto">You haven't submitted any applications yet.</p>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
            <?php foreach ($applications as $app): ?>
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden flex flex-col hover:border-emerald-300 transition-colors">
                    <div class="p-6 flex-1">
                        <div class="flex justify-between items-start mb-4">
                            <span class="px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider bg-emerald-100 text-emerald-800">
                                <?= htmlspecialchars($app['app_type']) ?>
                            </span>
                            <span class="text-xs font-bold text-gray-400">
                                ID: #<?= str_pad($app['app_id'], 5, '0', STR_PAD_LEFT) ?>
                            </span>
                        </div>
                        
                        <h3 class="text-lg font-bold text-gray-900 leading-tight mb-1"><?= htmlspecialchars($app['business_name']) ?></h3>
                        <p class="text-sm text-gray-500 mb-4"><i class="fas fa-user-tag mr-1"></i> <?= htmlspecialchars($app['applicant_type']) ?></p>
                        
                        <?php if ($app['app_type'] == 'Renewal' && !empty($app['reference_number'])): ?>
                            <div class="bg-blue-50 text-blue-800 border border-blue-100 rounded-lg p-3 text-xs mb-4">
                                <span class="font-bold">Ref No:</span> <?= htmlspecialchars($app['reference_number']) ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="bg-slate-50 px-6 py-4 border-t border-gray-100 mt-auto">
                        <button onclick="openAuditTrail(<?= $app['app_id'] ?>)" class="w-full bg-white border border-gray-300 text-gray-700 px-4 py-2.5 rounded-xl text-sm font-bold hover:bg-emerald-50 hover:text-emerald-700 hover:border-emerald-300 transition flex items-center justify-center gap-2">
                            <i class="fas fa-history"></i> View Audit Trail
                        </button>
                    </div>
                </div>

                <script type="application/json" id="logs_<?= $app['app_id'] ?>">
                    <?= json_encode($logs[$app['app_id']] ?? []) ?>
                </script>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div id="auditModal" class="fixed inset-0 z-[60] hidden bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden transition-all transform flex flex-col max-h-[90vh] modal-enter" id="auditModalContent">
            
            <div class="bg-emerald-900 p-6 text-white flex justify-between items-center shrink-0">
                <div>
                    <h3 class="text-xl font-bold">Application Audit Trail</h3>
                    <p class="text-emerald-200 text-sm mt-1" id="auditModalAppId">App ID: #00000</p>
                </div>
                <button onclick="closeModal('auditModal')" class="hover:bg-emerald-800 p-2 h-8 w-8 flex items-center justify-center rounded-full transition"><i class="fas fa-times"></i></button>
            </div>
            
            <div class="p-8 overflow-y-auto flex-1 bg-slate-50">
                <ul id="auditTimeline" class="relative border-l-2 border-emerald-200 ml-3 space-y-6">
                    </ul>
            </div>
            
        </div>
    </div>

    <script>
        function openAuditTrail(appId) {
            // Update Modal Header
            document.getElementById('auditModalAppId').innerText = `Tracking App ID: #${String(appId).padStart(5, '0')}`;
            
            // Get logs from hidden script tag
            const logsData = document.getElementById('logs_' + appId).textContent;
            const logs = JSON.parse(logsData);
            
            const timeline = document.getElementById('auditTimeline');
            timeline.innerHTML = ''; // Clear previous

            if (logs.length === 0) {
                timeline.innerHTML = '<li class="text-gray-500 text-sm ml-4">No tracking history found.</li>';
            } else {
                logs.forEach((log, index) => {
                    // Check if it's the latest log to give it a glowing dot effect
                    const isLatest = index === 0; 
                    const dotClass = isLatest ? 'bg-emerald-500 ring-4 ring-emerald-100' : 'bg-gray-300 ring-4 ring-white';
                    const titleClass = isLatest ? 'text-emerald-700 font-extrabold' : 'text-gray-700 font-bold';

                    const li = document.createElement('li');
                    li.className = "pl-8 relative";
                    
                    // Format Date nicely
                    const dateObj = new Date(log.created_at);
                    const formattedDate = dateObj.toLocaleString('en-US', { month: 'short', day: 'numeric', year: 'numeric', hour: 'numeric', minute: '2-digit', hour12: true });

                    li.innerHTML = `
                        <div class="absolute w-3 h-3 rounded-full -left-[7px] top-1.5 ${dotClass}"></div>
                        <p class="text-sm ${titleClass} mb-1">${log.action}</p>
                        ${log.remarks ? `<p class="text-xs text-gray-500 leading-relaxed mb-2">${log.remarks}</p>` : ''}
                        <span class="text-[10px] uppercase font-bold text-gray-400 font-mono tracking-wider"><i class="far fa-clock mr-1"></i> ${formattedDate}</span>
                    `;
                    timeline.appendChild(li);
                });
            }

            // Show Modal
            const modal = document.getElementById('auditModal');
            const content = document.getElementById('auditModalContent');
            modal.classList.remove('hidden');
            requestAnimationFrame(() => content.classList.add('modal-enter-active'));
        }

        function closeModal(id) {
            const modal = document.getElementById(id);
            const content = document.getElementById(id + 'Content');
            content.classList.remove('modal-enter-active');
            setTimeout(() => modal.classList.add('hidden'), 200);
        }
    </script>
</body>
</html>