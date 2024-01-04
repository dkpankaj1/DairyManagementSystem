<?php

namespace App\Http\Controllers\Api\V1;

use App\Filters\Api\V1\UserFilters;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\UserStoreRequest;
use App\Http\Requests\Api\V1\UserUpdateRequest;
use App\Models\User;
use App\Notifications\SendWelcomeUserNotification;
use App\Traits\HttpResponses;
use Cortexitsolution\ApiAuth\Http\Resources\UserCollection;
use Cortexitsolution\ApiAuth\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    use HttpResponses;

    /**
     * Retrieve a paginated list of staff users based on filters.
     *
     * @param Request $request
     * @return UserCollection
     */
    public function index(Request $request)
    {
        // Start with the base user query
        $userQuery = User::query();

        // Filter users based on the 'staff' role
        $userQuery = $userQuery->where('role', 'staff');

        // Apply additional filters using UserFilters
        $filter = new UserFilters();
        $queryFilter = $filter->transform($request);

        // If there are no filters, return a paginated collection of users
        if (count($queryFilter) == 0) {
            return new UserCollection($userQuery->paginate());
        }

        // If there are filters, apply them and return the paginated collection with appended query parameters
        return new UserCollection($userQuery->where($queryFilter)->paginate()->appends($request->query()));
    }

    /**
     * Store a new staff user and send a welcome notification.
     *
     * @param UserStoreRequest $request
     * @return mixed
     */
    public function store(UserStoreRequest $request)
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
                'role' => 'staff',
                'status' => $request->status,
            ]);

            // Send a welcome notification to the new user
            $user->notify(new SendWelcomeUserNotification($user, $password));

            // Return a success response with the created user resource
            return $this->sendSuccess(trans('crud.create', ['model' => 'user']), new UserResource($user), 200);

        } catch (\Exception $e) {
            // Return an error response with the exception message
            return $this->sendError(trans('api.401'), ["error" => $e->getMessage()], 401);
        }
    }

    /**
     * Show the details of a specific user.
     *
     * @param User $user
     * @return UserResource
     */
    public function show(User $user)
    {
        return new UserResource($user);
    }

    /**
     * Update the details of a specific user.
     *
     * @param UserUpdateRequest $request
     * @param User $user
     * @return mixed
     */
    public function update(UserUpdateRequest $request, User $user)
    {
        try {
            // Update the user with the provided information or keep the existing values if not provided
            $user->update([
                'name' => $request->name ?? $user->name,
                'status' => $request->status ?? $user->status,
            ]);

            // Return a success response with the updated user resource
            return $this->sendSuccess(trans('crud.update', ['model' => 'user']), new UserResource($user));

        } catch (\Exception $e) {
            // Return an error response with the exception message
            return $this->sendError(trans('crud.401'), ["error" => $e->getMessage()]);
        }
    }

    /**
     * Delete a specific user.
     *
     * @param Request $request
     * @param User $user
     * @return mixed
     */
    public function destroy(Request $request, User $user)
    {
        // Prevent deletion of a user with ID 1
        if ($user->id == 1) {
            return $this->sendError(trans('api.405'), [], 405);
        }

        try {
            // Delete the user and return a success response with the deleted user resource
            $user->delete();
            return $this->sendSuccess(trans('crud.delete', ['model' => 'users']), new UserResource($user));

        } catch (\Exception $e) {
            // Return an error response with the exception message
            return $this->sendError(trans('crud.401'), ["error" => $e->getMessage()]);
        }
    }
}
