<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoriesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum')->only(['store', 'update', 'destroy']);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tree = Category::get()->toTree();
        return response()->json([
            'data' => $tree
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string'
        ]);

        $newCategory = new Category();
        $newCategory->name = $request->name;
        if (!$request->parent) {
            $newCategory->saveAsRoot();
            return response()->json([
                'message' => 'Category saved as root'
            ]);
        }

        $parent = Category::find($request->parent);
        $parent->appendNode($newCategory);
        return response()->json([
            'message' => "Category saved as child to $parent->name"
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(int $id)
    {
        $categoryTree = Category::descendantsAndSelf($id)->toTree();
        return response()->json([
            'data' => $categoryTree
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Category  $category
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string'
        ]);
        $category->name = $request->name;
        if ($request->parent) {
            $category->parent_id = $request->parent;
        }
        $category->save();
        return response()->json([
            'message' => "Category edited successfully"
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Category  $category
     * @return \Illuminate\Http\Response
     */
    public function destroy(Category $category)
    {
        $category->delete();
        return response()->json([
            'message' => 'Category deleted successfully'
        ]);
    }
}
