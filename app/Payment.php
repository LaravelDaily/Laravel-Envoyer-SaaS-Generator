<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = ['user_id', 'plan_id', 'paid_amount', 'original_amount', 'discount_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function plan()
    {
        return $this->belongsTo(Role::class, 'plan_id', 'id');
    }

    public function getAmountAttribute()
    {
        return config('saas.currency') . number_format($this->paid_amount / 100, 2);
    }
}
