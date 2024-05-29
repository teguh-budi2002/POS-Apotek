<?php

namespace App;

enum StatusOrder: string
{
    case Pending = "Pending";
    case Shipping = "Shipping";
    case Delivered = "Delivered";
    case Cancelled = "Cancelled";
}
