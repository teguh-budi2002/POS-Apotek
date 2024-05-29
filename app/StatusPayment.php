<?php

namespace App;

enum StatusPayment: string
{
    case Paid = "Paid";
    case Due = "Due";
    case Late = "Late";
}
