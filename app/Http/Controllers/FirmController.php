<?php

namespace App\Http\Controllers;

use App\Models\Firm;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FirmController extends Controller
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
        return Firm::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        if ($user->tokenCan('admin')) {
            $request->validate([
                'name'=>'required|string|unique:stores|max:255',
                'location'=>'required',
            ]);

            $registration_number = 'keja' . uniqid();

            // check if registration number already exists
            while (Firm::where('registration_number', $registration_number)->exists()) {
                $registration_number = 'keja' . uniqid();
            }

            $firm = new Firm();
            $firm->name = $request['name'];
            $firm->location = $request['location'];
            $firm->registration_number = $registration_number;


            $firm->save();

            return response()->json($firm, 201);
        }else {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

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

    public function firmDetails(Request $request)
    {
        /**
         * is user authenticated
         */
        if (Auth::check()) {
            $user = Auth::user();

            /**
             * is user a store_owner and does he have a store attached to him
             */
            if ($user->tokenCan('store_owner') && $user->firm) {

                /**
                 * does user's store match the request
                 */
                $firm = Firm::find($user->firm);

                foreach ($firm->branches as $branch) {
                    $branch->employees;
                }
                $firm->load('branches.employees');

                return response()->json($firm, 200);

            }else {
                return response()->json(['message' => 'Unauthorized. Missing required permissions: Store Owner'], 403);
            }
        }
        else {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }
    }

}
