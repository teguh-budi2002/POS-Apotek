<?php

namespace App;

enum PaymentMethod: string
{
    case BankTF = 'Bank Transfer';
    case Credit = 'Credit';
    case Cash   = 'Cash';
}
