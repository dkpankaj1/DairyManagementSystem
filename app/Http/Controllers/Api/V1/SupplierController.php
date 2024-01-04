<?php

namespace App\Http\Controllers\Api\V1;

use App\Filters\Api\V1\SupplierFilter;
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

    /**
     * Retrieve a paginated list of suppliers based on filters.
     *
     * @param Request $request
     * @return SupplierCollection
     */
    public function index(Request $request)
    {
        // Create a SupplierFilter instance to handle filtering
        $filter = new SupplierFilter();
        $queryFilter = $filter->transform($request);

        // Start with the base supplier query
        $supplier = Supplier::query();

        // Check if there are filters; if not, include the 'user' relationship
        if (count($queryFilter) == 0) {
            $supplier = $supplier->with('user');
        } else {
            // Apply filters on the 'user' relationship
            $supplier = $supplier->whereHas('user', function ($query) use ($queryFilter) {
                $query->where($queryFilter);
            });
        }

        // Return a paginated collection with appended query parameters
        return new SupplierCollection($supplier->paginate()->appends($request->query()));
    }

    /**
     * Store a new supplier and send a welcome notification.
     *
     * @param SupplierStoreRequest $request
     * @return mixed
     */
    public function store(SupplierStoreRequest $request)
    {
        try {
            // Generate a password for the new user
            $password = $request->generatePassword();

            // Create a new user with the provided information
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($password),
                'role' => 'supplier',
                'status' => $request->status,
            ]);

            // Send a welcome notification to the new user
            $user->notify(new SendWelcomeSupplierNotification($user, $password));

            // Create a new Supplier associated with the user
            $supplier = new Supplier(['user_id' => $user->id]);
            $data = $user->suppliers()->save($supplier);

            // Return a success response with the created supplier resource
            return $this->sendSuccess(trans('crud.create', ['model' => 'supplier']), new SupplierResource($data), 200);

        } catch (\Exception $e) {
            // Return an error response with the exception message
            return $this->sendError(trans('api.400'), ["error" => $e->getMessage()], 400);
        }
    }

    /**
     * Show the details of a specific supplier.
     *
     * @param int $supplier
     * @return SupplierResource|mixed
     */
    public function show($supplier)
    {
        // Find the supplier details with the 'user' relationship
        $supplierDetail = Supplier::with('user')->find($supplier);

        // If the supplier is not found, return an error response
        if (!$supplierDetail)
            return $this->sendError(trans('api.400'), ["error" => trans('crud.notFound')], 400);

        // Return the supplier details as a resource
        return new SupplierResource($supplierDetail);
    }

    /**
     * Update the details of a specific supplier.
     *
     * @param SupplierUpdateRequest $request
     * @param Supplier $supplier
     * @return mixed
     */
    public function update(SupplierUpdateRequest $request, Supplier $supplier)
    {
        try {
            // Update the user associated with the supplier with the provided information or keep the existing values if not provided
            $supplier->user()->update([
                'name' => $request->name ?? $supplier->user->name,
                'status' => $request->status ?? $supplier->user->status,
            ]);

            // Return a success response with the updated supplier resource
            return $this->sendSuccess(trans('crud.update', ['model' => 'supplier']), new SupplierResource($supplier));
        } catch (\Exception $e) {
            // Return an error response with the exception message
            return $this->sendError(trans('api.400'), ["error" => $e->getMessage()]);
        }
    }

    /**
     * Delete a specific supplier and its associated user.
     *
     * @param int $supplier
     * @return mixed
     */
    public function destroy($supplier)
    {
        // Find the supplier details with the 'user' relationship
        $supplierDetail = Supplier::with('user')->find($supplier);

        // If the supplier is not found, return an error response
        if (!$supplierDetail)
            return $this->sendError(trans('api.400'), ["error" => trans('crud.notFound')], 400);

        try {
            // Delete the supplier and its associated user
            if (Supplier::destroy($supplierDetail->id))
                User::destroy($supplierDetail->user->id);

            // Return a success response with the deleted supplier resource
            return $this->sendSuccess(trans('crud.delete', ['model' => 'supplier']), new SupplierResource($supplierDetail));

        } catch (\Exception $e) {
            // Return an error response with the exception message
            return $this->sendError(trans('api.400'), ["error" => $e->getMessage()], "401");
        }
    }
}
