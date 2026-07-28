<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Applications | O-LDPMS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;800&display=swap');
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 p-8">
    
    <div class="mb-8 flex justify-between items-end">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Permit Applications</h1>
            <p class="text-gray-500 mt-1 text-sm">Manage and review submitted applications under your office's jurisdiction.</p>
        </div>
        <div>
            <button class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg shadow-sm text-sm font-semibold transition">
                <i class="fas fa-download mr-2"></i> Export List
            </button>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-emerald-50 text-emerald-800 text-xs uppercase tracking-wider border-b border-emerald-100">
                        <th class="px-6 py-4 font-bold">App ID</th>
                        <th class="px-6 py-4 font-bold">Business Name</th>
                        <th class="px-6 py-4 font-bold">Applicant Name</th>
                        <th class="px-6 py-4 font-bold">Type</th>
                        <th class="px-6 py-4 font-bold">Municipality / Office</th>
                        <th class="px-6 py-4 font-bold">Date Submitted</th>
                        <th class="px-6 py-4 font-bold text-center">Status</th>
                        <th class="px-6 py-4 font-bold text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    <?php if (count($applications) > 0): ?>
                        <?php foreach ($applications as $app): ?>
                            <tr class="hover:bg-gray-50 transition duration-150">
                                <td class="px-6 py-4 text-gray-500 font-medium whitespace-nowrap">
                                    #<?= str_pad($app['app_id'], 5, '0', STR_PAD_LEFT) ?>
                                </td>
                                <td class="px-6 py-4 text-gray-900 font-bold">
                                    <?= htmlspecialchars($app['business_name']) ?>
                                </td>
                                <td class="px-6 py-4 text-gray-600">
                                    <?= htmlspecialchars($app['firstname'] . ' ' . $app['lastname']) ?>
                                </td>
                                <td class="px-6 py-4 text-gray-600">
                                    <span class="bg-blue-50 text-blue-600 border border-blue-100 px-2 py-1 rounded text-xs font-semibold">
                                        <?= htmlspecialchars($app['app_type']) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-gray-600">
                                    <div class="font-semibold text-gray-700"><?= htmlspecialchars($app['muncity_name'] ?? 'N/A') ?></div>
                                    <div class="text-xs text-emerald-600 mt-0.5"><?= htmlspecialchars($app['office_cover'] ?? 'N/A') ?></div>
                                </td>
                                <td class="px-6 py-4 text-gray-600 whitespace-nowrap">
                                    <?= date('M d, Y', strtotime($app['date_submitted'])) ?><br>
                                    <span class="text-xs text-gray-400"><?= date('h:i A', strtotime($app['date_submitted'])) ?></span>
                                </td>
                                <td class="px-6 py-4 text-center whitespace-nowrap">
                                    <?php 
                                        $status = strtolower($app['status']);
                                        $statusClass = 'bg-yellow-100 text-yellow-800 border border-yellow-200';
                                        
                                        if (in_array($status, ['approved', 'completed', 'issued'])) {
                                            $statusClass = 'bg-green-100 text-green-800 border border-green-200';
                                        } elseif (in_array($status, ['rejected', 'returned'])) {
                                            $statusClass = 'bg-red-100 text-red-800 border border-red-200';
                                        }
                                    ?>
                                    <span class="px-3 py-1 rounded-full text-xs font-bold <?= $statusClass ?>">
                                        <?= htmlspecialchars($app['status']) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <a href="<?= base_url('/admin/view-application/') . $app['app_id'] ?>" class="inline-flex items-center justify-center text-emerald-600 hover:text-white bg-emerald-50 hover:bg-emerald-600 border border-emerald-200 px-3 py-1.5 rounded-lg transition text-xs font-bold shadow-sm">
                                        <i class="fas fa-search mr-1.5"></i> Review
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-slate-100 mb-4">
                                    <i class="fas fa-folder-open text-2xl text-slate-400"></i>
                                </div>
                                <p class="text-lg font-semibold text-gray-700">No applications found.</p>
                                <p class="text-sm text-gray-400 mt-1">There are no pending applications associated with your office.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
