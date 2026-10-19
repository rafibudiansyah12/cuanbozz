<?php

namespace App\Enums;

enum StoreRole: string
{
    case Owner = 'owner';
    case Admin = 'admin';
    case Cashier = 'cashier';
}
