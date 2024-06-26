<?php

namespace App\Http\Controllers;

use App\Enums\RoleEnum;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BranchController extends Controller
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

        if ($user->tokenCan(RoleEnum::super_admin->value)) {
            return response()->json(Branch::all(), 200);
        }

        if ($user->tokenCan(RoleEnum::firm_owner->value)) {
            $firmId = $user->firm;

            // Retrieve all branches associated with the user's firm
            $branches = Branch::whereHas('firm', function ($query) use ($firmId) {
                $query->where('firm', $firmId);
            })->get();

            // Return the moves
            return response()->json($branches, 200);
        }

        if ($user->tokenCan(RoleEnum::sales->value) ||
            $user->tokenCan(RoleEnum::marketing->value) ||
            $user->tokenCan(RoleEnum::project_manager->value) ||
            $user->tokenCan(RoleEnum::branch_manager->value)
        ) {
            return response()->json(Branch::where('id', $user->branch)->get(), 200);
        }

        abort(403, 'Unauthorized access!');

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        if ($user->tokenCan(RoleEnum::firm_owner->value)) {
            $request->validate([
                'name'=>'required|string|unique:stores|max:255',
            ]);

            $firm = $user->firm;

            // Ensure the firm exists
            if (!$firm) {
                return response()->json(['error' => 'Firm not found'], 404);
            }

            $branchData = [
                'name' => $request->get('name'),
                'firm' => $firm,
            ];

            $branch = new Branch($branchData);
            $branch->save();

            return response()->json($branch, 201);

        }else if ($user->tokenCan(RoleEnum::super_admin->value)) {
            $request->validate([
                'name'=>'required|string|unique:stores|max:255',
                'firm'=>'required|int',
            ]);

            $branchData = [
                'name' => $request->get('name'),
                'firm' => $request->get('firm'),
            ];

            $branch = new Branch($branchData);
            $branch->save();
            return response()->json($branch, 201);
        }
        else {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = Auth::user();
        $firmId = $user->firm;
        if ($user->tokenCan(RoleEnum::super_admin->value) || $user->tokenCan(RoleEnum::firm_owner->value) || $user->tokenCan(RoleEnum::branch_manager->value)) {

            if ($user->tokenCan(RoleEnum::firm_owner->value) ) {
                $branch = Branch::where('id', $id)
                    ->where('firm', $firmId)
                    ->first();

                if (!$branch) {
                    abort(403, 'Unauthorised. Wrong Firm or Branch!');
                }

                $branch = Branch::with(['employees', 'moves'])->find($id);

                return response()->json($branch, 200);
            }

            $branch = Branch::with(['employees', 'moves'])->find($id);

            return response()->json($branch, 200);
        }
        abort(403, 'Unauthorized access!');

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = Auth::user();

        if ($user->tokenCan(RoleEnum::super_admin->value) || $user->tokenCan(RoleEnum::firm_owner->value) || $user->tokenCan(RoleEnum::branch_manager->value)) {

            if ($user->tokenCan(RoleEnum::branch_manager->value) && $user->branch !== $id){
                abort(403, 'Unauthorised Action. Wrong Branch!');
            }

            $firmId = $user->firm;

            if ($user->tokenCan(RoleEnum::firm_owner->value)) {
                $branch = Branch::where('id', $id)
                    ->where('firm', $firmId)
                    ->first();

                if (!$branch) {
                    abort(403, 'Unauthorised. Wrong Firm or Branch!');
                }
            }

            $branch = Branch::findOrFail($id);

            $branch->fill($request->only($branch->getFillable()));

            $branch->save();

            $branch->update($request->only($branch->getFillable()));

            return response()->json(['data' => $branch], 200);
        }

        abort(403, 'Unauthorised Access');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = Auth::user();
        if ($user->tokenCan(RoleEnum::super_admin->value) || $user->tokenCan(RoleEnum::firm_owner->value) || $user->tokenCan(RoleEnum::branch_manager->value)) {

            if ($user->tokenCan(RoleEnum::branch_manager->value) && $user->branch !== $id){
                abort(403, 'Unauthorised Action. Wrong Branch!');
            }

            $firmId = $user->firm;

            if ($user->tokenCan(RoleEnum::firm_owner->value)) {
                $branch = Branch::where('id', $id)
                    ->where('firm', $firmId)
                    ->first();
                if (!$branch) {
                    abort(403, 'Unauthorised. Wrong Firm or Branch!');
                }
            }

            $branch = Branch::findOrFail($id);
            $branch->delete();

            return response()->json(['data' => 'Deleted Successfuly'], 204);
        }

        abort(403, 'Unauthorised Action');

    }
}
