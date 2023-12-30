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
    public function index(Request $request)
    {
        $userQuery = User::query();

        $userQuery = $userQuery->where('role', 'staff');

        $filter = new UserFilters();

        $queryFilter = $filter->transform($request);

        if (count($queryFilter) == 0) {
            return new UserCollection($userQuery->paginate());
        }

        return new UserCollection($userQuery->where($queryFilter)->paginate()->appends($request->query()));
    }

    public function store(UserStoreRequest $request)
    {
        try {
            $password = $request->generatePassword();

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($password),
                'role' => 'staff',
                'status' => $request->status,
            ]);

            // sand notification
            $user->notify(
                new SendWelcomeUserNotification(
                    $user,
                    $password
                )
            );

            return $this->sendSuccess(trans('crud.create', ['model' => 'user']), new UserResource($user), 200);

        } catch (\Exception $e) {
            return $this->sendError(trans('api.401'), ["error" => $e->getMessage()], 401);
        }
    }


    public function show(User $user)
    {
        return new UserResource($user);
    }

    public function update(UserUpdateRequest $request, User $user)
    {

        try {
            $user->update([
                'name' => $request->name ?? $user->name,
                'status' => $request->status ?? $user->status,
            ]);
            return $this->sendSuccess(trans('crud.update', ['model' => 'user']), new UserResource($user));
        } catch (\Exception $e) {
            return $this->sendError(trans('crud.401'), ["error" => $e->getMessage()]);
        }
    }

    public function destroy(Request $request, User $user)
    {
        if ($user->id == 1) {
            return $this->sendError(trans('api.405'), [], 405);
        }

        try {
            $user->delete();
            return $this->sendSuccess(trans('crud.delete', ['model' => 'users']), new UserResource($user));

        } catch (\Exception $e) {
            return $this->sendError(trans('crud.401'), ["error" => $e->getMessage()]);
        }
    }
}
