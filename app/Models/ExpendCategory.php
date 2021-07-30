<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ExpendCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'created_by'
    ];

    public static function getAllCategoriesForCurrentUser()
    {
        return self::select('name')->where('created_by', Auth::id())->get();
    }
}
