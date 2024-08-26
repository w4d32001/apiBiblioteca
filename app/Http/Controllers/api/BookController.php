<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\helpers\BaseController;
use App\Http\Requests\Book\StoreRequest;
use App\Http\Requests\Book\UpdateRequest;
use App\Http\Requests\Company\ImgRequest;
use App\Http\Resources\BookResource;
use App\Models\Book;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;


class BookController extends BaseController
{

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $books = Book::with('editor', 'creator')->get();
            return $this->sendResponse(BookResource::collection($books), 'Listo de categorías');
        } catch (Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request)
    {
        try {
            $validated = $request->validated();
            $validated['created_by'] = Auth::id();
            $validated['updated_by'] = $validated['created_by'];
            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('public/images');
                $imageUrl = Storage::url($path);
                $baseUrl = url('/');
                $validated['image'] = $baseUrl . $imageUrl;
            }
            if ($request->hasFile('pdf')) {
                $pdfPath = $request->file('pdf')->storeAs('public/files', $request->file('pdf')->getClientOriginalName());
                $validated['pdf'] = $pdfPath;
            }

            $book = Book::create($validated);
            return $this->sendResponse($book, 'Libro creado exitosamente', 'success', 201);
        } catch (Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Book $book)
    {
        try {
            return $this->sendResponse($book, 'Libro encontrado exitosamente');
        } catch (Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function updateBook(UpdateRequest $request, Book $book)
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
    public function destroy(Book $book)
    {
        try {
            $book->delete();
            return $this->sendResponse([], 'Libro eliminado exitosamente');
        } catch (Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    public function updateImage(ImgRequest $request, Book $book)
    {

        $validated = $request->validated();

        try {
            if ($request->hasFile('image')) {

                $path = $request->file('image')->store('public/images');
                $imageUrl = Storage::url($path);
                $baseUrl = url('/');
                $validated['image'] = $baseUrl . $imageUrl;

                $book->update(['image' => $validated['image']]);
                return $this->sendResponse($book, 'Imagen actualizada exitosamente');
            } else {
                return $this->sendError('No se encontró la imagen en la solicitud.');
            }
        } catch (Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }
}