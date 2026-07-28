<?php

namespace App\Models;

use CodeIgniter\Model;

class PermitRequirementFileModel extends Model
{
    protected $table            = 'permit_requirements_files';
    protected $primaryKey       = 'file_id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = false;
    protected $useTimestamps    = false;

    protected $allowedFields = [
        'app_id',
        'requirement_id',
        'file_path',
        'status',
        'remarks',
    ];
}
