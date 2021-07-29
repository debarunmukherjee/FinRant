<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlanCategoryBudget extends Model
{
    use HasFactory;

    protected $fillable = [
        'amount',
        'category_id',
        'user_id',
        'plan_id',
    ];
}
