<?php

namespace App\Http\Controllers;

use App\Enums\RoleEnum;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Lead;
use Illuminate\Support\Facades\Auth;


class LeadController extends Controller
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
        $leads = Lead::all();
        return response()->json($leads);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function create_lead(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'name' => 'required|string',
            'location' => 'required|string',
            'area' => 'required|string',
            'phone_number' => 'required|string',
            'designation' => 'required|string',
        ]);

        $lead = Lead::create($request->all());

        return response()->json($lead, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $lead = Lead::findOrFail($id);
        return response()->json($lead);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'date' => 'required|date',
            'name' => 'required|string',
            'location' => 'required|string',
            'area' => 'required|string',
            'phone_number' => 'required|string',
            'designation' => 'required|string',
        ]);

        $lead = Lead::findOrFail($id);
        $lead->update($request->all());
        return response()->json($lead);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $user = Auth::user();
        if ($user->tokenCan(RoleEnum::super_admin->value)) {
            $lead = Lead::findOrFail($id);
            $lead->delete();
            return response()->json(['message' => 'Lead deleted successfully']);
        }else {
            return response()->json(['error'=> 'Fobidden', 'message'=>'You need to be admin to perform this action']);
        }

    }

     /**
     * Display Info about the resources
     */

     public function getLeadData()
     {
        if (Auth::check()) {
            $user = Auth::user();

            // only admin can see all stores
            if ($user->tokenCan(RoleEnum::super_admin->value)) {
                return response()->json(['data' => Lead::count() ]);
            }elseif ($user->tokenCan(RoleEnum::firm_owner->value)){
                $firmId = $user->firm;
                $leads = Lead::whereHas('store', function ($query) use ($firmId) {
                    $query->where('firm', $firmId);
                })->get()->count();
                return response()->json(['data'=>$leads], 200);
            }
            else {
                return response()->json(['message' => 'Unauthorized. Missing required permissions: Admin'], 403);
            }
        }else {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }
     }
}
