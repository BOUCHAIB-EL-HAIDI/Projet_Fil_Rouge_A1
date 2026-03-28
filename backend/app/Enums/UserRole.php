<?php

namespace App\Enums;

enum UserRole: string
{

    
    case DEMANDEUR = 'demandeur';
    case MANAGER = 'manager';
    case DIRECTEUR = 'directeur';
    case ACHETEUR = 'acheteur';
}
