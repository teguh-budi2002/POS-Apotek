<?php

namespace App;

enum StatusReturn: string
{
    case PENDING = "Pending";
    case COMPLETED = "Completed";
    case REJECTED = "Rejected";
}
