<?php

namespace App\Http\Controllers;

use App\Http\Requests\Category\StoreRequest;
use App\Http\Requests\Category\UpdateRequest;
use App\Models\Category;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }
    public function index()
    {
        try {
            $categories = Category::all();
            return response()->json([
                'messages' => 'Lisa de categorias',
                'category' => $categories,
                'type' => 'success'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'messages' => $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request)
    {
        try {
            $category = new Category();
            $category->name = $request->name;
            $category->description = $request->description;
            $category->created_by = Auth::id();
            $category->save();

            return response()->json([
                'message' => 'Categoría creada exitosamente',
                'category' => $category,
                'type' => 'success'
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'type' => 'error'
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        return response()->json([
            'category' => $category,
            'type' => 'success'
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, Category $category)
    {
        try {
            $category->name = $request->name;
            $category->description = $request->description;
            $category->updated_by = Auth::id();
            $category->save();

            return response()->json([
                'message' => 'Categoría actualizada exitosamente',
                'category' => $category,
                'type' => 'success'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'type' => 'error'
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        try {
            $category->delete();
            return response()->json([
                'message' => 'Categoría eliminada exitosamente',
                'type' => 'success'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'type' => 'error'
            ], 500);
        }
    }
}
