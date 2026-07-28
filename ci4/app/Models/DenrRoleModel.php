<?php

namespace App\Models;

use CodeIgniter\Model;

class DenrRoleModel extends Model
{
    protected $table            = 'denr_roles';
    protected $primaryKey       = 'role_id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = false;
    protected $useTimestamps    = false;

    protected $allowedFields = [
        'office_level',
        'role_name',
        'description',
    ];
}
