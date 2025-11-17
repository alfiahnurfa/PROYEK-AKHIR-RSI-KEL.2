<?php

namespace App\Http\Controllers;

use App\Models\SessionProposal;
use App\Models\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\SessionResource;

class SessionProposalController extends Controller
{
    public function createProposal(Request $request)
    {
        // Validate the incoming request data
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        // If validation fails, return error response
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 400);
        }

        // Get the authenticated user
        $user = Auth::user();

        // Define the time period for the restriction (e.g., one session proposal per day)
        $startOfPeriod = now()->startOfDay(); // Start of the current day
        $endOfPeriod = now()->endOfDay(); // End of the current day

        // Check if the user already has a session proposal in the given time period
        $existingProposal = SessionProposal::where('user_id', $user->id)
                                        ->whereBetween('created_at', [$startOfPeriod, $endOfPeriod])
                                        ->first();

        // If a proposal already exists within the time period, return an error
        if ($existingProposal) {
            return response()->json([
                'status' => 'error',
                'message' => 'You can only create one session proposal per day.',
            ], 400);
        }

        // Create a new session proposal
        $sessionProposal = new SessionProposal();
        $sessionProposal->user_id = $user->id;
        $sessionProposal->title = $request->title;
        $sessionProposal->description = $request->description;
        $sessionProposal->status = 'pending'; // Initial status is 'pending'
        $sessionProposal->save();

        // Return success response with the created session proposal details
        return response()->json([
            'status' => 'success',
            'message' => 'Session proposal created successfully.',
            'session_proposal' => $sessionProposal,
        ], 201);
    }


    public function showProposal($id)
    {
        $user = Auth::user();

        // Find the session proposal by ID and ensure it belongs to the authenticated user
        $sessionProposal = SessionProposal::where('id', $id)
                                        ->where('user_id', $user->id)
                                        ->first();

        if (!$sessionProposal) {
            return response()->json([
                'status' => 'error',
                'message' => 'Session proposal not found or not authorized.',
            ], 404);
        }

        // Return the session proposal details
        return response()->json([
            'status' => 'success',
            'session_proposal' => $sessionProposal,
        ]);
    }

    public function updateProposal(Request $request, $id)
    {
        $user = Auth::user();
    
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            // 'status' => 'in:pending,accepted,rejected',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 400);
        }
    
        $sessionProposal = SessionProposal::where('id', $id)
                                          ->where('user_id', $user->id)
                                          ->first();
    
        if (!$sessionProposal) {
            return response()->json([
                'status' => 'error',
                'message' => 'Session proposal not found or not authorized.',
            ], 404);
        }
    
        $sessionProposal->title = $request->title;
        $sessionProposal->description = $request->description;
        $sessionProposal->save();
    
        return response()->json([
            'status' => 'success',
            'message' => 'Session proposal updated successfully.',
            'session_proposal' => $sessionProposal,
        ]);
    }    

    public function deleteProposal($id)
    {
        $user = Auth::user();
    
        $sessionProposal = SessionProposal::where('id', $id)
                                          ->where('user_id', $user->id)
                                          ->first();
    
        if (!$sessionProposal) {
            return response()->json([
                'status' => 'error',
                'message' => 'Session proposal not found or not authorized.',
            ], 404);
        }
    
        $sessionProposal->delete();
    
        return response()->json([
            'status' => 'success',
            'message' => 'Session proposal deleted successfully.',
        ]);
    }

    public function viewSessionProposals()
    {
        // Retrieve all session proposals (adjust as needed for filtering or sorting)
        $sessionProposals = SessionProposal::all();

        // Return the session proposals in the response
        return response()->json([
            'status' => 'success',
            'message' => 'Session proposals fetched successfully.',
            'data' => $sessionProposals
        ], 200);
    }

    public function acceptProposal($proposalId)
    {
        $proposal = SessionProposal::findOrFail($proposalId);
        
        // Change the status to accepted
        $proposal->status = 'accepted';
        $proposal->save();

        // Create the session (if needed)
        $session = new Session();
        $session->user_id = $proposal->user_id;
        $session->title = $proposal->title;
        $session->description = $proposal->description;
        $session->start_time = now(); // Example start time
        $session->end_time = now()->addHours(1); // Example end time
        $session->max_attendees = 100; // Example max attendees
        $session->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Session proposal accepted and session created.',
            'session' => $session
        ], 200);
    }

    // Reject a session proposal
    public function rejectProposal($proposalId)
    {
        $proposal = SessionProposal::findOrFail($proposalId);
        
        // Change the status to rejected
        $proposal->status = 'rejected';
        $proposal->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Session proposal rejected.',
        ], 200);
    }
}
