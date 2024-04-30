<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Move;

class VolumeController extends Controller
{

    /**
     * User needs to be authenticated
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Check if the user's store matches the store associated with the move.
     */
    private function does_user_belong_to_the_store($user, $move)
    {
        if ($user->store != $move->store || $user->user_type == 'admin') {
            return response()->json(['error' => 'forbidden', 'message' => 'You are not permitted to edit volumes for moves associated with other stores.'], 403);
        }
    }

    /**
     * Check if the user has permission to perform the action.
     */
    private function check_permission($user)
    {
        if ($user->user_type != 'admin' && $user->user_type != 'store_owner') {
            return response()->json(['error'=> 'Not Permitted', 'message'=> 'You need to be an admin or a store owner to perform this action'], 403);
        }
    }

    /**
     * Store a newly created volume in storage.
     */
    public function store(Request $request, $moveId)
    {
        // Validate the incoming request data
        $request->validate([
            'area' => 'required|string',
            'item' => 'required|string',
            'size_cubic_meters' => 'required|numeric',
            'quantity' => 'required|integer',
            'number_of_boxes' => 'required|integer',
        ]);

        // Find the move by its ID
        $move = Move::findOrFail($moveId);

        // Check if the user is authenticated
        $user = Auth::user();

        // Check if the user's store matches the store associated with the move
        $this->does_user_belong_to_the_store($user, $move);

        // Create a new volume related to the move
        $volume = $move->volumes()->create($request->all());

        // Return the created volume with status code 201 (Created)
        return response()->json($volume, 201);
    }

    /**
     * Display the specified volume.
     */
    public function show($moveId, $volumeId)
    {
        // Find the move by its ID
        $move = Move::findOrFail($moveId);

        // Find the volume by its ID related to the move
        $volume = $move->volumes()->findOrFail($volumeId);

        // Check if the user is authenticated
        $user = Auth::user();

        // Check if the user's store matches the store associated with the move
        $this->does_user_belong_to_the_store($user, $move);

        // Return the volume
        return response()->json($volume);
    }

    /**
     * Update the specified volume in storage.
     */
    public function update(Request $request, $moveId, $volumeId)
    {
        // Validate the incoming request data
        $request->validate([
            'area' => 'required|string',
            'item' => 'required|string',
            'size_cubic_meters' => 'required|numeric',
            'quantity' => 'required|integer',
            'number_of_boxes' => 'required|integer',
        ]);

        // Find the move by its ID
        $move = Move::findOrFail($moveId);

        // Find the volume by its ID related to the move
        $volume = $move->volumes()->findOrFail($volumeId);

        // Check if the user is authenticated
        $user = Auth::user();

        // Check if the user's store matches the store associated with the move
        $this->does_user_belong_to_the_store($user, $move);

        // Update the volume with the new data
        $volume->update($request->all());

        // Return the updated volume
        return response()->json($volume);
    }

    /**
     * Remove the specified volume from storage.
     */
    public function destroy($moveId, $volumeId)
    {
        // Find the move by its ID
        $move = Move::findOrFail($moveId);

        // Find the volume by its ID related to the move
        $volume = $move->volumes()->findOrFail($volumeId);

        // Check if the user is authenticated
        $user = Auth::user();

        // Check if the user's store matches the store associated with the move
        $this->does_user_belong_to_the_store($user, $move);

        // Check if the user has permission to delete the volume
        $this->check_permission($user);

        // Delete the volume
        $volume->delete();

        // Return success response
        return response()->json(['message' => 'Volume deleted successfully']);
    }
}
