<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Resources\ApiResourceCollection;

class UserController extends Controller
{
    /**
     * Display a listing of the users.
     */
    public function index()
    {
        return new ApiResourceCollection(User::query()->paginate(3));
    }

    /**
     * Display the specified user.
     */
    public function show(int $id)
    {
        $user = User::find($id);

        if ($user) {
            return response()->json($user, 200);
        }

        return response()->json(['message' => 'User not found'], 404);
    }

    /**
     * Store a new user in storage.
     */
    public function store(StoreUserRequest $request)
    {

        $data = $request->validated();
        $user = User::create($data);

        return response()->json($user, 201);
    }

    /**
     * Update a existing user data
     */
    public function update(UpdateUserRequest $request, int $id)
    {
        $data = $request->validated();

        $user = User::find($id);

        if ($user) {
            $user->update($data);

            return response()->json($user, 200);
        }

        return response()->json(['message' => 'User not found'], 404);
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(int $id)
    {

        $user = User::find($id);

        if ($user) {
            $user->delete($id);
            return response()->json(['message' => 'User deleted successfully'], 200);
        }

        return response()->json(['message' => 'User not found'], 404);
    }
}
