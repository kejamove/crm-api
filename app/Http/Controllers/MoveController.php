<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Firm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Move;
use App\Models\User;
use App\Helpers;
use Illuminate\Support\Facades\DB;

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

        // Check if the user is a super admin && return all moves
        if ($user->tokenCan('super_admin')) {
            return response()->json(['data'=>Move::all()],200);
        }

        // firm_owner
        if ($user->tokenCan('firm_owner')) {
            $firmId = $user->firm;

            // Retrieve all moves associated with the user's firm
            $moves = Move::whereHas('branch', function ($query) use ($firmId) {
                $query->where('firm', $firmId);
            })->get();

            // Return the moves
            return response()->json(['moves' => $moves], 200);
        }

        // branch_manager && project_manager
        if ($user->tokenCan('branch_manager') || $user->tokenCan('project_manager')) {
            $moves = Move::where('branch', $user->branch)->get();
            // Return the moves
            return response()->json(['moves' => $moves], 200);
        }

        abort(403, 'Unauthorised Action');
    }


    /**
     * Store a newly created resource in storage.
     */
    public function create_move(Request $request)
    {
        // Check if the required fields are present
        $request->validate([
            'sales_representative' => 'required',
            'branch' => 'required',
            'moving_from' => 'required',
            'moving_to' => 'required',
            'contact_information' => 'required',
            'move_stage' => 'required',
        ]);

        // Check if the user is authenticated
        $user = Auth::user();

        if (!($user->tokenCan('super_admin') || $user->branch == $request->branch)) {
            abort(403, 'Unauthorised Action!');
        }


        if ( !$request->sales_representative || $request->sales_representative !== $user->branch  ) {
            abort(403, 'The Sales Rep does not belong this branch or does not exist!');
        }

        $data = $request->only([
            'sales_representative',
            'branch',
            'move_stage',
            'consumer_name',
            'corporate_name',
            'contact_information',
            'moving_from',
            'moving_to',
            'invoiced_amount',
            'notes',
            'remarks',
            'lead_source',
        ]);

        try {
//            $move = Move::create($data);
            return response()->json(['data' => $data], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'internal_server_error' . $e, 'message' => 'Failed to create move. Please try again later.'], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = Auth::user();

        $move = Move::findOrFail($id);

        // unless you are a super admin , you need to belong to the branch of that move
        if (!($user->tokenCan('super_admin') || $user->branch == $move->branch)) {
            abort(403, 'Unauthorised Action!');
        }

        // Get the user object for the sales representative
        if ($move->sales_representative){
            $sales_rep = User::findOrFail($move->sales_representative);
            return response()->json(['data' => $move, 'sales_representative_object'=> $sales_rep], 200);
        }

        return response()->json(['data' => $move], 200);

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $move = Move::findOrFail($id);
        $user = Auth::user();

        if (!($user->tokenCan('super_admin') || $user->tokenCan('firm_owner') || $user->tokenCan('branch_manager'))) {
            abort(403, 'Unauthorised Action!');
        }

        if ($user->tokenCan('branch_manager') && $user->branch != $move->branch){
            abort(403, 'Unauthorised Action!');
        }

        $branch = Branch::with('firm')->findOrFail($move->branch);

        if ($user->tokenCan('firm_owner') && $user->firm != $branch->firm) {
            abort(403, 'Unauthorised. Wrong Firm!');
        }

        $move->fill($request->only($move->getFillable()));
        $move->save();

        $move->update($request->only($move->getFillable()));

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
        if (!($user->tokenCan('super_admin') || $user->tokenCan('firm_owner') || $user->tokenCan('branch_manager'))) {
            abort(403, 'Unauthorised Action!');
        }

        if ($user->tokenCan('branch_manager') && $user->branch != $move->branch){
            abort(403, 'Unauthorised Action!');
        }

        $branch = Branch::findOrFail($move->branch);

        if ($user->tokenCan('firm_owner') && $user->firm != $branch->firm) {
//            return response()->json('sm');
            abort(403, 'Unauthorised. Wrong Firm!');
        }

        $move->delete();
        return response()->json(['message' => 'Move deleted successfully'], 200);
    }

    /**
     * Display Info about the resources
     */

     public function get_move_data()
     {
        if (Auth::check()) {
            $user = Auth::user();

            // only admin can see all stores
            if ($user->tokenCan('admin')) {
                return response()->json(['count' => count(Move::all())]);
            }else {
                return response()->json(['message' => 'Unauthorized. Missing required permissions: Admin'], 403);
            }
        }else {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }
    }

    /**
     * Data on moves per month
     */
    public function get_moves_per_month($year)
    {
        if (Auth::check()) {
            $user = Auth::user();

            // only admin can see all stores
            if ($user->tokenCan('admin')) {
                $moveData = Move::select(
                    DB::raw('MONTH(move_request_received_at) as month'),
                    DB::raw('COUNT(*) as count')
                )
                ->whereYear('move_request_received_at', $year)
                ->groupBy('month')
                ->orderBy('month')
                ->get();

                // Prepare the data for the response
                $formattedData = $moveData->map(function($item) {
                    return [
                        'month' => $item->month,
                        'count' => $item->count,
                    ];
                });

                return response()->json($formattedData);
            }else {
                return response()->json(['message' => 'Unauthorized. Missing required permissions: Admin'], 403);
            }
        }else {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

    }

}
