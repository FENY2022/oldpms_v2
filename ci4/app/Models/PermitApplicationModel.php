<?php

namespace App\Models;

use CodeIgniter\Model;

class PermitApplicationModel extends Model
{
    protected $table            = 'permit_applications';
    protected $primaryKey       = 'app_id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = false;
    protected $useTimestamps    = false;

    protected $allowedFields = [
        'client_id',
        'app_type',
        'applicant_type',
        'business_name',
        'tin_number',
        'reference_number',
        'province_id',
        'muncity_id',
        'brgy_id',
        'zip_code',
        'street_address',
        'status',
        'date_submitted',
    ];
}
