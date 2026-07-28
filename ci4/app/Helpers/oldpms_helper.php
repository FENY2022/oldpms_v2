<?php

function format_app_id($id) {
    return '#' . str_pad($id, 5, '0', STR_PAD_LEFT);
}

function get_status_badge_class($status) {
    $status = strtolower($status);
    if (in_array($status, ['approved', 'completed', 'issued'])) {
        return 'bg-green-100 text-green-800 border border-green-200';
    } elseif (in_array($status, ['rejected', 'returned'])) {
        return 'bg-red-100 text-red-800 border border-red-200';
    } elseif (in_array($status, ['under evaluation', 'pending review'])) {
        return 'bg-yellow-100 text-yellow-800 border border-yellow-200';
    }
    return 'bg-gray-100 text-gray-800 border border-gray-200';
}

function is_admin_role($role_id) {
    $admin_role_ids = ['12', '13', '14', '15', '27', '28', '29', '30', 'Admin'];
    return in_array((string)$role_id, $admin_role_ids);
}

function is_system_admin_role($role_id) {
    $system_admin_roles = ['15', '30', 'Admin'];
    return in_array((string)$role_id, $system_admin_roles, true);
}
