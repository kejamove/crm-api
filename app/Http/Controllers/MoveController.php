<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Move;
use App\Models\User;

class MoveController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Move::all();
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
        if (!$user) {
            return response()->json(['error' => 'unauthorized', 'message' => 'You need to be logged in.'], 401);
        }
    
        // Check if the user belongs to a store
        if (!$user->store) {
            return response()->json(['error' => 'forbidden', 'message' => 'You need to belong to a store.'], 403);
        }

        // check if the sales rep is in the same excact store
        $sales_rep_store = User::where('id', $request->sales_representative);
        if (!$sales_rep_store){
            return response()->json(['error' => 'forbidden', 'message' => 'The sales rep does not belong to your store'], 403);
        }
    
        // Prepare data for move creation
        $data = [
            'sales_representative' => $request->sales_representative,
            'store' => $user->store,
            'lead_source' => $request->lead_source,
            'consumer_name' => $request->consumer_name,
            'corporate_name' => $request->corporate_name,
            'contact_information' => $request->contact_information,
            'moving_from' => $request->moving_from,
            'moving_to' => $request->moving_to,
            'invoiced_amount' => $request->invoiced_amount,
            'notes' => $request->notes
        ];
    
        try {
            // Create the move
            $move = Move::create($data);
            // Return the created move with status code 201 (Created)
            return response()->json($move, 201);
        } catch (\Exception $e) {
            // Handle server errors
            return response()->json(['error' => 'internal_server_error', 'message' => 'Failed to create move. Please try again later.'], 500);
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

        // Append the user object to the move
        $move->sales_representative_object = $user;

        // Return the move with the user object included
        return $move;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $move = Move::find($id);
        $move->update($request->all());
        return $move;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
    
        return Move::destroy($id);
    }

    
}
