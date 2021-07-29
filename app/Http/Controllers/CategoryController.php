<?php

namespace App\Http\Controllers;

use App\Models\ExpendCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    public function createCategory(Request $request)
    {
        $userId = Auth::id();
        $categoryName = $request->post('name');

        $request->validate(
            [
                'name' => [
                    'required',
                    'max:50',
                    'string',
                    Rule::unique('expend_categories')->where(function ($query) use($userId, $categoryName){
                        return $query->where([
                            ['name', $categoryName],
                            ['created_by', $userId]
                        ]);
                    })
                ]
            ],
            [
                'name.unique' => 'Another category with the same name has been created.'
            ]
        );

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
