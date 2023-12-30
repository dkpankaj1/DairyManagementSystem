<?php

namespace App\Http\Controllers\API\V1;

use App\Filters\Api\V1\RiderFilter;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\RiderStoreRequest;
use App\Http\Requests\Api\V1\RiderUpdateRequest;
use App\Http\Resources\Api\V1\RiderCollection;
use App\Http\Resources\Api\V1\RiderResource;
use App\Models\Rider;
use App\Models\User;;
use Illuminate\Http\Request;
use App\Traits\HttpResponses;
use Illuminate\Support\Facades\Hash;
use App\Notifications\SendWelcomeRiderNotification;

class RiderController extends Controller
{
    use HttpResponses;

    public function index(Request $request)
    {
        $riderQuery = Rider::query();

        $filter = new RiderFilter();
        $queryFilter = $filter->transform($request);


        if (count($queryFilter) == 0) {
            $riderQuery = $riderQuery->with('user');
        } else {
            $riderQuery = $riderQuery->whereHas('user', function ($query) use ($queryFilter) {
                $query->where($queryFilter);
            });
        }
        return new RiderCollection($riderQuery->paginate()->appends($request->query()));
    }

    public function store(RiderStoreRequest $request)
    {
        try {
            $password = $request->generatePassword();

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($password),
                'role' => 'rider',
                'status' => $request->status,
            ]);

            // sand notification
            $user->notify(
                new SendWelcomeRiderNotification(
                    $user,
                    $password
                )
            );

            $rider = new Rider(['user_id' => $user]);
            $riderData = $user->riders()->save($rider);
            return $this->sendSuccess(trans('crud.create', ['model' => 'rider']), new RiderResource($riderData), 200);

        } catch (\Exception $e) {
            return $this->sendError(trans('api.400'), ["error" => $e->getMessage()], 400);
        }

    }

    public function show($rider)
    {
        $riderDetail = Rider::with('user')->find($rider);

        if (!$riderDetail)
            return $this->sendError(trans('api.400'), ["error" => trans('crud.notFound')], 400);

        return new RiderResource($riderDetail);
    }

    public function update(RiderUpdateRequest $request, Rider $rider)
    {
        try {
            $rider->user()->update([
                'name' => $request->name ?? $rider->user->name,
                'status' => $request->status ?? $rider->user->status,
            ]);
            return $this->sendSuccess(trans('crud.update', ['model' => 'rider']), new RiderResource($rider));
        } catch (\Exception $e) {
            return $this->sendError(trans('api.400'), ["error" => $e->getMessage()]);
        }

    }
    public function destroy($rider)
    {

        $riderDetail = Rider::with('user')->find($rider);

        if (!$riderDetail)
            return $this->sendError(trans('api.400'), ["error" => trans('crud.notFound')], 400);

        try {
            if (Rider::destroy($riderDetail->id))
                User::destroy($riderDetail->user->id);
            return $this->sendSuccess(trans('crud.delete', ['model' => 'rider']), new RiderResource($riderDetail));

        } catch (\Exception $e) {
            return $this->sendError(trans('api.400'), ["error" => $e->getMessage()], "401");
        }
    }

}