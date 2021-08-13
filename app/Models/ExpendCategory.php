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
        return self::select('name')->where('created_by', Auth::id())->get()->toArray();
    }

    /**
     * Get the category id from name for a given user.
     * @param $name
     * @param $userId
     * @return int
     */
    public static function getCategoryIdFromName($name, $userId): int
    {
        $result = self::where([
            ['name', $name],
            ['created_by', $userId]
        ])->first();
        return empty($result) ? 0 : $result->id;
    }

    /**
     * Returns the category name from the category id.
     * @param $categoryId
     * @return string
     */
    public static function getCategoryNameFromId($categoryId): string
    {
        $result = self::where([
            ['id', $categoryId]
        ])->first();
        return empty($result) ? '' : $result->name;
    }
}
