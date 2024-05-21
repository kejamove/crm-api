<?php
namespace App\Enums;

enum InvoiceStatus: string
{
    case pending = 'pending';
    case paid = 'paid';
}
