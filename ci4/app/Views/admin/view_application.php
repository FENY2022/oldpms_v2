<?php
    $appId = $application['app_id'] ?? 0;
    $statusClass = 'bg-yellow-100 text-yellow-800';
    $status = strtolower($application['status'] ?? '');
    if (in_array($status, ['approved', 'completed', 'issued'])) {
        $statusClass = 'bg-green-100 text-green-800';
    } elseif (in_array($status, ['rejected', 'returned'])) {
        $statusClass = 'bg-red-100 text-red-800';
    }
    $successMsg = session()->getFlashdata('success');
    $errorMsg = session()->getFlashdata('error');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Review Application #<?= str_pad($appId, 5, '0', STR_PAD_LEFT) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="icon" href="<?= base_url('logo/denr_logo.png') ?>" type="image/x-icon">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;800&display=swap');
        body { font-family: 'Inter', sans-serif; }
        
        /* Modal Background Scroll Prevention */
        .modal-active { overflow: hidden; }
    </style>
</head>
<body class="bg-slate-50 p-8">

    <div class="max-w-7xl mx-auto">
        <div class="mb-6 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="<?= base_url('/admin/applications') ?>" class="h-10 w-10 bg-white rounded-full flex items-center justify-center text-gray-500 hover:text-emerald-600 hover:bg-emerald-50 border border-gray-200 transition shadow-sm">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <img src="<?= base_url('logo/denr_logo.png') ?>" alt="DENR Logo" class="h-10 w-10">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Review Application</h1>
                    <p class="text-sm text-gray-500">Application ID: #<?= str_pad($appId, 5, '0', STR_PAD_LEFT) ?></p>
                </div>
            </div>
            <div>
                <span class="px-4 py-1.5 rounded-full text-sm font-bold border <?= $statusClass ?>">
                    Current Status: <?= htmlspecialchars($application['status'] ?? '') ?>
                </span>
            </div>
        </div>

        <?php if($successMsg): ?>
            <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg flex items-center gap-3 shadow-sm">
                <i class="fas fa-check-circle text-lg"></i>
                <span class="font-semibold"><?= $successMsg ?></span>
            </div>
        <?php endif; ?>
        <?php if($errorMsg): ?>
            <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg flex items-center gap-3 shadow-sm">
                <i class="fas fa-exclamation-triangle text-lg"></i>
                <span class="font-semibold"><?= $errorMsg ?></span>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 space-y-8">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                        <h3 class="font-bold text-gray-800"><i class="fas fa-info-circle text-emerald-600 mr-2"></i> Application Details</h3>
                    </div>
                    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <p class="text-xs text-gray-400 font-bold uppercase tracking-wider mb-1">Business Name</p>
                            <p class="text-gray-800 font-semibold text-lg"><?= htmlspecialchars($application['business_name'] ?? '') ?></p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 font-bold uppercase tracking-wider mb-1">Application Type</p>
                            <p class="text-gray-800 font-semibold"><span class="bg-blue-50 text-blue-600 px-2 py-0.5 rounded text-sm border border-blue-100"><?= htmlspecialchars($application['app_type'] ?? '') ?></span> - <?= htmlspecialchars($application['applicant_type'] ?? '') ?></p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 font-bold uppercase tracking-wider mb-1">Applicant Name</p>
                            <p class="text-gray-800 font-semibold"><?= htmlspecialchars(($application['firstname'] ?? '') . ' ' . ($application['mid_name'] ?? '') . ' ' . ($application['lastname'] ?? '')) ?></p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 font-bold uppercase tracking-wider mb-1">TIN Number</p>
                            <p class="text-gray-800 font-semibold"><?= htmlspecialchars($application['tin_number'] ?? 'N/A') ?></p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 font-bold uppercase tracking-wider mb-1">Contact Info</p>
                            <p class="text-gray-800 text-sm"><i class="fas fa-envelope text-gray-400 mr-1"></i> <?= htmlspecialchars($application['email'] ?? '') ?></p>
                            <p class="text-gray-800 text-sm mt-1"><i class="fas fa-phone text-gray-400 mr-1"></i> <?= htmlspecialchars($application['mobilenum'] ?? '') ?></p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 font-bold uppercase tracking-wider mb-1">Location & Office</p>
                            <p class="text-gray-800 text-sm font-semibold"><?= htmlspecialchars($application['muncity_name'] ?? 'N/A') ?></p>
                            <p class="text-emerald-600 text-xs font-bold mt-1"><?= htmlspecialchars($application['office_cover'] ?? 'N/A') ?></p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 bg-emerald-50 text-emerald-800">
                        <h3 class="font-bold"><i class="fas fa-tasks mr-2"></i> Update Evaluation Status</h3>
                    </div>
                    <form method="POST" action="<?= base_url('/admin/view-application/' . $appId) ?>" class="p-6">
                        <div class="mb-4">
                            <label class="block text-sm font-bold text-gray-700 mb-2">New Status</label>
                            <select name="status" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-emerald-500 focus:border-emerald-500 text-gray-700">
                                <option value="" disabled selected>-- Select Action --</option>
                                <option value="Under Evaluation" <?= ($application['status'] ?? '') == 'Under Evaluation' ? 'selected' : '' ?>>Under Evaluation</option>
                                <option value="Returned" <?= ($application['status'] ?? '') == 'Returned' ? 'selected' : '' ?>>Returned (Lacks Requirements / Corrections)</option>
                                <option value="For Inspection" <?= ($application['status'] ?? '') == 'For Inspection' ? 'selected' : '' ?>>Approved for Inspection</option>
                                <option value="Approved" <?= ($application['status'] ?? '') == 'Approved' ? 'selected' : '' ?>>Approved / Issued</option>
                                <option value="Rejected" <?= ($application['status'] ?? '') == 'Rejected' ? 'selected' : '' ?>>Rejected</option>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-bold text-gray-700 mb-2">Remarks / Note to Applicant</label>
                            <textarea name="remarks" rows="4" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-emerald-500 focus:border-emerald-500" placeholder="State reason for return, instructions, or evaluation remarks..."></textarea>
                            <p class="text-xs text-gray-500 mt-1">This remark will be recorded in the application logs and viewable by the applicant. Mark individual incorrect files first before returning the application. <strong>Note: Returning the application will automatically send an email notification to the client.</strong></p>
                        </div>
                        <div class="flex justify-end">
                            <button type="submit" name="update_application" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2.5 px-6 rounded-lg transition shadow-sm">
                                Submit Update
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="space-y-8">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                        <h3 class="font-bold text-gray-800"><i class="fas fa-file-pdf text-red-500 mr-2"></i> Submitted Documents</h3>
                    </div>
                    <div class="p-0">
                        <?php if(count($groupedFiles) > 0): ?>
                            <ul class="divide-y divide-gray-100">
                                <?php foreach($groupedFiles as $index => $req): ?>
                                    <li class="p-4 hover:bg-gray-50 flex items-start gap-3 transition">
                                        <i class="fas fa-file-alt text-gray-400 mt-1 text-lg"></i>
                                        <div class="flex-1">
                                            <p id="doc-title-<?= $index ?>" class="text-sm font-semibold text-gray-800 mb-1 leading-tight flex items-center flex-wrap gap-2">
                                                <?= htmlspecialchars($req['requirement_name'] ?? '') ?>
                                                <span id="doc-badge-<?= $index ?>">
                                                    <?php if(isset($req['status']) && $req['status'] === 'OK'): ?>
                                                        <span class="px-2 py-0.5 bg-green-100 text-green-700 text-[10px] uppercase tracking-wider font-bold rounded"><i class="fas fa-check-circle mr-1"></i> OK</span>
                                                    <?php elseif(isset($req['status']) && $req['status'] === 'Incorrect'): ?>
                                                        <span class="px-2 py-0.5 bg-red-100 text-red-700 text-[10px] uppercase tracking-wider font-bold rounded"><i class="fas fa-exclamation-circle mr-1"></i> Incorrect File</span>
                                                    <?php endif; ?>
                                                </span>
                                            </p>
                                            
                                            <p id="doc-remark-<?= $index ?>" class="text-xs text-red-600 italic mt-1 <?= empty($req['remarks']) ? 'hidden' : '' ?>">
                                                Remark: <?= htmlspecialchars($req['remarks'] ?? '') ?>
                                            </p>

                                            <div class="mt-1.5 flex items-center gap-3">
                                                <button type="button" onclick="openDocumentModal(<?= $index ?>)" class="inline-flex items-center text-xs font-bold text-emerald-600 hover:text-emerald-800 transition">
                                                    <i class="fas fa-search-plus mr-1"></i> Preview / Evaluate
                                                </button>
                                                <a href="<?= base_url(htmlspecialchars($req['file_path'] ?? '')) ?>" target="_blank" class="inline-flex items-center text-xs font-bold text-blue-600 hover:text-blue-800 transition">
                                                    <i class="fas fa-external-link-alt mr-1"></i> Open Direct
                                                </a>
                                            </div>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <div class="p-6 text-center text-gray-500 text-sm">
                                No requirements uploaded.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                        <h3 class="font-bold text-gray-800"><i class="fas fa-history text-blue-500 mr-2"></i> Action History</h3>
                    </div>
                    <div class="p-6">
                        <?php if(count($logs) > 0): ?>
                            <div class="relative border-l-2 border-gray-200 ml-3 space-y-6">
                                <?php foreach($logs as $log): ?>
                                    <div class="relative pl-6">
                                        <div class="absolute -left-[9px] top-1 h-4 w-4 rounded-full bg-emerald-500 border-4 border-white shadow-sm"></div>
                                        <div>
                                            <p class="text-xs font-bold text-gray-400 mb-0.5">
                                                <i class="far fa-clock mr-1"></i> <?= date('M d, Y h:i A', strtotime($log['created_at'])) ?>
                                            </p>
                                            <p class="text-sm font-bold text-gray-800"><?= htmlspecialchars($log['action']) ?></p>
                                            <?php if(!empty($log['remarks'])): ?>
                                                <div class="mt-2 text-xs text-gray-600 bg-gray-50 border border-gray-100 p-3 rounded-lg leading-relaxed">
                                                    <?= nl2br(htmlspecialchars($log['remarks'])) ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p class="text-sm text-gray-500 text-center">No logs available for this application.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="documentModal" class="fixed inset-0 z-50 hidden bg-black bg-opacity-75 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-5xl h-[90vh] flex flex-col overflow-hidden">
            <div class="px-6 py-4 border-b flex justify-between items-center bg-gray-50">
                <h3 id="modalDocTitle" class="font-bold text-lg text-gray-800">Document Title</h3>
                <button onclick="closeDocumentModal()" class="text-gray-400 hover:text-red-500 transition focus:outline-none">
                    <i class="fas fa-times text-2xl"></i>
                </button>
            </div>
            
            <div class="flex-1 bg-gray-200 relative p-4 flex items-center justify-center overflow-auto">
                <div id="modalAlert" class="hidden absolute top-4 left-1/2 transform -translate-x-1/2 text-white px-4 py-2 rounded shadow-lg font-bold text-sm z-10 transition">
                    </div>
                
                <iframe id="modalPdfViewer" src="" class="w-full h-full border-0 rounded bg-white shadow-inner hidden"></iframe>
                <img id="modalImgViewer" src="" alt="Document Preview" class="max-w-full max-h-full object-contain hidden rounded shadow-sm">
                
                <div id="modalUnsupported" class="hidden text-center">
                    <i class="fas fa-file-download text-5xl text-gray-400 mb-4"></i>
                    <p class="text-gray-600 font-semibold mb-2">Preview not available for this file type.</p>
                    <a id="modalDownloadLink" href="" target="_blank" class="inline-block bg-emerald-600 text-white px-4 py-2 rounded shadow hover:bg-emerald-700 transition">Open / Download File</a>
                </div>
            </div>
            
            <div class="px-6 py-4 border-t flex justify-between items-center bg-white">
                <button id="prevDocBtn" onclick="prevDocument()" class="px-4 py-2 bg-gray-100 text-gray-700 font-semibold rounded-lg hover:bg-gray-200 transition disabled:opacity-50 disabled:cursor-not-allowed">
                    <i class="fas fa-chevron-left mr-2"></i> Previous
                </button>
                
                <div class="flex items-center gap-3">
                    <input type="hidden" id="modalFileId" value="">
                    
                    <button id="markOkBtn" onclick="updateDocumentStatus('OK')" class="px-6 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 font-bold transition shadow-sm flex items-center">
                        <i class="fas fa-check-circle mr-2"></i> Mark as OK
                    </button>
                    
                    <button id="markIncorrectBtn" onclick="updateDocumentStatus('Incorrect')" class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 font-bold transition shadow-sm flex items-center">
                        <i class="fas fa-exclamation-circle mr-2"></i> Mark as Incorrect
                    </button>
                </div>

                <button id="nextDocBtn" onclick="nextDocument()" class="px-4 py-2 bg-gray-100 text-gray-700 font-semibold rounded-lg hover:bg-gray-200 transition disabled:opacity-50 disabled:cursor-not-allowed">
                    Next <i class="fas fa-chevron-right ml-2"></i>
                </button>
            </div>
        </div>
    </div>

    <script>
        const baseUrl = "<?= base_url('/') ?>";
        const updateFileUrl = "<?= base_url('/api/application/update-file') ?>";
        const documentsList = <?= json_encode($groupedFiles) ?>;
        let currentIndex = 0;

        const modal = document.getElementById('documentModal');
        
        const pdfViewer = document.getElementById('modalPdfViewer');
        const imgViewer = document.getElementById('modalImgViewer');
        const unsupportedViewer = document.getElementById('modalUnsupported');
        const downloadLink = document.getElementById('modalDownloadLink');
        
        const title = document.getElementById('modalDocTitle');
        const fileIdInput = document.getElementById('modalFileId');
        const prevBtn = document.getElementById('prevDocBtn');
        const nextBtn = document.getElementById('nextDocBtn');
        const markOkBtn = document.getElementById('markOkBtn');
        const markIncorrectBtn = document.getElementById('markIncorrectBtn');
        const modalAlert = document.getElementById('modalAlert');

        function openDocumentModal(index) {
            currentIndex = index;
            updateModalContent();
            modal.classList.remove('hidden');
            document.body.classList.add('modal-active');
        }

        function closeDocumentModal() {
            modal.classList.add('hidden');
            pdfViewer.src = ""; 
            imgViewer.src = "";
            document.body.classList.remove('modal-active');
            modalAlert.classList.add('hidden');
        }

        function updateModalContent() {
            if (documentsList.length === 0) return;
            
            const currentDoc = documentsList[currentIndex];
            title.innerText = currentDoc.requirement_name;
            
            pdfViewer.classList.add('hidden');
            imgViewer.classList.add('hidden');
            unsupportedViewer.classList.add('hidden');
            pdfViewer.src = "";
            imgViewer.src = "";

            const filePath = baseUrl + currentDoc.file_path;

            if (currentDoc.file_path) {
                const ext = currentDoc.file_path.split('.').pop().toLowerCase();
                
                if (ext === 'pdf') {
                    pdfViewer.src = filePath;
                    pdfViewer.classList.remove('hidden');
                } else if (['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(ext)) {
                    imgViewer.src = filePath;
                    imgViewer.classList.remove('hidden');
                } else {
                    downloadLink.href = filePath;
                    unsupportedViewer.classList.remove('hidden');
                }
            } else {
                unsupportedViewer.classList.remove('hidden');
            }

            fileIdInput.value = currentDoc.file_id;

            if (currentDoc.status === 'OK') {
                setButtonAsMarked('OK');
            } else if (currentDoc.status === 'Incorrect') {
                setButtonAsMarked('Incorrect');
            } else {
                setButtonsAsUnmarked();
            }

            prevBtn.disabled = (currentIndex === 0);
            nextBtn.disabled = (currentIndex === (documentsList.length - 1));
        }

        function nextDocument() {
            if (currentIndex < documentsList.length - 1) {
                currentIndex++;
                updateModalContent();
                modalAlert.classList.add('hidden');
            }
        }

        function prevDocument() {
            if (currentIndex > 0) {
                currentIndex--;
                updateModalContent();
                modalAlert.classList.add('hidden');
            }
        }

        function setButtonAsMarked(status) {
            if (status === 'OK') {
                markOkBtn.innerHTML = '<i class="fas fa-check-double mr-2"></i> Marked OK';
                markOkBtn.classList.replace('bg-emerald-600', 'bg-gray-400');
                markOkBtn.classList.replace('hover:bg-emerald-700', 'cursor-not-allowed');
                markOkBtn.disabled = true;

                markIncorrectBtn.innerHTML = '<i class="fas fa-exclamation-circle mr-2"></i> Mark as Incorrect';
                markIncorrectBtn.classList.replace('bg-gray-400', 'bg-red-600');
                markIncorrectBtn.classList.replace('cursor-not-allowed', 'hover:bg-red-700');
                markIncorrectBtn.disabled = false;
            } else if (status === 'Incorrect') {
                markIncorrectBtn.innerHTML = '<i class="fas fa-ban mr-2"></i> Marked Incorrect';
                markIncorrectBtn.classList.replace('bg-red-600', 'bg-gray-400');
                markIncorrectBtn.classList.replace('hover:bg-red-700', 'cursor-not-allowed');
                markIncorrectBtn.disabled = true;

                markOkBtn.innerHTML = '<i class="fas fa-check-circle mr-2"></i> Mark as OK';
                markOkBtn.classList.replace('bg-gray-400', 'bg-emerald-600');
                markOkBtn.classList.replace('cursor-not-allowed', 'hover:bg-emerald-700');
                markOkBtn.disabled = false;
            }
        }

        function setButtonsAsUnmarked() {
            markOkBtn.innerHTML = '<i class="fas fa-check-circle mr-2"></i> Mark as OK';
            markOkBtn.className = 'px-6 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 font-bold transition shadow-sm flex items-center';
            markOkBtn.disabled = false;

            markIncorrectBtn.innerHTML = '<i class="fas fa-exclamation-circle mr-2"></i> Mark as Incorrect';
            markIncorrectBtn.className = 'px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 font-bold transition shadow-sm flex items-center';
            markIncorrectBtn.disabled = false;
        }

        async function updateDocumentStatus(status) {
            let remark = '';
            
            if (status === 'Incorrect') {
                remark = prompt("Please enter a remark explaining why this document is incorrect. \n\nNote: This only tags the file. You must STILL use the main form to 'Return' the application to the client.");
                if (remark === null) return;
            }

            const formData = new FormData();
            formData.append('file_id', fileIdInput.value);
            formData.append('status', status);
            formData.append('remarks', remark);

            try {
                const btn = status === 'OK' ? markOkBtn : markIncorrectBtn;
                const originalText = btn.innerHTML;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Saving...';
                btn.disabled = true;

                const response = await fetch(updateFileUrl, {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success) {
                    documentsList[currentIndex].status = status;
                    documentsList[currentIndex].remarks = remark;
                    
                    setButtonAsMarked(status);

                    if(status === 'OK') {
                        modalAlert.innerHTML = '<i class="fas fa-check mr-2"></i> Marked as OK';
                        modalAlert.className = 'absolute top-4 left-1/2 transform -translate-x-1/2 bg-green-500 text-white px-4 py-2 rounded shadow-lg font-bold text-sm z-10 transition';
                    } else {
                        modalAlert.innerHTML = '<i class="fas fa-info-circle mr-2"></i> Tagged as Incorrect';
                        modalAlert.className = 'absolute top-4 left-1/2 transform -translate-x-1/2 bg-red-500 text-white px-4 py-2 rounded shadow-lg font-bold text-sm z-10 transition';
                    }
                    
                    modalAlert.classList.remove('hidden');
                    setTimeout(() => modalAlert.classList.add('hidden'), 3000);

                    const badgeContainer = document.getElementById('doc-badge-' + currentIndex);
                    if (badgeContainer) {
                        if(status === 'OK') {
                            badgeContainer.innerHTML = '<span class="px-2 py-0.5 bg-green-100 text-green-700 text-[10px] uppercase tracking-wider font-bold rounded"><i class="fas fa-check-circle mr-1"></i> OK</span>';
                        } else {
                            badgeContainer.innerHTML = '<span class="px-2 py-0.5 bg-red-100 text-red-700 text-[10px] uppercase tracking-wider font-bold rounded"><i class="fas fa-exclamation-circle mr-1"></i> Incorrect File</span>';
                        }
                    }

                    const remarkContainer = document.getElementById('doc-remark-' + currentIndex);
                    if (remarkContainer) {
                        if (status === 'Incorrect' && remark !== '') {
                            remarkContainer.innerText = 'Remark: ' + remark;
                            remarkContainer.classList.remove('hidden');
                        } else {
                            remarkContainer.classList.add('hidden');
                            remarkContainer.innerText = '';
                        }
                    }
                } else {
                    alert("Error updating document: " + (data.error || "Unknown Error"));
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                }
            } catch (error) {
                console.error('Error:', error);
                alert("A connection error occurred. Please try again.");
                setButtonsAsUnmarked();
            }
        }
    </script>
</body>
</html>
