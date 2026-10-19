<?php

namespace App\Enums;

enum StockMovementType: string
{
    case Initial = 'initial';
    case Sale = 'sale';
    case CancelReturn = 'cancel_return';
    case Adjustment = 'adjustment';
}
