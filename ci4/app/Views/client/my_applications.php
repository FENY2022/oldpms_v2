<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Applications</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="icon" href="<?= base_url('logo/denr_logo.png') ?>" type="image/x-icon">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;800&display=swap');
        body { font-family: 'Inter', sans-serif; }
        
        /* Modal Animations */
        .modal-enter { opacity: 0; transform: scale(0.95); }
        .modal-enter-active { opacity: 1; transform: scale(1); transition: all 0.2s ease-out; }
    </style>
</head>
<body class="bg-slate-50 min-h-screen p-6 lg:p-10 relative">

    <?php if(!empty($upload_msg)): ?>
    <div id="toastMsg" class="fixed top-5 right-5 z-[100] <?= strpos(strtolower($upload_msg), 'error') !== false || strpos(strtolower($upload_msg), 'expired') !== false ? 'bg-red-600' : 'bg-emerald-600' ?> text-white px-6 py-4 rounded-xl shadow-2xl flex items-center gap-3 transition-opacity duration-500">
        <i class="fas <?= strpos(strtolower($upload_msg), 'error') !== false || strpos(strtolower($upload_msg), 'expired') !== false ? 'fa-exclamation-circle' : 'fa-check-circle' ?> text-xl"></i>
        <span class="font-bold"><?= htmlspecialchars($upload_msg) ?></span>
    </div>
    <script>
        setTimeout(() => {
            const t = document.getElementById('toastMsg');
            if(t) { t.style.opacity = '0'; setTimeout(()=>t.remove(), 500); }
        }, 4000);
    </script>
    <?php endif; ?>

    <div class="mb-8 flex justify-between items-center">
        <div class="flex items-center gap-4">
            <img src="<?= base_url('logo/denr_logo.png') ?>" alt="DENR Logo" class="h-12 w-12">
            <div>
                <h2 class="text-3xl font-extrabold text-gray-900">My Applications</h2>
                <p class="text-gray-500 mt-1">Track the status and history of your submitted permits.</p>
            </div>
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
            <?php foreach ($applications as $app): 
                $can_edit = ((int)$app['seconds_elapsed'] <= 180); 
                
                $status_bg = 'bg-gray-100 text-gray-800';
                if ($app['status'] === 'Returned' || $app['status'] === 'Rejected') {
                    $status_bg = 'bg-red-100 text-red-800 border border-red-200';
                } elseif (in_array($app['status'], ['Approved', 'Issued', 'Completed'])) {
                    $status_bg = 'bg-green-100 text-green-800 border border-green-200';
                } elseif ($app['status'] === 'Under Evaluation') {
                    $status_bg = 'bg-blue-100 text-blue-800 border border-blue-200';
                }
            ?>
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden flex flex-col hover:border-blue-300 transition-colors <?= $app['status'] === 'Returned' ? 'ring-2 ring-red-400' : '' ?>">
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
                        
                        <div class="mb-4">
                            <span class="px-3 py-1 rounded-full text-xs font-bold <?= $status_bg ?>">
                                <i class="fas <?= $app['status'] === 'Returned' ? 'fa-exclamation-circle' : 'fa-info-circle' ?> mr-1"></i> 
                                <?= htmlspecialchars($app['status']) ?>
                            </span>
                        </div>
                    </div>
                    
                    <div class="bg-slate-50 px-6 py-4 border-t border-gray-100 mt-auto">
                        <button onclick="openDocsModal(<?= $app['app_id'] ?>)" class="w-full <?= $app['status'] === 'Returned' ? 'bg-red-50 border border-red-200 text-red-700 hover:bg-red-100' : 'bg-blue-50 border border-blue-200 text-blue-700 hover:bg-blue-100' ?> px-4 py-2.5 rounded-xl text-sm font-bold transition flex items-center justify-center gap-2 mb-2">
                            <i class="fas fa-file-pdf"></i> View / Edit Documents
                        </button>

                        <button onclick="openAuditTrail(<?= $app['app_id'] ?>)" class="w-full bg-white border border-gray-300 text-gray-700 px-4 py-2.5 rounded-xl text-sm font-bold hover:bg-emerald-50 hover:text-emerald-700 hover:border-emerald-300 transition flex items-center justify-center gap-2">
                            <i class="fas fa-history"></i> View Audit Trail
                        </button>
                    </div>
                </div>

                <script type="application/json" id="logs_<?= $app['app_id'] ?>">
                    <?= json_encode($logs[$app['app_id']] ?? []) ?>
                </script>
                <script type="application/json" id="files_<?= $app['app_id'] ?>">
                    <?= json_encode($groupedFiles[$app['app_id']] ?? []) ?>
                </script>
                <script type="application/json" id="app_meta_<?= $app['app_id'] ?>">
                    <?= json_encode(['can_edit' => $can_edit, 'status' => $app['status']]) ?>
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
                <ul id="auditTimeline" class="relative border-l-2 border-emerald-200 ml-3 space-y-6"></ul>
            </div>
        </div>
    </div>

    <div id="docsModal" class="fixed inset-0 z-[60] hidden bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-3xl overflow-hidden transition-all transform flex flex-col max-h-[90vh] modal-enter" id="docsModalContent">
            <div class="bg-blue-900 p-6 text-white flex justify-between items-center shrink-0">
                <div>
                    <h3 class="text-xl font-bold">Application Documents</h3>
                    <p class="text-blue-200 text-sm mt-1" id="docsModalAppId">App ID: #00000</p>
                </div>
                <button onclick="closeModal('docsModal')" class="hover:bg-blue-800 p-2 h-8 w-8 flex items-center justify-center rounded-full transition"><i class="fas fa-times"></i></button>
            </div>
            <div class="p-6 overflow-y-auto flex-1 bg-slate-50">
                
                <div id="docsAlertContainer"></div>
                <ul id="docsList" class="space-y-4"></ul>
                <div id="resubmitContainer"></div>

            </div>
        </div>
    </div>

    <div id="pdfModal" class="fixed inset-0 z-[70] hidden bg-black/80 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-5xl h-[90vh] overflow-hidden flex flex-col transition-all transform modal-enter" id="pdfModalContent">
            <div class="bg-gray-900 p-4 text-white flex justify-between items-center shrink-0">
                <h3 class="text-lg font-bold flex items-center gap-2">
                    <i class="fas fa-file-pdf text-red-400"></i> <span id="pdfModalTitle">View Document</span>
                </h3>
                <button onclick="closeModal('pdfModal')" class="hover:bg-gray-700 bg-gray-800 p-2 h-8 w-8 flex items-center justify-center rounded-full transition"><i class="fas fa-times"></i></button>
            </div>
            <div class="flex-1 bg-gray-100 relative w-full h-full flex justify-center items-center">
                <iframe id="pdfIframe" src="" class="w-full h-full border-none hidden"></iframe>
                <img id="imgViewer" src="" class="max-w-full max-h-full object-contain hidden" />
            </div>
        </div>
    </div>

    <script>
        function openFileViewer(filePath, title) {
            document.getElementById('pdfModalTitle').innerText = title;
            
            const ext = filePath.split('.').pop().toLowerCase();
            const pdfViewer = document.getElementById('pdfIframe');
            const imgViewer = document.getElementById('imgViewer');
            
            if (ext === 'pdf') {
                imgViewer.classList.add('hidden');
                pdfViewer.classList.remove('hidden');
                pdfViewer.src = filePath + '#toolbar=0';
            } else {
                pdfViewer.classList.add('hidden');
                imgViewer.classList.remove('hidden');
                imgViewer.src = filePath;
            }

            const modal = document.getElementById('pdfModal');
            const content = document.getElementById('pdfModalContent');
            modal.classList.remove('hidden');
            requestAnimationFrame(() => content.classList.add('modal-enter-active'));
        }

        function openDocsModal(appId) {
            document.getElementById('docsModalAppId').innerText = `Tracking App ID: #${String(appId).padStart(5, '0')}`;
            
            const filesData = document.getElementById('files_' + appId).textContent;
            const files = JSON.parse(filesData);
            
            const metaData = document.getElementById('app_meta_' + appId).textContent;
            const meta = JSON.parse(metaData);
            const canEdit = meta.can_edit;
            const appStatus = meta.status;

            const list = document.getElementById('docsList');
            const alertContainer = document.getElementById('docsAlertContainer');
            const resubmitContainer = document.getElementById('resubmitContainer');
            
            list.innerHTML = '';
            resubmitContainer.innerHTML = '';

            if (appStatus === 'Returned') {
                alertContainer.innerHTML = `
                    <div class="mb-4 bg-red-50 text-red-800 border border-red-200 p-4 rounded-xl shadow-sm">
                        <h4 class="font-bold mb-1"><i class="fas fa-exclamation-triangle"></i> Application Returned</h4>
                        <p class="text-sm text-red-700">Please review the documents tagged as "Incorrect" below. Re-upload the required files and hit the Resubmit button at the bottom.</p>
                    </div>
                `;
            } else {
                alertContainer.innerHTML = `
                    <p class="text-xs text-gray-500 mb-4 bg-yellow-50 text-yellow-800 border border-yellow-200 p-3 rounded-lg">
                        <i class="fas fa-info-circle"></i> You can view your submitted documents below. The initial re-upload window is strictly available for <strong>3 minutes</strong> after submitting.
                    </p>
                `;
            }

            if (files.length === 0) {
                list.innerHTML = '<li class="text-center p-6 text-gray-500 font-medium">No documents found for this application.</li>';
            } else {
                files.forEach(reqGroup => {
                    const li = document.createElement('li');
                    const isIncorrect = reqGroup.status === 'Incorrect';
                    
                    li.className = `p-4 rounded-xl border transition shadow-sm flex flex-col gap-3 
                        ${isIncorrect ? 'bg-red-50/50 border-red-300' : 'bg-white border-gray-200 hover:border-blue-300'}`;
                    
                    let fileLinks = reqGroup.files.map((f, index) => {
                        const safeTitle = (reqGroup.requirement_name + ' - File ' + (index + 1)).replace(/'/g, "\\'");
                        return `
                        <button type="button" onclick="openFileViewer('${f.file_path}', '${safeTitle}')" class="text-xs font-bold text-blue-600 hover:text-blue-800 mt-2 mr-2 inline-flex items-center gap-1 bg-blue-50 px-3 py-1.5 rounded-lg border border-blue-100 transition hover:bg-blue-200">
                            <i class="fas fa-eye"></i> View File ${index + 1}
                        </button>
                    `}).join('');

                    let statusBadge = '';
                    if (reqGroup.status === 'OK') {
                        statusBadge = '<span class="px-2 py-0.5 bg-green-100 text-green-700 text-[10px] uppercase tracking-wider font-bold rounded"><i class="fas fa-check-circle mr-1"></i> Verified OK</span>';
                    } else if (isIncorrect) {
                        statusBadge = '<span class="px-2 py-0.5 bg-red-100 text-red-700 text-[10px] uppercase tracking-wider font-bold rounded"><i class="fas fa-times-circle mr-1"></i> Incorrect File</span>';
                    } else {
                        statusBadge = '<span class="px-2 py-0.5 bg-gray-100 text-gray-600 text-[10px] uppercase tracking-wider font-bold rounded"><i class="fas fa-clock mr-1"></i> Pending / Unverified</span>';
                    }

                    let remarkBox = '';
                    if (isIncorrect && reqGroup.remarks) {
                        remarkBox = `
                            <div class="mt-3 p-3 bg-red-100 border border-red-200 rounded text-sm text-red-800 shadow-inner">
                                <strong class="block text-xs uppercase tracking-wider text-red-600 mb-1"><i class="fas fa-comment-dots"></i> Evaluator Note:</strong>
                                ${reqGroup.remarks}
                            </div>
                        `;
                    }

                    let allowEdit = canEdit || (appStatus === 'Returned' && reqGroup.status !== 'OK');
                    
                    let editForm = allowEdit ? `
                        <form method="POST" action="<?= base_url('/client/my-applications/reupload-file') ?>" enctype="multipart/form-data" class="mt-3 pt-3 border-t border-gray-200 flex items-center gap-3">
                            <input type="hidden" name="requirement_id" value="${reqGroup.requirement_id}">
                            <input type="hidden" name="app_id" value="${appId}">
                            <input type="file" name="new_files[]" accept=".pdf,.jpg,.jpeg,.png" multiple required class="flex-1 text-xs text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200 cursor-pointer bg-white border border-gray-300 rounded-lg">
                            <button type="submit" class="bg-gray-800 text-white text-xs px-4 py-2 rounded-lg font-bold hover:bg-gray-900 transition flex items-center gap-1 shrink-0">
                                <i class="fas fa-upload"></i> Upload Fix
                            </button>
                        </form>
                    ` : `
                        <div class="mt-2 pt-2 border-t border-gray-100 text-xs text-gray-500 italic">
                            <i class="fas fa-lock mr-1"></i> Upload locked.
                        </div>
                    `;

                    li.innerHTML = `
                        <div class="flex justify-between items-start">
                            <div class="w-full">
                                <div class="flex items-center gap-2 mb-1">
                                    <p class="font-bold text-gray-800 text-sm leading-tight">${reqGroup.requirement_name}</p>
                                    ${statusBadge}
                                </div>
                                <div class="flex flex-wrap">
                                    ${fileLinks}
                                </div>
                                ${remarkBox}
                            </div>
                        </div>
                        ${editForm}
                    `;
                    list.appendChild(li);
                });
            }

            if (appStatus === 'Returned') {
                resubmitContainer.innerHTML = `
                    <div class="mt-6 border-t border-gray-200 pt-5 flex justify-between items-center bg-gray-50 p-5 rounded-xl shadow-sm">
                        <div>
                            <h4 class="font-bold text-gray-800 text-sm">Done fixing everything?</h4>
                            <p class="text-xs text-gray-500 mt-1">Make sure you uploaded all corrections before resubmitting.</p>
                        </div>
                        <form method="POST" action="<?= base_url('/client/my-applications/resubmit') ?>">
                            <input type="hidden" name="app_id" value="${appId}">
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 px-6 rounded-xl transition shadow-md flex items-center">
                                <i class="fas fa-paper-plane mr-2"></i> Resubmit Application
                            </button>
                        </form>
                    </div>
                `;
            }

            const modal = document.getElementById('docsModal');
            const content = document.getElementById('docsModalContent');
            modal.classList.remove('hidden');
            requestAnimationFrame(() => content.classList.add('modal-enter-active'));
        }

        function openAuditTrail(appId) {
            document.getElementById('auditModalAppId').innerText = `Tracking App ID: #${String(appId).padStart(5, '0')}`;
            const logsData = document.getElementById('logs_' + appId).textContent;
            const logs = JSON.parse(logsData);
            const timeline = document.getElementById('auditTimeline');
            timeline.innerHTML = '';

            if (logs.length === 0) {
                timeline.innerHTML = '<li class="text-gray-500 text-sm ml-4">No tracking history found.</li>';
            } else {
                logs.forEach((log, index) => {
                    const isLatest = index === 0; 
                    const dotClass = isLatest ? 'bg-emerald-500 ring-4 ring-emerald-100' : 'bg-gray-300 ring-4 ring-white';
                    const titleClass = isLatest ? 'text-emerald-700 font-extrabold' : 'text-gray-700 font-bold';
                    const li = document.createElement('li');
                    li.className = "pl-8 relative";
                    const dateObj = new Date(log.created_at);
                    const formattedDate = dateObj.toLocaleString('en-US', { month: 'short', day: 'numeric', year: 'numeric', hour: 'numeric', minute: '2-digit', hour12: true });

                    const safeAction = log.action ? log.action.replace(/</g, "&lt;").replace(/>/g, "&gt;") : '';
                    const safeRemarks = log.remarks ? log.remarks.replace(/</g, "&lt;").replace(/>/g, "&gt;") : '';

                    li.innerHTML = `
                        <div class="absolute w-3 h-3 rounded-full -left-[7px] top-1.5 ${dotClass}"></div>
                        <div class="flex flex-col pb-2">
                            <p class="text-sm ${titleClass} mb-1">${safeAction}</p>
                            ${safeRemarks ? `<div class="bg-white border border-gray-200 rounded-md p-3 mb-2 shadow-sm"><p class="text-xs text-gray-600 leading-relaxed italic"><i class="fas fa-quote-left text-gray-300 mr-1"></i> ${safeRemarks}</p></div>` : ''}
                            <span class="text-[10px] uppercase font-bold text-gray-400 font-mono tracking-wider"><i class="far fa-clock mr-1"></i> ${formattedDate}</span>
                        </div>
                    `;
                    timeline.appendChild(li);
                });
            }

            const modal = document.getElementById('auditModal');
            const content = document.getElementById('auditModalContent');
            modal.classList.remove('hidden');
            requestAnimationFrame(() => content.classList.add('modal-enter-active'));
        }

        function closeModal(id) {
            const modal = document.getElementById(id);
            const content = document.getElementById(id + 'Content');
            content.classList.remove('modal-enter-active');
            
            setTimeout(() => {
                modal.classList.add('hidden');
                
                if (id === 'pdfModal') {
                    document.getElementById('pdfIframe').src = '';
                    document.getElementById('imgViewer').src = '';
                }
            }, 200);
        }
    </script>
</body>
</html>