<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FeedbackController extends Controller
{
    // Store feedback
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id', // Ensure the user exists
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'rating' => 'required|integer|between:1,5', // Rating between 1 and 5
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $feedback = Feedback::create($request->all());
        return response()->json(['message' => 'Feedback created successfully.', 'feedback' => $feedback], 201);
    }

    // Get all feedback
    public function index()
    {
        $feedback = Feedback::with('user')->get(); // Load user information
        return response()->json($feedback);
    }

    // Get specific feedback
    public function show($id)
    {
        $feedback = Feedback::with('user')->find($id);
        if (!$feedback) {
            return response()->json(['message' => 'Feedback not found.'], 404);
        }
        return response()->json($feedback);
    }

    // Delete feedback
    public function destroy($id)
    {
        $feedback = Feedback::find($id);
        if (!$feedback) {
            return response()->json(['message' => 'Feedback not found.'], 404);
        }

        $feedback->delete();
        return response()->json(['message' => 'Feedback deleted successfully.'], 200);
    }
}

