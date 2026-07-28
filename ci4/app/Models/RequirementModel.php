<?php

namespace App\Models;

use CodeIgniter\Model;

class RequirementModel extends Model
{
    protected $table            = 'requirements';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = false;
    protected $useTimestamps    = false;

    protected $allowedFields = [
        'requirement_name',
        'download_link',
        'new_app_status',
        'renewal_app_status',
        'sequence',
    ];

    protected $defaultOrder = ['sequence' => 'ASC'];
}
