<?php

namespace App\Http\Controllers;

use App\Models\ExpendCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class CategoryController extends Controller
{
    public function createCategory(Request $request)
    {
        $userId = Auth::id();
        $categoryName = $request->post('categoryName');

        $request->validate([
            'categoryName' => [
                'required',
                'max:50',
                'string',
                function ($attribute, $value, $fail) use($userId) {
                    $categoryExists = ExpendCategory::where([
                                            ['name', $value],
                                            ['created_by', $userId]
                                        ])->exists();
                    if ($categoryExists) {
                        $fail('Another category with the same name has been created.');
                    }
                },
            ]
        ]);

        $category = ExpendCategory::create([
            'name' => $categoryName,
            'created_by' => $userId
        ]);

        if (empty($category)) {
            return Redirect::back()->with('error', 'Could not create category');
        }
        return Redirect::back()->with('success', 'Category successfully created');
    }
}
