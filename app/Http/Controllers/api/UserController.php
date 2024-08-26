<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\helpers\BaseController;
use App\Http\Requests\Company\ImgRequest;
use App\Http\Requests\User\StoreRequest;
use App\Http\Requests\User\UpdateRequest;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

use Illuminate\Support\Facades\Storage;

class UserController extends BaseController
{
    public function index()
    {
        try {
            $users = User::all();
            return $this->sendResponse($users, 'Lista de usuarios');
        } catch (Exception $e) {
            return $this->sendError($e->getMessage())    ;
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request)
    {
        try {
            $validated = $request->validated();
            $validated['password'] = Hash::make($validated['password']);
            $user = User::create($validated);
            return $this->sendResponse($user, 'Usuario creado exitosamente','success', 201);
        } catch (Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        try {
            return $this->sendResponse($user,'Usuario encontrado');
        } catch (Exception $e) {
            return $this->sendError($e->getMessage())    ;
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, User $user)
    {
        try {
            $validated = $request->validated();
            if (!empty($validated['password'])) {
                $validated['password'] = Hash::make($validated['password']);
            }else{
                $validated['password'] = $user->password;
            }
            $user->update($validated);
            return $this->sendResponse($user, 'Usuario actualizado correctamente');
        } catch (Exception $e) {
            return $this->sendError($e->getMessage())    ;
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        try {
            $user->delete();
            return $this->sendResponse([], 'Usuario eliminado exitosamente');
        } catch (Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    public function updateImage(ImgRequest $request, User $user)
    {

        $validated = $request->validated();

        try {
            if ($request->hasFile('image')) {

                $path = $request->file('image')->store('public/images');
                $imageUrl = Storage::url($path);
                $baseUrl = url('/');
                $validated['image'] = $baseUrl . $imageUrl;

                $user->update(['image' => $validated['image']]);
                return $this->sendResponse($user, 'Imagen actualizada exitosamente');
            } else {
                return $this->sendError('No se encontró la imagen en la solicitud.');
            }
        } catch (Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }
}