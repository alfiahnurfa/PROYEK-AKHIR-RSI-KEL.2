<?php

namespace App\Http\Controllers;

use App\Models\Session;
use App\Models\Feedback;
use App\Models\Registration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\SessionResource;
use Illuminate\Support\Facades\Validator;

class SessionController extends Controller
{
    public function index()
    {
        $session = Session::all();
        // return response()->json(['data' => $posts, "test" => "halo"]);
        return SessionResource::collection($session->loadMissing(['writer:id,username', 'comments:id,post_id,user_id,comments_content']));
    }

    public function register(Request $request, $sessionId)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 400);
        }

        $user = Auth::user();
        $session = Session::findOrFail($sessionId);

        $registeredCount = $session->registrations()->count();
        if ($registeredCount >= $session->max_attendees) {
            return response()->json([
                'status' => 'error',
                'message' => 'This session is already full.',
            ], 400);
        }

        $overlappingSession = $user->registrations()->where(function ($query) use ($session) {
            $query->whereBetween('start_time', [$session->start_time, $session->end_time])
                  ->orWhereBetween('end_time', [$session->start_time, $session->end_time])
                  ->orWhere(function ($query) use ($session) {
                      $query->where('start_time', '<=', $session->start_time)
                            ->where('end_time', '>=', $session->end_time);
                  });
        })->exists();

        if ($overlappingSession) {
            return response()->json([
                'status' => 'error',
                'message' => 'You are already registered for another session during this time period.',
            ], 400);
        }

        $registration = new Registration();
        $registration->user_id = $user->id;
        $registration->session_id = $session->id;
        $registration->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Successfully registered for the session.',
            'registration' => $registration,
        ], 200);
    }

    public function showRegistrations()
    {
        $user = Auth::user();
        $sessions = $user->registrations;

        return response()->json([
            'status' => 'success',
            'sessions' => $sessions,
        ], 200);
    }

    public function showSession($id)
    {
        $user = Auth::user();

        // Find the session by ID and ensure it belongs to the authenticated user
        $session = Session::where('id', $id)
                        ->where('user_id', $user->id)
                        ->first();

        if (!$session) {
            return response()->json([
                'status' => 'error',
                'message' => 'Session not found or not authorized.',
            ], 404);
        }

        // Return the session details
        return response()->json([
            'status' => 'success',
            'session' => $session,
        ]);
    }

    // Update a specific session
    public function updateSession(Request $request, $id)
    {
        $user = Auth::user();

        // Validate input
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'max_attendees' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 400);
        }

        // Find the session
        $session = Session::where('id', $id)
                        ->where('user_id', $user->id)
                        ->first();

        if (!$session) {
            return response()->json([
                'status' => 'error',
                'message' => 'Session not found or not authorized.',
            ], 404);
        }

        // Update session details
        $session->title = $request->title;
        $session->description = $request->description;
        $session->start_time = $request->start_time;
        $session->end_time = $request->end_time;
        $session->max_attendees = $request->max_attendees;
        $session->save();

        // Return success response
        return response()->json([
            'status' => 'success',
            'message' => 'Session updated successfully.',
            'session' => $session,
        ]);
    }

    // Delete a specific session
    public function deleteSession($id)
    {
        $user = Auth::user();

        // Find the session
        $session = Session::where('id', $id)
                        ->where('user_id', $user->id)
                        ->first();

        if (!$session) {
            return response()->json([
                'status' => 'error',
                'message' => 'Session not found or not authorized.',
            ], 404);
        }

        // Delete the session
        $session->delete();

        // Return success response
        return response()->json([
            'status' => 'success',
            'message' => 'Session deleted successfully.',
        ]);
    }

    public function deleteSessionEvent($sessionId)
    {
        $session = Session::findOrFail($sessionId);

        // Delete the session
        $session->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Session deleted successfully.',
        ], 200);
    }

    public function deleteFeedback($sessionId, $feedbackId)
    {
        $feedback = Feedback::findOrFail($feedbackId);
        
        // Delete the feedback
        $feedback->delete();
    
        return response()->json([
            'status' => 'success',
            'message' => 'Feedback deleted successfully.',
        ], 200);
    }
}
