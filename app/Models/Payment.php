<?php

// app/Models/Payment.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'project_id',
        'package_id',
        'buyer_name',
        'buyer_email',
        'buyer_phone',
        'amount',
        'status'
    ];

    public function project() {
        return $this->belongsTo(ProjectDemo::class, 'project_id');
    }

    public function package() {
        return $this->belongsTo(Package::class, 'package_id');
    }
}

