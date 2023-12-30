<?php

namespace App\Http\Controllers\Api\V1;

use App\Filters\API\V1\SupplierFilter;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\SupplierStoreRequest;
use App\Http\Requests\Api\V1\SupplierUpdateRequest;
use App\Http\Resources\Api\V1\SupplierCollection;
use App\Http\Resources\Api\V1\SupplierResource;
use App\Models\Supplier;
use App\Models\User;
use App\Notifications\SendWelcomeSupplierNotification;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class SupplierController extends Controller
{
    use HttpResponses;

    public function index(Request $request)
    {
        $filter = new SupplierFilter();
        $queryFilter = $filter->transform($request);

        $supplier = Supplier::query();

        if (count($queryFilter) == 0) {
            $supplier = $supplier->with('user');
        } else {
            $supplier = $supplier->whereHas('user', function ($query) use ($queryFilter) {
                $query->where($queryFilter);
            });
        }
        return new SupplierCollection($supplier->paginate()->appends($request->query()));
    }

    public function store(SupplierStoreRequest $request)
    {
        try {
            $password = $request->generatePassword();

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($password),
                'role' => 'supplier',
                'status' => $request->status,
            ]);

            // sand notification
            $user->notify(
                new SendWelcomeSupplierNotification(
                    $user,
                    $password
                )
            );

            $supplier = new Supplier(['user_id' => $user]);
            $data = $user->suppliers()->save($supplier);
            return $this->sendSuccess(trans('crud.create', ['model' => 'supplier']), new SupplierResource($data), 200);

        } catch (\Exception $e) {
            return $this->sendError(trans('api.400'), ["error" => $e->getMessage()], 400);
        }

    }

    public function show($supplier)
    {
        $supplierDetail = Supplier::with('user')->find($supplier);

        if (!$supplierDetail)
            return $this->sendError(trans('api.400'), ["error" => trans('crud.notFound')], 400);

        return new SupplierResource($supplierDetail);
    }

    public function update(SupplierUpdateRequest $request, Supplier $supplier)
    {
        try {
            $supplier->user()->update([
                'name' => $request->name ?? $supplier->user->name,
                'status' => $request->status ?? $supplier->user->status,
            ]);
            return $this->sendSuccess(trans('crud.update', ['model' => 'supplier']), new SupplierResource($supplier));
        } catch (\Exception $e) {
            return $this->sendError(trans('api.400'), ["error" => $e->getMessage()]);
        }

    }
    public function destroy($supplier)
    {

        $supplierDetail = Supplier::with('user')->find($supplier);

        if (!$supplierDetail)
            return $this->sendError(trans('api.400'), ["error" => trans('crud.notFound')], 400);

        try {
            if (Supplier::destroy($supplierDetail->id))
                User::destroy($supplierDetail->user->id);
            return $this->sendSuccess(trans('crud.delete', ['model' => 'supplier']), new SupplierResource($supplierDetail));

        } catch (\Exception $e) {
            return $this->sendError(trans('api.400'), ["error" => $e->getMessage()], "401");
        }
    }

}