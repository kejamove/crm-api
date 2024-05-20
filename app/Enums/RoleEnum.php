<?php
namespace App\Enums;

enum RoleEnum: string
{
    case super_admin = 'super_admin'; // SUPER ADMIN
    case firm_owner = 'firm_owner';
    case branch_manager = 'branch_manager';
    case project_manager = 'project_manager';
    case sales = 'sales';
    case marketing = 'marketing';
}

?>
