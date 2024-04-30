<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Move;
use App\Models\User;
use App\Helpers;

class MoveController extends Controller
{

    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();

        // Check if the user is an admin && return all moves 
        if ($user->user_type === 'admin') {
            return response()->json(['data'=>Move::all()],200);
        }

        // Check if the user belongs to a store
        if (!$user->store) {
            return response()->json(['error' => 'forbidden', 'message' => 'You need to belong to a store.'], 403);
        }

        // Return moves belonging to the user's store
        return response()->json(['data'=>Move::where('store', $user->store)->get()],200);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function create_move(Request $request)
    {
        // Check if the required fields are present
        $request->validate([
            'sales_representative' => 'required',
            'store' => 'required',
            'moving_from' => 'required',
            'moving_to' => 'required',
            'contact_information' => 'required',
        ]);

        // Check if the user is authenticated
        $user = Auth::user();

        // Check if the user belongs to a store
        if (!$user->store) {
            return response()->json(['error' => 'forbidden', 'message' => 'You need to belong to a store.'], 403);
        }

        // Check if the sales representative belongs to the user's store
        $salesRep = User::find($request->sales_representative);
        if (!$salesRep || $salesRep->store != $user->store) {
            return response()->json(['error' => 'forbidden', 'message' => 'The sales rep does not belong to your store'], 403);
        }

        // Prepare data for move creation
        $data = $request->only([
            'sales_representative',
            'store',
            'lead_source',
            'consumer_name',
            'corporate_name',
            'contact_information',
            'moving_from',
            'moving_to',
            'invoiced_amount',
            'notes'
        ]);

        try {
            // Create the move
            $move = Move::create($data);
            // Return the created move with status code 201 (Created)
            return response()->json(['data' => $move], 201);
        } catch (\Exception $e) {
            // Handle server errors
            return response()->json(['error' => 'internal_server_error' . $e, 'message' => 'Failed to create move. Please try again later.'], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Find the move by its ID
        $move = Move::find($id);

        if (!$move) {
            // Handle case where move is not found
            return response()->json(['error' => 'Move not found'], 404);
        }

        // Get the user object for the sales representative
        $user = User::find($move->sales_representative);

        if (!$user) {
            // Handle case where user is not found
            return response()->json(['error' => 'User not found'], 404);
        }

        // Check if the user is an admin or belongs to the store
        if ($user->store == $move->store || $user->user_type == 'admin') {
            return response()->json(['data' => $move], 200);
        } else {
            return response()->json(['error' => 'You need to be an admin or belong to this store'], 403);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $move = Move::findOrFail($id);
        $user = Auth::user();

        // Check if the user is an admin or belongs to the store
        if ($user->user_type != 'admin' && $user->store != $move->store) {
            return response()->json(['error' => 'forbidden', 'message' => 'You need to belong to this store'], 403);
        }

        $move->update($request->all());
        return response()->json(['data' => $move], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $move = Move::findOrFail($id);
        $user = Auth::user();

        // Check if the user is an admin or belongs to the store
        if ($user->user_type != 'admin' && $user->store != $move->store) {
            return response()->json(['error' => 'forbidden', 'message' => 'You need to belong to this store'], 403);
        }

        $move->delete();
        return response()->json(['message' => 'Move deleted successfully'], 200);
    }

}
