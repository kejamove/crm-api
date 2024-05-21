<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Firm;
use App\Models\Store;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FirmController extends Controller
{

    /**
     * check user permissions
     */
    public function checkPermissions($id, User $user)
    {
        // Check if the user is a super admin or a firm owner
        if (!($user->tokenCan('super_admin') || $user->tokenCan('firm_owner'))) {
            abort(403, 'Unauthorised Action!');
        }

        // Check if the user is a firm owner and if the firm ID matches
        if ($user->tokenCan('firm_owner') && $user->firm !== $id) {
            abort(403, 'Unauthorised Access');
        }
    }


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

        if(!$user->tokenCan('super_admin')){
            abort(403, 'Unauthorised Action');
        }

        return Firm::with('branches')->get();

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        if ($user->tokenCan('super_admin')) {
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
            abort(403, 'Unauthorized action.');
        }

    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = Auth::user();

        if (!($user->tokenCan('super_admin') || $user->tokenCan('firm_owner'))) {
            abort(403, 'Unauthorised Action!');
        }

        $firm = Firm::with(['branches.employees', 'branches.moves', 'branches' => function ($query) {
            $query->withCount('moves');
        }])->find($id);

        if (!$firm) {
            abort(404, 'Firm Not Found');
        }

        if ($user->tokenCan('firm_owner') && $user->firm !== $id) {
            abort(403, 'Unauthorised Access');
        }

        abort(403, 'Unauthorized access!');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = Auth::user();

        if (!($user->tokenCan('super_admin') || $user->tokenCan('firm_owner'))) {
            abort(403, 'Unauthorised Action!');
        }

        // Check if the user is a firm owner and if the firm ID matches
        if ($user->tokenCan('firm_owner') && $user->firm !== $id) {
            abort(403, 'Unauthorised Access');
        }

        $firm = Firm::findOrFail($id);

        $firm->update($request->only($firm->getFillable()));

        return response()->json(['message' => 'Firm updated successfully', 'firm' => $firm], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = Auth::user();

        if (!($user->tokenCan('super_admin') || $user->tokenCan('firm_owner'))) {
            abort(403, 'Unauthorised Action!');
        }

        // Check if the user is a firm owner and if the firm ID matches
        if ($user->tokenCan('firm_owner') && $user->firm !== $id) {
            abort(403, 'Unauthorised Access');
        }

        $firm = Firm::findOrFail($id);
        $firm->delete();

    }

    public function firmDetails(string $id)
    {
        /**
         * is user authenticated
         */
        if (Auth::check()) {
            $user = Auth::user();

            /**
             * is user a store_owner and does he have a store attached to him
             */
            if ($user->tokenCan('firm_owner') && $user->firm) {

                /**
                 * does user's store match the request
                 */
                $firm = Firm::find($user->firm);

                foreach ($firm->branches as $branch) {
                    $branch->employees;
                }
                $firm->load('branches.employees');

                return response()->json($firm, 200);

            }else if ($user->tokenCan('super_admin')) {
                return response()->json(['Firm' => Firm::Find($id), 'sth'=> 'some text']);
            }else
            {
                return response()->json(['message' => 'Unauthorized. Missing required permissions: Store Owner'], 403);
            }
        }
        else {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }
    }

}
