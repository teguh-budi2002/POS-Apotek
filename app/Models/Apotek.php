<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Apotek extends Model
{
    use HasFactory;

    protected $fillable = [
        'name_of_apotek',
        'email',
        'contact_phone',
        'city',
        'province',
        'zip_code',
        'address',
        'bio'
    ];
}
