<?php

namespace App\Models;

use CodeIgniter\Model;

class ApplicationLogModel extends Model
{
    protected $table            = 'application_logs';
    protected $primaryKey       = 'log_id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = false;
    protected $useTimestamps    = false;

    protected $allowedFields = [
        'app_id',
        'action',
        'remarks',
        'created_at',
    ];
}
