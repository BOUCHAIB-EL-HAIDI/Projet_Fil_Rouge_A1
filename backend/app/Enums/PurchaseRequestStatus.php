<?php

namespace App\Enums;

enum PurchaseRequestStatus: string
{
    case DRAFT = 'draft';
    case PENDING_MANAGER = 'pending_manager';
    case PENDING_DIRECTEUR = 'pending_directeur';
    case APPROVED = 'approved';
    case IN_PROGRESS = 'in_progress';
    case ORDERED = 'ordered';
    case CONSULTATION = 'consultation';
    case DELIVERED = 'delivered';
    case REJECTED = 'rejected';
    case CANCELLED = 'cancelled';
}
