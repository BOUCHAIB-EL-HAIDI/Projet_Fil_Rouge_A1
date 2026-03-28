<?php

namespace App\Enums;

enum PurchaseRequestPriority: string
{
    case LOW = 'low';
    case MEDIUM = 'medium';
    case HIGH = 'high';
}
