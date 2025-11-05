<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\SessionProposalController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::get('/session', [SessionController::class, 'index']);
Route::post('/sessions/{sessionId}/register', [SessionController::class, 'register'])->middleware(['auth:sanctum','role:user']);
Route::get('/user/registrations', [SessionController::class, 'showRegistrations'])->middleware(['auth:sanctum','role:user']);
Route::get('/sessions/{id}', [SessionController::class, 'showSession'])->middleware(['auth:sanctum','role:user']);
Route::put('/sessions/{id}', [SessionController::class, 'updateSession'])->middleware(['auth:sanctum','role:user']);
Route::delete('/sessions/{id}', [SessionController::class, 'deleteSession'])->middleware(['auth:sanctum','role:user']);

Route::post('/session-proposals/create', [SessionProposalController::class, 'createProposal'])->middleware(['auth:sanctum','role:user']);
Route::get('/session-proposals/{id}', [SessionProposalController::class, 'showProposal'])->middleware(['auth:sanctum','role:user']);
Route::put('/session-proposals/{id}', [SessionProposalController::class, 'updateProposal'])->middleware(['auth:sanctum','role:user']);
Route::delete('/session-proposals/{id}', [SessionProposalController::class, 'deleteProposal'])->middleware(['auth:sanctum','role:user']);

Route::get('/session-proposals', [SessionProposalController::class, 'viewSessionProposals'])->middleware(['auth:sanctum','role:event_coordinator']);
Route::post('/session-proposals/{proposalId}/accept', [SessionProposalController::class, 'acceptProposal'])->middleware(['auth:sanctum','role:event_coordinator']);
Route::post('/session-proposals/{proposalId}/reject', [SessionProposalController::class, 'rejectProposal'])->middleware(['auth:sanctum','role:event_coordinator']);
Route::delete('/session/{id}', [SessionController::class, 'deleteSessionEvent'])->middleware(['auth:sanctum','role:event_coordinator']);
Route::delete('/sessions/{sessionId}/feedback/{feedbackId}', [SessionController::class, 'deleteFeedback'])->middleware(['auth:sanctum','role:event_coordinator']);

Route::post('/users', [AdminController::class, 'addUser'])->middleware(['auth:sanctum','role:admin']);
Route::delete('/users/{userId}', [AdminController::class, 'removeUser'])->middleware(['auth:sanctum','role:admin']);

Route::post('/feedback', [FeedbackController::class, 'store'])->middleware(['auth:sanctum','role:user']);

Route::post('/register', [AuthenticationController::class, 'register']);
Route::post('/login', [AuthenticationController::class, 'login']);
Route::get('/me', [AuthenticationController::class, 'me'])->middleware(['auth:sanctum','role:user']);
Route::get('/search/{id}', [AuthenticationController::class, 'search'])->middleware(['auth:sanctum','role:user']);
Route::put('/update', [AuthenticationController::class, 'updateProfile'])->middleware(['auth:sanctum','role:user']);
Route::get('/logout', [AuthenticationController::class, 'logout'])->middleware(['auth:sanctum','role:user,admin,event_coordinator']);
