<?php

namespace App\Models;

use CodeIgniter\Model;

class MuncityModel extends Model
{
    protected $table            = 'muncity';
    protected $primaryKey       = 'muncity_id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = false;
    protected $useTimestamps    = false;

    protected $allowedFields = [
        'muncity_name',
        'prov_code',
        'mun_code',
        'zip_code',
        'office_id',
        'office_cover',
    ];
}
