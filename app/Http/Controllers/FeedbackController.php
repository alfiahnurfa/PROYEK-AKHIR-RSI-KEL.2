<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\FeedbackResource;

class FeedbackController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'session_id' => 'required|exists:sessions,id',
            'comments' => 'required',
        ]);

        $request["user_id"] = Auth::user()->id;
        $feedback = Feedback::create($request->all());
        return new FeedbackResource($feedback->loadMissing('comentator:id,username'));
    }

}
