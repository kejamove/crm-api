<?php

namespace App\Http\Controllers;

use App\Enums\LeadSourceEnum;
use App\Enums\MoveStage;
use App\Enums\RoleEnum;
use App\Models\Branch;
use App\Models\Firm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Move;
use App\Models\User;
use App\Helpers;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

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
        if ($user->tokenCan(RoleEnum::super_admin->value)) {
            return response()->json(Move::orderBy('id', 'desc')->get(), 200);
        }

        // firm_owner
        if ($user->tokenCan('firm_owner')) {
            $firmId = $user->firm;

            // Retrieve all moves associated with the user's firm
            $moves = Move::whereHas('branch', function ($query) use ($firmId) {
                $query->where('firm', $firmId);
            })->get();

            // Return the moves
            return response()->json($moves->sortByDesc('id')->values());
        }

        // branch_manager && project_manager
        if ($user->tokenCan(RoleEnum::branch_manager->value) || $user->tokenCan(RoleEnum::project_manager->value)) {
            $moves = Move::where('branch', $user->branch)->get();
            // Return the moves
            return response()->json($moves->sortByDesc('id')->values(), 200);
        }

        if ($user->tokenCan(RoleEnum::sales->value) || $user->tokenCan(RoleEnum::marketing->value)) {
            return response()->json(Move::where('sales_representative', $user->id)->get(), 200);
        }

        abort(403, 'Unauthorised Action');
    }


    /**
     * Store a newly created resource in storage.
     */
    public function create_move(Request $request)
    {
        $enumValues = [
            LeadSourceEnum::web->value,
            LeadSourceEnum::referral->value,
            LeadSourceEnum::offline_marketing->value,
            LeadSourceEnum::social_media->value,
            LeadSourceEnum::repeat_client->value,
        ];

        // Validate the incoming request data
        $validator = Validator::make($request->all(), [
            'sales_representative' => 'required',
            'branch' => 'required',
            'moving_from' => 'required',
            'moving_to' => 'required',
            'client_email' => 'required',
            'move_stage' => 'required',
            'lead_source' => ['required', 'string', 'in:' . implode(',', $enumValues)], // Ensure lead_source matches one of the enum values
        ]);

        // If validation fails, return the errors
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }
        // Check if the user is authenticated
        $user = Auth::user();

        if (
            !$user->tokenCan(RoleEnum::super_admin->value) &&
            $user->branch != $request->branch &&
            !$user->tokenCan(RoleEnum::firm_owner->value)
        )
        {
            abort(403, 'Unauthorised Action!');
        }

        if($user->tokenCan(RoleEnum::firm_owner->value))
        {
            $firmId = $user->firm;

            // Check if the branch exists and belongs to the firm owned by the user
            $branchId = $request->input('branch'); // Assuming 'branch' is the key for the branch ID in the request
            $branch = Branch::find($branchId);

            if (!$branch || $branch->firm !== $firmId) {
                abort(403, 'Unauthorized Action! The branch does not belong to your firm.');
            }
        }

        $sales_rep_object = User::findOrFail($request->sales_representative);

//        return response(['reps_branch' => $sales_rep_object->branch, 'moves'=> $request->branch], 200);

        if ( !$request->sales_representative && $sales_rep_object->branch !== $request->branch  ) {
            abort(403, 'The Sales Rep does not belong this branch or does not exist!');
        }

        $data = $request->only([
            'sales_representative',
            'branch',
            'move_stage',
            'consumer_name',
            'corporate_name',
            'client_email',
            'moving_from',
            'moving_to',
            'invoiced_amount',
            'notes',
            'remarks',
            'lead_source',
        ]);



        try {
            $move = Move::create($data);
            return response()->json(['data' => $move], 201);
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
        $sales_rep = User::findOrFail($move->sales_representative);
        if ($user->tokenCan(RoleEnum::firm_owner->value)){
            /**
             * CHECK IF THE FIRM THIS USER BELONGS TO HAS SUCH A BRANCH
             */

            $branch = Branch::findOrFail($move->branch);

            if ($user->firm != $branch->firm ){
                return response()->json(['error' => 'Unauthorised Action!', $branch, $user , $move], 200);
//                abort(403, 'Unauthorised Action! Wrong Firm ! Branch does not exist or does not belong to this firm!', $branch, $user-firm);

            }

            return response()->json(['data' => $move, 'sales_representative_object'=> $sales_rep], 200);
        }

        // unless you are a super admin , you need to belong to the branch of that move
        if (!($user->tokenCan(RoleEnum::super_admin->value) || $user->branch == $move->branch)) {
            abort(403, 'Unauthorised Action!');
        }

        // Get the user object for the sales representative
        if ($move->sales_representative){
            return response()->json(['data' =>$move, 'sales_representative_object'=> $sales_rep], 200);
        }

        return response()->json(['data' =>$move], 200);

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $move = Move::findOrFail($id);
        $user = Auth::user();

        if (!($user->tokenCan(RoleEnum::super_admin->value) ||
            $user->tokenCan(RoleEnum::firm_owner->value) ||
            $user->tokenCan(RoleEnum::branch_manager->value))) {
            abort(403, 'Unauthorised Action!');
        }

        if ($user->tokenCan(RoleEnum::branch_manager->value) && $user->branch != $move->branch){
            abort(403, 'Unauthorised Action!');
        }

        $branch = Branch::with('firm')->findOrFail($move->branch);

        if ($user->tokenCan(RoleEnum::firm_owner->value) && $user->firm != $branch->firm) {
            abort(403, 'Unauthorised. Wrong Firm!');
        }

        $move->fill($request->only($move->getFillable()));
        $move->save();

        $move->update($request->only($move->getFillable()));

        return response()->json(['data' => $move], 200);

    }

    /**
     * Remove the specified resource from storage.
     * 1. A Super Admin / Firm owner can delete move at any time
     * 2. Anyone else belonging to the permitted roles must belong to the same branch as the move
     * 3. The move should not be LOST/ WON
     */
    public function destroy(string $id)
    {
        $move = Move::findOrFail($id);

        $user = Auth::user();

        // Check if the user is a super admin
        if ($user->tokenCan(RoleEnum::super_admin->value)) {
            $move->delete();
            return response()->json(['message' => 'Move deleted successfully'], 200);
        }

        $branch = Branch::findOrFail($move->branch);

        // check if user is firm owner
        if ($user->tokenCan(RoleEnum::firm_owner->value) && $user->firm == $branch->firm) {
            $move->delete();
            return response()->json(['message' => 'Move deleted successfully'], 200);
        }

        // check if user is branch manager
        if ($user->tokenCan(RoleEnum::branch_manager->value) && $user->branch == $move->branch){
            if ($move->move_stage != MoveStage::won && $move->move_stage != MoveStage::lost)
            {
                $move->delete();
                return response()->json(['message' => 'Move deleted successfully'], 200);
            }
        }

        // The user should be in the same branch as the move
        if ($user->branch == $move->branch) {
            if ($move->move_stage == MoveStage::won->value || $move->move_stage == MoveStage::lost->value)
            {
                abort(403, 'Unauthorised Action. Attempting to delete a complete move');
            }

            // The user id should be the same of the sales rep
            if ($move->sales_representative != $user->id){
                abort(403, 'Unauthorised Action. Attempting to delete a somebody else`s move' .$move->sales_reperesentative . $user->id);
            }

            $move->delete();
            return response()->json(['message' => 'Move deleted successfully'], 200);
        }

        abort(403, 'Unauthorised Action');

    }

    /**
     * Display Info about the resources
     */

     public function get_move_data()
     {
        if (Auth::check()) {
            $user = Auth::user();

            if ($user->tokenCan(RoleEnum::super_admin->value)) {
                return response()->json(['count' => count(Move::all())]);
            }elseif ($user->tokenCan(RoleEnum::firm_owner->value)){
                $firmId = $user->firm;
                $totalMoves = \DB::table('branches')
                    ->join('moves', 'branches.id', '=', 'moves.branch')
                    ->where('branches.firm', $firmId)
                    ->count('moves.id');
                return response()->json(['count'=>$totalMoves], 200);
            }
            else {
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
            if ($user->tokenCan(RoleEnum::super_admin->value)) {
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
                return response()->json(['message' => 'Unauthorized. Missing required permissions'], 403);
            }
        }else {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

    }



}
