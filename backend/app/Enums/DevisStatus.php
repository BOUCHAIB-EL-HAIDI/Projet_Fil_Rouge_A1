<?php

namespace App\Enums;

enum DevisStatus: string
{
    case PENDING = 'pending';
    case RECEIVED = 'received';
    case SELECTED = 'selected';
    case REJECTED = 'rejected';
}
