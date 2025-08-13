<?php

namespace App\Enum;

enum PermissionEnum: string
{
    // User Permissions
    case ViewUsers = 'view-users';
    case CreateUsers = 'create-users';
    case UpdateUsers = 'update-users';
    case DeleteUsers = 'delete-users';

    // Systems Permission
    case ViewSystemData = 'view-system-data';
    case AddSystemData = 'add-system-data';
    case UpdateSystemData = 'update-system-data';
    case DeleteSystemData = 'delete-system-data';

}
