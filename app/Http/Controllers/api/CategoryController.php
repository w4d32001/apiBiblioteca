<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\helpers\BaseController;
use App\Http\Requests\Category\StoreRequest;
use App\Http\Requests\Category\UpdateRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CategoryController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try{
            $categories = Category::with('editor', 'creator')->get();
            return $this->sendResponse(CategoryResource::collection($categories), 'Lista de categorías');
        }catch(Exception $e){
            return $this->sendError($e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request)
    {
        try{
            $validated = $request->validated();
            $validated['created_by'] = Auth::id();
            $validated['updated_by'] = $validated['created_by'];
            $category = Category::create($validated);
            
            return $this->sendResponse($category, 'Categoria creada exitosamente', 'success', 201);

        }catch(Exception $e){
            return $this->sendError($e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        try{
            return $this->sendResponse($category, 'Categoria encontrada exitosamente');
        }catch(Exception $e){
            return $this->sendError($e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, Category $category)
    {
        try {
            $validated = $request->validated();
            $validated['updated_by'] = Auth::id();
            $category->update($validated);
            return $this->sendResponse($category, 'Categoria actualizada exitosamente');
        } catch (Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        try
        {
            if($category->books()->exists())
            {
                return $this->sendError('Este dato tiene otro dato asociado, no se puede eliminar');
            }
            else{
                $category->delete();
                return $this->sendResponse([], 'Categoria eliminada exitosamente');
            }
        }
        catch(Exception $e)
        {
            return $this->sendError($e->getMessage());
        }
    }
}