<?php

namespace App\Models;

use CodeIgniter\Model;

class UserClientModel extends Model
{
    protected $table            = 'user_client';
    protected $primaryKey       = 'client_id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = false;
    protected $useTimestamps    = false;

    protected $allowedFields = [
        'firstname',
        'mid_name',
        'lastname',
        'email',
        'profile_picture',
        'verification_token',
        'password',
        'mobilenum',
        'comp_id_upload',
        'govt_id_upload',
        'auth_letter',
        'password_unhashed',
        'Status',
        'province',
        'citymun',
        'brgy',
        'zips',
    ];
}
