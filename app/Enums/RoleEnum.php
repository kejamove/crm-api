<?php
namespace App\Enums;

enum RoleEnum: string
{
    case client = 'client';
    case store_owner = 'store_owner';
    case project_manager = 'project_manager';
    case sales = 'sales';
    case marketing = 'marketing';
    case admin = 'admin';
}

?>