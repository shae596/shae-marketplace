<?php

namespace App\Enums;

enum UserRole: string
{
    case Admin = 'admin';
    case Gestionnaire = 'gestionnaire';
    case Client = 'client';

    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Administrateur',
            self::Gestionnaire => 'Gestionnaire',
            self::Client => 'Client',
        };
    }

    public function dashboardRoute(): string
    {
        return match ($this) {
            self::Admin => 'admin.dashboard',
            self::Gestionnaire => 'gestionnaire.dashboard',
            self::Client => 'home',
        };
    }
}
