<?php

namespace App\Models;

use CodeIgniter\Model;

class DenrUserModel extends Model
{
    protected $table            = 'denr_users';
    protected $primaryKey       = 'user_id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = false;
    protected $useTimestamps    = false;

    protected $allowedFields = [
        'name',
        'username',
        'password',
        'usertype',
        'contact_no',
        'office_id',
        'role_id',
        'user_role_id',
        'uploadSignature',
        'unhashPassword',
    ];
}
