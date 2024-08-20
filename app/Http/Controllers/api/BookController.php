<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\helpers\BaseController;
use App\Http\Requests\Book\StoreRequest;
use App\Http\Requests\Book\UpdateRequest;
use App\Http\Resources\AuthorResource;
use App\Http\Resources\BookResource;
use App\Models\Author;
use App\Models\Book;
use Exception;
use Illuminate\Support\Facades\Auth;

class BookController extends BaseController
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
            $books = Book::with('editor', 'creator')->get();
            return $this->sendResponse(BookResource::collection($books), 'Listo de categorías');
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
            $book = Book::create($validated);
            return $this->sendResponse($book, 'Libro creado exitosamente', 'success', 201);

        }catch(Exception $e){
            return $this->sendError($e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Book $book)
    {
        try{
            return $this->sendResponse($book, 'Libro encontrado exitosamente');
        }catch(Exception $e){
            return $this->sendError($e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, Author $book)
    {
        try {
            $validated = $request->validated();
            $validated['updated_by'] = Auth::id();
            $book->update($validated);
            return $this->sendResponse($book, 'Libro actualizado exitosamente');
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
                return $this->sendResponse([], 'Categoria eliminada exitosamente');
            }
        }
        catch(Exception $e)
        {
            return $this->sendError($e->getMessage());
        }
        
    }
}