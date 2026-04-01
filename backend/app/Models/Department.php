<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $fillable = ['name', 'manager_id'];

    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function members()
    {
        return $this->hasMany(User::class);
    }

    public function purchaseRequests()
    {
        return $this->hasMany(PurchaseRequest::class);
    }
}
