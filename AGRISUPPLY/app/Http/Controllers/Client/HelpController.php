<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\HelpRequest;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HelpController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        if ($user->isAdmin()) {
            // Admin sees all help requests
            $helpRequests = HelpRequest::with(['user', 'assignedTo'])
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            // Users see only their own help requests
            $helpRequests = HelpRequest::where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->get();
        }

        return view('client.help.index', compact('helpRequests'));
    }

    public function create()
    {
        return view('client.help.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|in:low,medium,high',
        ]);

        // Create help request
        $helpRequest = HelpRequest::create([
            'user_id' => Auth::id(),
            'subject' => $request->subject,
            'description' => $request->description,
            'priority' => $request->priority,
            'status' => 'pending',
        ]);

        // Notify all admins
        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            Notification::create([
                'user_id' => $admin->id,
                'type' => 'help_request',
                'title' => 'New Help Request',
                'message' => Auth::user()->name . ' submitted a new help request: ' . $request->subject,
                'data' => [
                    'help_request_id' => $helpRequest->id,
                    'priority' => $request->priority,
                    'user_name' => Auth::user()->name,
                ]
            ]);
        }

        return redirect()->route('client.help.index')->with('success', 'Your help request has been submitted successfully!');
    }

    public function show($id)
    {
        $helpRequest = HelpRequest::with(['user', 'assignedTo'])->findOrFail($id);
        
        // Check if user can view this help request
        if (!Auth::user()->isAdmin() && $helpRequest->user_id !== Auth::id()) {
            abort(403);
        }

        return view('client.help.show', compact('helpRequest'));
    }

    public function edit($id)
    {
        $helpRequest = HelpRequest::findOrFail($id);
        
        // Only admins or the original user can edit (when status is pending)
        if (!Auth::user()->isAdmin() && ($helpRequest->user_id !== Auth::id() || $helpRequest->status !== 'pending')) {
            abort(403);
        }

        return view('client.help.edit', compact('helpRequest'));
    }

    public function update(Request $request, $id)
    {
        $helpRequest = HelpRequest::findOrFail($id);
        
        if (Auth::user()->isAdmin()) {
            // Admin update
            $request->validate([
                'status' => 'required|in:pending,in_progress,resolved,closed',
                'admin_response' => 'nullable|string',
                'assigned_to' => 'nullable|exists:users,id',
            ]);

            $oldStatus = $helpRequest->status;
            
            $helpRequest->update([
                'status' => $request->status,
                'admin_response' => $request->admin_response,
                'assigned_to' => $request->assigned_to,
                'resolved_at' => $request->status === 'resolved' ? now() : null,
            ]);

            // Notify user of status change
            if ($oldStatus !== $request->status) {
                Notification::create([
                    'user_id' => $helpRequest->user_id,
                    'type' => 'help_response',
                    'title' => 'Help Request Updated',
                    'message' => 'Your help request "' . $helpRequest->subject . '" status has been updated to ' . ucfirst($request->status),
                    'data' => [
                        'help_request_id' => $helpRequest->id,
                        'status' => $request->status,
                        'admin_name' => Auth::user()->name,
                    ]
                ]);
            }

            return redirect()->route('client.help.index')->with('success', 'Help request updated successfully!');
        } else {
            // User update (only for pending requests)
            if ($helpRequest->status !== 'pending') {
                abort(403);
            }

            $request->validate([
                'subject' => 'required|string|max:255',
                'description' => 'required|string',
                'priority' => 'required|in:low,medium,high',
            ]);

            $helpRequest->update([
                'subject' => $request->subject,
                'description' => $request->description,
                'priority' => $request->priority,
            ]);

            return redirect()->route('client.help.index')->with('success', 'Help request updated successfully!');
        }
    }

    public function destroy($id)
    {
        $helpRequest = HelpRequest::findOrFail($id);
        
        // Only admins or the original user can delete (when status is pending)
        if (!Auth::user()->isAdmin() && ($helpRequest->user_id !== Auth::id() || $helpRequest->status !== 'pending')) {
            abort(403);
        }

        $helpRequest->delete();

        return response()->json(['success' => true]);
    }
}