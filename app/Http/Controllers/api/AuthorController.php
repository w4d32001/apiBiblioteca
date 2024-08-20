<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\helpers\BaseController;
use App\Http\Requests\Author\StoreRequest;
use App\Http\Requests\Author\UpdateRequest;
use App\Http\Resources\AuthorResource;
use App\Models\Author;
use Exception;
use Illuminate\Support\Facades\Auth;

class AuthorController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try{
            $authors = Author::with('editor', 'creator')->get();
            return $this->sendResponse(AuthorResource::collection($authors), 'Lista de Autores');
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
            $validated['updated_by'] = Auth::id();
            $author = Author::create($validated);
            
            return $this->sendResponse($author, 'Autor creado exitosamente', 'success', 201);

        }catch(Exception $e){
            return $this->sendError($e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Author $author)
    {
        try{
            return $this->sendResponse($author, 'Autor encontrado exitosamente');
        }catch(Exception $e){
            return $this->sendError($e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, Author $author)
    {
        try {
            $validated = $request->validated();
            $validated['updated_by'] = Auth::id();
            $author->update($validated);
            return $this->sendResponse($author, 'Autor actualizado exitosamente');
        } catch (Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Author $author)
    {
        try
        {
            if($author->books()->exists())
            {
                return $this->sendError('Este dato tiene otro dato asociado, no se puede eliminar');
            }
            else{
                $author->delete();
                return $this->sendResponse([], 'Autor eliminado exitosamente');
            }
        }
        catch(Exception $e)
        {
            return $this->sendError($e->getMessage());
        }
    }
}