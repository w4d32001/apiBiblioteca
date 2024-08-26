<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\helpers\BaseController;
use App\Http\Requests\Company\ImgRequest;
use App\Http\Requests\Company\StoreRequest;
use App\Http\Requests\Company\UpdateRequest;
use App\Models\Company;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class CompanyController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $companies = Company::all();
            return $this->sendResponse($companies, 'Lista de compañias');
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
            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('public/images');
                $imageUrl = Storage::url($path);
                $baseUrl = url('/');
                $validated['image'] = $baseUrl . $imageUrl;
            }

            $company = Company::create($validated);
            return $this->sendResponse($company, 'Compañia exitosamente', 'success', 201);
        } catch (Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Company $company)
    {
        try {
            return $this->sendResponse($company, 'Compañia encontrada exitosamente');
        } catch (Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, Company $company)
    {
        try {
            $validated = $request->validated();
            $company->update($validated);
            return $this->sendResponse($company, 'Dato actualizado',);
        } catch (Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    public function updateImage(ImgRequest $request, Company $company)
    {

        $validated = $request->validated();

        try {
            if ($request->hasFile('image')) {

                $path = $request->file('image')->store('public/images');
                $imageUrl = Storage::url($path);
                $baseUrl = url('/');
                $validated['image'] = $baseUrl . $imageUrl;

                $company->update(['image' => $validated['image']]);
                return $this->sendResponse($company, 'Imagen actualizada exitosamente');
            } else {
                return $this->sendError('No se encontró la imagen en la solicitud.');
            }
        } catch (Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }




    public function destroy(string $id)
    {
        //
    }

    public function hola(Request $request)
    {
        $request->validate([
            "hola" => ['required']
        ]);
    }
}