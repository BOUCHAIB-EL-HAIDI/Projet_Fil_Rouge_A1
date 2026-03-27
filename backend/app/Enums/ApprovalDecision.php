<?php

namespace App\Enums;

enum ApprovalDecision: string
{
    case APPROVE = 'approved';
    case REJECT = 'rejected';
}
