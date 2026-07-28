<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Application #<?= str_pad($app['app_id'], 5, '0', STR_PAD_LEFT) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="icon" href="<?= base_url('logo/denr_logo.png') ?>" type="image/x-icon">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;800&display=swap');
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 p-8">

    <div class="max-w-4xl mx-auto">
        <div class="mb-6 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="<?= base_url('/client/applications') ?>" class="h-10 w-10 bg-white rounded-full flex items-center justify-center text-gray-500 hover:text-blue-600 hover:bg-blue-50 border border-gray-200 transition shadow-sm">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Application Details</h1>
                    <p class="text-sm text-gray-500">Business: <?= esc($app['business_name']) ?></p>
                </div>
            </div>
            <div>
                <?php 
                    $statusColor = 'bg-yellow-100 text-yellow-800';
                    if($app['status'] == 'Returned') $statusColor = 'bg-red-100 text-red-800';
                    if($app['status'] == 'Approved') $statusColor = 'bg-green-100 text-green-800';
                ?>
                <span class="px-4 py-1.5 rounded-full text-sm font-bold border <?= $statusColor ?>">
                    Status: <?= esc($app['status']) ?>
                </span>
            </div>
        </div>

        <?php if (session()->getFlashdata('success')): ?>
            <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg flex items-center gap-3 shadow-sm">
                <i class="fas fa-check-circle text-lg"></i>
                <span class="font-semibold"><?= session()->getFlashdata('success') ?></span>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($error_msg)): ?>
            <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg flex items-center gap-3 shadow-sm">
                <i class="fas fa-exclamation-triangle text-lg"></i>
                <span class="font-semibold"><?= $error_msg ?></span>
            </div>
        <?php endif; ?>

        <?php if($app['status'] === 'Returned' && $return_log): ?>
            <div class="mb-8 bg-red-50 border-l-4 border-red-500 p-6 rounded-r-lg shadow-sm">
                <h3 class="text-red-800 font-bold text-lg flex items-center mb-2">
                    <i class="fas fa-exclamation-circle mr-2"></i> Action Required: Application Returned
                </h3>
                <p class="text-red-700 text-sm mb-4">Your application was returned for the following reason:</p>
                <div class="bg-white p-4 rounded border border-red-200 text-gray-800 text-sm italic">
                    "<?= nl2br(esc($return_log['remarks'])) ?>"
                </div>
                <p class="text-red-700 text-xs mt-3">Please review the documents marked as <strong>Incorrect</strong> below, upload the correct files, and resubmit.</p>
            </div>
        <?php endif; ?>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                <h3 class="font-bold text-gray-800"><i class="fas fa-folder-open text-blue-500 mr-2"></i> Submitted Requirements</h3>
            </div>
            
            <form action="<?= base_url('/client/update-application/' . $app['app_id']) ?>" method="POST" enctype="multipart/form-data" class="p-0 m-0">
                <?= csrf_field() ?>
                <input type="hidden" name="resubmit_application" value="1">
                <ul class="divide-y divide-gray-100">
                    <?php foreach($requirements as $req): ?>
                        <li class="p-6 hover:bg-gray-50 transition <?= ($req['status'] === 'Incorrect') ? 'bg-red-50/30' : '' ?>">
                            <div class="flex flex-col md:flex-row md:items-start justify-between gap-4">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-1">
                                        <h4 class="font-semibold text-gray-800 text-sm"><?= esc($req['requirement_name']) ?></h4>
                                        
                                        <?php if($req['status'] === 'OK'): ?>
                                            <span class="px-2 py-0.5 bg-green-100 text-green-700 text-[10px] uppercase tracking-wider font-bold rounded"><i class="fas fa-check-circle mr-1"></i> Verified OK</span>
                                        <?php elseif($req['status'] === 'Incorrect'): ?>
                                            <span class="px-2 py-0.5 bg-red-100 text-red-700 text-[10px] uppercase tracking-wider font-bold rounded"><i class="fas fa-times-circle mr-1"></i> Incorrect File</span>
                                        <?php else: ?>
                                            <span class="px-2 py-0.5 bg-gray-100 text-gray-600 text-[10px] uppercase tracking-wider font-bold rounded"><i class="fas fa-clock mr-1"></i> Under Review</span>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="text-xs text-gray-500 mb-2">
                                        Current File: <a href="<?= base_url('/') . esc($req['file_path']) ?>" target="_blank" class="text-blue-600 hover:underline">View Uploaded File</a>
                                    </div>

                                    <?php if($req['status'] === 'Incorrect'): ?>
                                        <div class="mt-3 p-3 bg-red-100/50 border border-red-200 rounded text-sm text-red-800">
                                            <strong class="block text-xs uppercase tracking-wider text-red-600 mb-1">Evaluator Remark:</strong>
                                            <?= esc($req['remarks'] ?? 'File is incorrect. Please upload the correct document.') ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <?php if($app['status'] === 'Returned' && $req['status'] === 'Incorrect'): ?>
                                    <div class="md:w-1/3 mt-2 md:mt-0">
                                        <label class="block text-xs font-bold text-gray-700 mb-1">Upload Replacement File (PDF/Image)</label>
                                        <input type="file" name="requirements[<?= $req['requirement_id'] ?>]" accept=".pdf,.png,.jpg,.jpeg" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 border border-gray-200 rounded cursor-pointer bg-white" required>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>

                <?php if($app['status'] === 'Returned' && $has_incorrect_files): ?>
                    <div class="px-6 py-5 bg-gray-50 border-t border-gray-200 flex justify-end">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 px-8 rounded-lg transition shadow-md flex items-center">
                            <i class="fas fa-paper-plane mr-2"></i> Resubmit Application
                        </button>
                    </div>
                <?php endif; ?>
            </form>
        </div>
    </div>

</body>
</html>
