<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MoveController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        /**
         * 1. User has to be authenticated 
         * 2. User has to belong to a store
         * 
         */

        $user = Auth::user();

        if (!$user){
            return response()->json(['error' => 'unauthorised', 'message'=> 'You need to be logged in'], 401);
        }

        if (!$user->store){
            return response()->json(['error' => 'forbidden', 'message'=> 'You need to belong to a store'], 403);
        }

        $request->validate([
            'sales_representative'=>'required',
            'moving_from'=>'required',
            'moving_to'=>'required',
            'contact_information'=>'required',
        ]);
        return Move::create($request->all());
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
