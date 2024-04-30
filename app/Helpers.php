<?php

if (!function_exists('is_user_admin')) {
    /**
     * Check if the user is an admin.
     *
     * @param  mixed  $user
     * @return bool|\Illuminate\Http\JsonResponse
     */
    function is_user_admin($user)
    {
        if ($user->user_type != 'admin') {
            return response()->json(['error' => 'forbidden', 'message' => 'You need admin privileges'], 403);
        }
        return true;
    }
}

if (!function_exists('check_store_match')) {
    /**
     * Check if the store attribute of both the user and another object match.
     *
     * @param  mixed  $user
     * @param  mixed  $other
     * @return bool|\Illuminate\Http\JsonResponse
     */
    function check_store_match($user, $other)
    {
        if ($user->store != $other->store) {
            return response()->json(['error' => 'forbidden', 'message' => 'You need to belong to this store'], 403);
        }
        return true;
    }
}
