<?php

namespace App\Http\Controllers\Api\V1;

use App\Filters\Api\V1\RiderFilter;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\RiderStoreRequest;
use App\Http\Requests\Api\V1\RiderUpdateRequest;
use App\Http\Resources\Api\V1\RiderCollection;
use App\Http\Resources\Api\V1\RiderResource;
use App\Models\Rider;
use App\Models\User;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Notifications\SendWelcomeRiderNotification;

class RiderController extends Controller
{
    use HttpResponses;

    /**
     * Retrieve a paginated list of riders based on filters.
     *
     * @param Request $request
     * @return RiderCollection
     */
    public function index(Request $request)
    {
        // Start with the base rider query
        $riderQuery = Rider::query();

        // Create a RiderFilter instance to handle filtering
        $filter = new RiderFilter();
        $queryFilter = $filter->transform($request);

        // Check if there are filters; if not, include the 'user' relationship
        if (count($queryFilter) == 0) {
            $riderQuery = $riderQuery->with('user');
        } else {
            // Apply filters on the 'user' relationship
            $riderQuery = $riderQuery->whereHas('user', function ($query) use ($queryFilter) {
                $query->where($queryFilter);
            });
        }

        // Return a paginated collection with appended query parameters
        return new RiderCollection($riderQuery->paginate()->appends($request->query()));
    }

    /**
     * Store a new rider and send a welcome notification.
     *
     * @param RiderStoreRequest $request
     * @return mixed
     */
    public function store(RiderStoreRequest $request)
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
                'role' => 'rider',
                'status' => $request->status,
            ]);

            // Send a welcome notification to the new user
            $user->notify(new SendWelcomeRiderNotification($user, $password));

            // Create a new Rider associated with the user
            $rider = new Rider(['user_id' => $user->id]);
            $riderData = $user->riders()->save($rider);

            // Return a success response with the created rider resource
            return $this->sendSuccess(trans('crud.create', ['model' => 'rider']), new RiderResource($riderData), 200);

        } catch (\Exception $e) {
            // Return an error response with the exception message
            return $this->sendError(trans('api.400'), ["error" => $e->getMessage()], 400);
        }
    }

    /**
     * Show the details of a specific rider.
     *
     * @param int $rider
     * @return RiderResource|mixed
     */
    public function show($rider)
    {
        // Find the rider details with the 'user' relationship
        $riderDetail = Rider::with('user')->find($rider);

        // If the rider is not found, return an error response
        if (!$riderDetail)
            return $this->sendError(trans('api.400'), ["error" => trans('crud.notFound')], 400);

        // Return the rider details as a resource
        return new RiderResource($riderDetail);
    }

    /**
     * Update the details of a specific rider.
     *
     * @param RiderUpdateRequest $request
     * @param Rider $rider
     * @return mixed
     */
    public function update(RiderUpdateRequest $request, Rider $rider)
    {
        try {
            // Update the user associated with the rider with the provided information or keep the existing values if not provided
            $rider->user()->update([
                'name' => $request->name ?? $rider->user->name,
                'status' => $request->status ?? $rider->user->status,
            ]);

            // Return a success response with the updated rider resource
            return $this->sendSuccess(trans('crud.update', ['model' => 'rider']), new RiderResource($rider));
        } catch (\Exception $e) {
            // Return an error response with the exception message
            return $this->sendError(trans('api.400'), ["error" => $e->getMessage()]);
        }
    }

    /**
     * Delete a specific rider and its associated user.
     *
     * @param int $rider
     * @return mixed
     */
    public function destroy($rider)
    {
        // Find the rider details with the 'user' relationship
        $riderDetail = Rider::with('user')->find($rider);

        // If the rider is not found, return an error response
        if (!$riderDetail)
            return $this->sendError(trans('api.400'), ["error" => trans('crud.notFound')], 400);

        try {
            // Delete the rider and its associated user
            if (Rider::destroy($riderDetail->id))
                User::destroy($riderDetail->user->id);

            // Return a success response with the deleted rider resource
            return $this->sendSuccess(trans('crud.delete', ['model' => 'rider']), new RiderResource($riderDetail));

        } catch (\Exception $e) {
            // Return an error response with the exception message
            return $this->sendError(trans('api.400'), ["error" => $e->getMessage()], "401");
        }
    }
}
