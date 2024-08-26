<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\helpers\BaseController;
use App\Http\Requests\Customer\StoreRequest;
use App\Http\Requests\Customer\UpdateRequest;
use App\Models\Customer;
use Exception;
use Illuminate\Support\Facades\Hash;

class CustomerController extends BaseController
{
    public function index()
    {
        $customers = Customer::all();
        return $this->sendResponse($customers, "Lista de clientes");
    }

    public function store(StoreRequest $request)
    {
        try {
            $validated = $request->validated();
    
            $validated['password'] = Hash::make($validated['password']);
    
            $customer = Customer::create($validated);
            return $this->sendResponse($customer, 'Cliente creado con exito', 'succcess', 201);
        } catch (Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

   
    public function show(Customer $customer)
    {
        return $this->sendResponse($customer, "Cliente encontrado con exito");
    }

    public function update(UpdateRequest $request, Customer $customer)
{
    try {
        $validated = $request->validated();
        
        $currentData = $customer->only(['image']);
        
        $updatedData = array_merge($currentData, array_filter($validated, function ($value) {
            return $value !== null;
        }));
        $customer->update($updatedData);
        
        return $this->sendResponse($customer, 'Cliente actualizado con éxito');
    } catch (\Illuminate\Database\QueryException $e) {
        return $this->sendError('Error de base de datos: ' . $e->getMessage());
    } catch (Exception $e) {
        return $this->sendError('Error: ' . $e->getMessage());
    }
}
    

    public function destroy(Customer $customer)
    {
        try {
            $customer->delete();
            return $this->sendResponse([], "Cliente eliminado exitosamente");
        } catch (Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }
}