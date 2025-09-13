<?php

namespace App\Enum;

enum PermissionEnum: int
{
    // User Permissions
    case ViewUsers = 1;
    case CreateUsers = 2;
    case UpdateUsers = 3;
    case DeleteUsers = 4;

    // Systems Permission
    case ViewSystemData = 5;
    case AddSystemData = 6;
    case UpdateSystemData = 7;
    case DeleteSystemData = 8;


        public static function fromName(string $name): self
    {
        foreach (self::cases() as $case) {
            if ($case->name === $name) {
                return $case;
            }
        }

        throw new \InvalidArgumentException("Invalid permission name: $name");
    }

}
