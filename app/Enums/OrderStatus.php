<?php

namespace App\Enums;

enum OrderStatus: string
{
    case Paid = 'paid';
    case Pending = 'pending';
    case Expired = 'expired';
    case Failed = 'failed';
    case Cancelled = 'cancelled';
}
