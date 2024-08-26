<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\helpers\BaseController;
use App\Http\Requests\Sale\StoreRequest;
use App\Http\Resources\SaleResource;
use App\Models\Book;
use App\Models\Sale;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SaleController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $users = Sale::all();
            return $this->sendResponse(SaleResource::collection($users), 'Lista de usuarios');
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
            $sale = Sale::create($validated);

            // Obtener el libro relacionado a la venta
            $book = $sale->book; // Asegúrate de que 'book' sea la relación correcta

            // Generar la URL para descargar el PDF
            $pdfUrl = url("/api/books/{$book->id}/download-pdf");

            // Agregar la URL del PDF a la respuesta
            return $this->sendResponse([
                'pdf_url' => $pdfUrl,
            ], 'Venta creada exitosamente', 'success', 201,);
        } catch (Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(Sale $sale) {}

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
    public function getSalesByCustomer($customerId)
    {
        try {
            $sales = Sale::where('customer_id', $customerId)->get();

            return $this->sendResponse(SaleResource::collection($sales), 'Ventas encontradas para el cliente');
        } catch (Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }
    public function downloadPdf($id)
{
    try {
        // Buscar el libro por su ID
        $book = Book::findOrFail($id);

        // Obtener la ruta del PDF almacenado
        $pdfPath = $book->pdf;

        // Verificar si el archivo PDF existe
        if (!Storage::exists($pdfPath)) {
            return $this->sendError('PDF no encontrado');
        }

        // Retornar el PDF como descarga sin eliminar el archivo
        return response()->download(storage_path('app/' . $pdfPath));
    } catch (Exception $e) {
        return $this->sendError($e->getMessage());
    }
}

}