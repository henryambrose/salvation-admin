<?php

namespace App\Services;

class ApiResponseService
{
    /**
     * Return a success response.
     *
     * @param  mixed  $data
     * @param  int  $status
     * @return \Illuminate\Http\JsonResponse
     */
    public function success($data = [], $status = 200)
    {
        return response()->json([
            'status' => 'success',
            'data' => $data,
        ], $status);
    }

    /**
     * Return an error response.
     *
     * @param  string  $message
     * @param  int  $status
     * @return \Illuminate\Http\JsonResponse
     */
    public function error($message = 'An error occurred', $status = 400)
    {
        return response()->json([
            'status' => 'error',
            'data' => ['message' => $message],
        ], $status);
    }
}
