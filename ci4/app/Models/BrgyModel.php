<?php

namespace App\Models;

use CodeIgniter\Model;

class BrgyModel extends Model
{
    protected $table            = 'brgy';
    protected $primaryKey       = 'brgy_id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = false;
    protected $useTimestamps    = false;

    protected $allowedFields = [
        'brgy_name',
        'mun_code',
        'brgy_code',
    ];
}
