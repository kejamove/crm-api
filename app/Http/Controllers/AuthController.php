<?php

namespace App\Http\Controllers;

use App\Enums\RoleEnum;
use App\Models\Branch;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['login']);
    }

    public function index(Request $request)
    {
        $user = Auth::user();

        /**
         * SUPER USER
         */
        if ($user->tokenCan(RoleEnum::super_admin->value)) {
            $query = User::query();

            if ($request->has('branch')) {
                $query->where('branch', $request->input('branch'));
            }

            return $query->get();
        }

        /**
         * FIRM OWNER
         */
        if ($user->tokenCan(RoleEnum::firm_owner->value)) {
            $query = User::where('firm', $user->firm);

            if ($request->has('branch')) {
                $query->where('branch', $request->input('branch'));
            }

            return $query->get();
        }

        /**
         * BRANCH MANAGER
         */
        if ($user->tokenCan(RoleEnum::branch_manager->value)) {
            return User::where('branch', $user->branch)->get();
        }

        abort(403, 'Unauthorized action.');

    }

    /**
     * Get the authenticated user.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function get_current_logged_in_user()
    {
        $user = Auth::user();

        if ($user) {
            $user_type = $user->user_type;

            // List allowed_urls for every role for frontend authentication
            $all_urls = [
                'register_user' => ['super_admin', 'store_owner'],
                'register_store' => ['super_admin', 'store_owner'],
                'logout' => ['super_admin', 'project_manager', 'sales', 'marketing'],
                'login' => ['super_admin', 'project_manager', 'sales', 'marketing'],
                'all_users' => ['super_admin'],
            ];

            $my_allowed_urls = [];
            foreach ($all_urls as $url => $roles) {
                if (in_array($user_type, $roles)) {
                    $my_allowed_urls[] = $url;
                }
            }

            return response()->json([
                'user' => $user,
                'user_type' => $user_type,
                'allowed_urls' => $my_allowed_urls
            ]);
        } else {
            abort(401, 'Unauthenticated action.');
        }
    }

    public function logout(Request $request)
    {
        $user = Auth::user();

        if ($user) {
            $user->tokens()->delete();
            return response()->json(['message' => 'Successfully logged out'], 200);
        } else {
            return response()->json(['message' => 'There is no logged-in user'], 404);
        }
    }

    public function login(Request $request)
    {
        $fields = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (User::count() < 1) {
            return "There are no users in the database.";
        }

        // Check email and password
        $user = User::where('email', $fields['email'])->first();

        if (!$user || !Hash::check($fields['password'], $user->password)) {
            abort(401,'Bad credentials.');
        }

        // Create user token with abilities of the given user type
        $token = $user->createToken('kejamovetoken', [$user->user_type])->plainTextToken;

        // Output
        $response = [
            'user' => $user,
            'token' => $token
        ];

        return response($response, 200);
    }

    public function register_user(Request $request)
    {
        // Ensure the user is authenticated
        if (!Auth::check()) {
            abort(401, 'Unauthenticated action.');
        }

        // Get the authenticated user
        $user = Auth::user();

        // Validate request fields
        $fields = $request->validate([
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'user_type' => 'required|string',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone_local_number' => 'required|string|max:255',
            'phone_country_code' => 'required|string|max:255',
        ]);

        // Check if the authenticated user is an admin
        if ($fields['user_type'] == 'super_admin' && !$user->tokenCan('super_admin')) {
            abort(403, 'Unauthorized action. Only super admin can access this page.');
        }

        // Create the new user
        $newUser = User::create([
            'email' => $fields['email'],
            'password' => Hash::make($fields['password']),
            'user_type' => $fields['user_type'],
            'first_name' => $fields['first_name'],
            'last_name' => $fields['last_name'],
            'phone_local_number' => $fields['phone_local_number'],
            'phone_country_code' => $fields['phone_country_code'],
            'firm' => $request['firm'],
            'branch' => $request['branch'],
        ]);

        // Generate token
        $token = $newUser->createToken('kejamovetoken', [$newUser->user_type])->plainTextToken;

        // Update user with token
        $newUser->update(['remember_token' => $token]);

        // Output
        $response = [
            'user' => $newUser,
            'token' => $token
        ];

        return response($response, 201);
    }

    public function getUserByBranch($branchId)
    {
        $user = Auth::user();

        $userObject = [] ;

        if ($user->tokenCan(RoleEnum::super_admin->value) || $user->tokenCan(RoleEnum::firm_owner->value)) {
            $userObject = User::where('branch', $branchId)->get();
            return response()->json($userObject, 200);
        }

        if ($user->tokenCan(RoleEnum::sales->value) ||
            $user->tokenCan(RoleEnum::marketing->value) ||
            $user->tokenCan(RoleEnum::project_manager->value) ||
            $user->tokenCan(RoleEnum::branch_manager->value)
        ) {
            return response()->json([$user], 200);
        }

        abort(403, 'Unauthorized action.');
    }


    /**
     * Display Info about the resources
     */
    public function get_user_data()
    {
        $user = Auth::user();

        if ($user) {
            // only admin can see all users
            if ($user->tokenCan('super_admin')) {
                return response()->json(['count' => User::count()]);
            }elseif ($user->tokenCan(RoleEnum::firm_owner->value)){
                $employeeCount = User::where('firm', $user->firm)->get()->count();
                return response()->json(['count'=>$employeeCount], 200);
            }
            else {
                abort(403, 'Unauthorized Action.');
            }
        } else {
            abort(401, 'Unauthenticated action.');
        }
    }
}
