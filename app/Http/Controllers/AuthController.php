<?php
namespace App\Http\Controllers;

use App\Enums\RoleEnum;
use App\Models\Branch;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * @OA\Info(title="My First API", version="0.1")
 */
class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['login']);
    }

    /**
     * @OA\Get(
     *     path="/list-users",
     *     operationId="index",
     *     @OA\Response(
     *         response="200",
     *         description="Successful operation"
     *     )
     * )
     */
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
            if ($user->firm == null) {
                return response()->json([]);
            }

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
            return User::where('branch', $user->branch)
                ->where('is_active', true)
                ->get();
        }

        abort(403, 'Unauthorized action.');
    }

    /**
     * @OA\Get(
     *     path="/active-user",
     *     operationId="getCurrentUser",
     *     @OA\Response(
     *         response=200,
     *         description="Get the authenticated user"
     *     )
     * )
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

    /**
     * @OA\Post(
     *     path="/logout",
     *     operationId="logout",
     *     @OA\Response(
     *         response=200,
     *         description="Successfully logged out"
     *     )
     * )
     */
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

    /**
     * @OA\Post(
     *     path="/login",
     *     operationId="login",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="email", type="string"),
     *             @OA\Property(property="password", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successfully logged in"
     *     )
     * )
     */
    public function login(Request $request)
    {
        $fields = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (User::count() < 1) {
            abort(401, 'There are no users in the database.');
        }

        // Check email and password
        $user = User::where('email', $fields['email'])->first();

        if ($user->is_active === false) {
            abort(403, 'Unauthorized action. || You were fired!!');
        }

        if (!$user || !Hash::check($fields['password'], $user->password)) {
            abort(401, 'Bad credentials.');
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

    /**
     * @OA\Post(
     *     path="/register-user",
     *     operationId="registerUser",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="email", type="string"),
     *             @OA\Property(property="password", type="string"),
     *             @OA\Property(property="user_type", type="string"),
     *             @OA\Property(property="first_name", type="string"),
     *             @OA\Property(property="last_name", type="string"),
     *             @OA\Property(property="phone_local_number", type="string"),
     *             @OA\Property(property="phone_country_code", type="string"),
     *             @OA\Property(property="firm", type="string"),
     *             @OA\Property(property="branch", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Successfully registered user"
     *     )
     * )
     */
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
        if ($fields['user_type'] == 'super_admin' && !$user->tokenCan(RoleEnum::super_admin->value)) {
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

    /**
     * @OA\Get(
     *     path="list-user-by-branch/{branchId}",
     *     operationId="getUserByBranch",
     *     @OA\Parameter(
     *         name="branchId",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Users by branch"
     *     )
     * )
     */
    public function getUserByBranch($branchId)
    {
        $user = Auth::user();

        $userObject = [];

        if ($user->tokenCan(RoleEnum::super_admin->value) ||
            $user->tokenCan(RoleEnum::firm_owner->value)) {
            $userObject = User::where('branch', $branchId)->get();
            return response()->json($userObject, 200);
        }

        if ($user->tokenCan(RoleEnum::sales->value) ||
            $user->tokenCan(RoleEnum::marketing->value)) {
            return response()->json([$user], 200);
        }

        if (
            $user->tokenCan(RoleEnum::project_manager->value) ||
            $user->tokenCan(RoleEnum::branch_manager->value)
        ) {
            $userObject = User::where('branch', $branchId)->get();
            return response()->json($userObject, 200);
        }

        abort(403, 'Unauthorized action.');
    }


    /**
     * @OA\Get(
     *     path="/list-user-by-id/{id}",
     *     operationId="getUserById",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User details"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function getUserById($id)
    {
        $user = Auth::user();

        if (!$user) {
            abort(403, 'Unauthorized action.');
        }

        $requestedUser = User::findOrFail($id);

        if (
            $user->tokenCan(RoleEnum::super_admin->value) ||
            $user->id === $requestedUser->id ||
            ($user->tokenCan(RoleEnum::firm_owner->value) && $user->firm === $requestedUser->firm) ||
            ($user->tokenCan(RoleEnum::branch_manager->value) && $user->branch === $requestedUser->branch)
        )
        {
            return response()->json($requestedUser, 200);
        }

        abort(403, 'Unauthorized action.');

    }

    /**
     * @OA\Get(
     *     path="/user-data",
     *     operationId="getUserData",
     *     @OA\Response(
     *         response=200,
     *         description="Get user data"
     *     )
     * )
     */
    public function get_user_data()
    {
        $user = Auth::user();

        if ($user) {
            // only admin can see all users
            if ($user->tokenCan('super_admin')) {
                return response()->json(['count' => User::count()]);
            } elseif ($user->tokenCan(RoleEnum::firm_owner->value) && $user->firm !== null) {
                $employeeCount = User::where('firm', $user->firm)->get()->count();
                return response()->json(['count' => $employeeCount], 200);
            } elseif ($user->tokenCan(RoleEnum::firm_owner->value) && $user->firm == null) {
                return response()->json(['count' => 0], 200);
            } else {
                abort(403, 'Unauthorized Action.');
            }
        } else {
            abort(401, 'Unauthenticated action.');
        }
    }

    /**
     * @OA\Delete(
     *     path="/delete-user/{id}",
     *     operationId="deleteUser",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User deactivated successfully"
     *     )
     * )
     */
    public function destroy($id)
    {
        $loggedInUser = Auth::user();
        $userUnderReview = User::findOrFail($id);

        // You cannot fire a super admin unless you are one
        if (!$loggedInUser->tokenCan(RoleEnum::super_admin->value)) {
            if ($userUnderReview->user_type == RoleEnum::super_admin) {
                abort(403, 'Unauthorized action. You need to be a ' . RoleEnum::super_admin->value . ' user to perform this action.');
            }
        }

        // Check if the logged-in user is a super admin and is trying to deactivate another super admin
        if ($loggedInUser->tokenCan(RoleEnum::super_admin->value)) {
            $userUnderReview->is_active = false;
            $userUnderReview->save();

            return response()->json(['message' => 'User deactivated successfully.'], 200);
        }

        // firm owner can fire anyone below them
        if ($loggedInUser->tokenCan(RoleEnum::firm_owner->value)) {
            // Check if the user being reviewed has one of the specified user types
            if (
                $userUnderReview->user_type == RoleEnum::branch_manager ||
                $userUnderReview->user_type == RoleEnum::sales ||
                $userUnderReview->user_type == RoleEnum::project_manager ||
                $userUnderReview->user_type == RoleEnum::marketing ||
                $userUnderReview->user_type == RoleEnum::firm_owner
            ) {
                // Check if the firm of the user being reviewed matches the firm of the logged-in user
                if ($userUnderReview->firm == $loggedInUser->firm ||
                    ($userUnderReview->branch && Branch::find($userUnderReview->branch)->firm == $loggedInUser->firm)) {
                    // Deactivate the user
                    $userUnderReview->is_active = false;
                    $userUnderReview->save();
                } else {
                    // Return unauthorized action response if the user does not belong to the same firm
                    return response()->json(['message' => 'Unauthorized action. User does not belong to your firm.'], 403);
                }
            } else {
                // Return unauthorized action response if the user type is not allowed
                return response()->json(['message' => 'Unauthorized action. Invalid user type.'], 403);
            }
            // Return success response after deactivating the user
            return response()->json(['message' => 'User deactivated successfully.'], 200);
        }

        return response()->json(['message' => 'Unauthorized action.'], 403);
    }


    public function activateUser($id)
    {
        $loggedInUser = Auth::user();
        $userUnderReview = User::findOrFail($id);

        // You cannot deactivate a super admin unless you are one
        if (!$loggedInUser->tokenCan(RoleEnum::super_admin->value)) {
            if ($userUnderReview->user_type == RoleEnum::super_admin) {
                abort(403, 'Unauthorized action. You need to be a ' . RoleEnum::super_admin->value . ' user to perform this action.');
            }
        }

        // Check if the logged-in user is a super admin and is trying to activate another super admin
        if ($loggedInUser->tokenCan(RoleEnum::super_admin->value)) {
            $userUnderReview->is_active = true;
            $userUnderReview->save();

            return response()->json(['message' => 'User activated successfully.'], 200);
        }

        // firm owner can activate anyone below them
        if ($loggedInUser->tokenCan(RoleEnum::firm_owner->value)) {
            // Check if the user being reviewed has one of the specified user types
            if (
                $userUnderReview->user_type == RoleEnum::branch_manager ||
                $userUnderReview->user_type == RoleEnum::sales ||
                $userUnderReview->user_type == RoleEnum::project_manager ||
                $userUnderReview->user_type == RoleEnum::marketing ||
                $userUnderReview->user_type == RoleEnum::firm_owner
            ) {
                // Check if the firm of the user being reviewed matches the firm of the logged-in user
                if ($userUnderReview->firm == $loggedInUser->firm ||
                    ($userUnderReview->branch && Branch::find($userUnderReview->branch)->firm == $loggedInUser->firm)) {
                    // Activate the user
                    $userUnderReview->is_active = true;
                    $userUnderReview->save();
                } else {
                    // Return unauthorized action response if the user does not belong to the same firm
                    return response()->json(['message' => 'Unauthorized action. User does not belong to your firm.'], 403);
                }
            } else {
                // Return unauthorized action response if the user type is not allowed
                return response()->json(['message' => 'Unauthorized action. Invalid user type.'], 403);
            }
            // Return success response after activating the user
            return response()->json(['message' => 'User activated successfully.'], 200);
        }

        return response()->json(['message' => 'Unauthorized action.'], 403);
    }


    /**
     * @OA\Patch(
     *     path="/edit-user/{id}",
     *     operationId="editUser",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="email", type="string"),
     *             @OA\Property(property="password", type="string"),
     *             @OA\Property(property="user_type", type="string"),
     *             @OA\Property(property="first_name", type="string"),
     *             @OA\Property(property="last_name", type="string"),
     *             @OA\Property(property="phone_local_number", type="string"),
     *             @OA\Property(property="phone_country_code", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User updated successfully"
     *     )
     * )
     */
    public function editUser(Request $request, $id)
    {
        $loggedInUser = Auth::user();
        $userToEdit = User::findOrFail($id);

        // Define validation rules
        $rules = [
            'email' => 'sometimes|string|email|max:255|unique:users,email,' . $id,
            'password' => 'sometimes|string|min:8|confirmed',
            'user_type' => 'sometimes|string',
            'first_name' => 'sometimes|string|max:255',
            'last_name' => 'sometimes|string|max:255',
            'phone_local_number' => 'sometimes|string|max:255',
            'phone_country_code' => 'sometimes|string|max:255',
        ];

        // Validate the request data
        $data = $request->validate($rules);

        // Super admin can edit any user's data except for other super admins
        if ($loggedInUser->tokenCan(RoleEnum::super_admin->value)) {
            if ($userToEdit->user_type == RoleEnum::super_admin->value && $loggedInUser->id != $id) {
                return response()->json(['message' => 'Unauthorized action. You cannot edit another super admin.'], 403);
            }

            // Update user data
            $userToEdit->update($data);
            return response()->json(['message' => 'User updated successfully.'], 200);
        }

        // Firm owner can edit any user's data within their firm or any branch of their firm
        if ($loggedInUser->tokenCan(RoleEnum::firm_owner->value)) {
            // Check if the user belongs to the same firm or branch within the firm
            if ($userToEdit->firm == $loggedInUser->firm ||
                ($userToEdit->branch && Branch::find($userToEdit->branch)->firm == $loggedInUser->firm)) {
                // Update user data
                $userToEdit->update($data);
                return response()->json(['message' => 'User updated successfully.'], 200);
            } else {
                return response()->json(['message' => 'Unauthorized action. User does not belong to your firm.'], 403);
            }
        }

        // Allow any user to edit their own data, except for branch or firm
        if ($loggedInUser->id == $userToEdit->id) {
            // Exclude firm and branch from the update
            unset($data['firm']);
            unset($data['branch']);

            // Update user data
            $userToEdit->update($data);
            return response()->json(['message' => 'User updated successfully.'], 200);
        }

        return response()->json(['message' => 'Unauthorized action.'], 403);
    }
}
