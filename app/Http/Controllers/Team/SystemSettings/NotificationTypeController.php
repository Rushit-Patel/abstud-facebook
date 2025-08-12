<?php

namespace App\Http\Controllers\Team\SystemSettings;

use App\DataTables\Team\Setting\NotificationTypeDataTable;
use App\Http\Controllers\Controller;
use App\Models\TeamNotificationType;
use Illuminate\Http\Request;

class NotificationTypeController extends Controller
{
    /**
     * Display a listing of NotificationType
     */
    public function index(NotificationTypeDataTable $NotificationTypeDataTable)
    {
        return $NotificationTypeDataTable->render('team.settings.notification-type.index');
    }

    /**
     * Show the form for creating a new NotificationType
     */
    public function create()
    {
        return view('team.settings.notification-type.create');
    }

    /**
     * Store a newly created NotificationType
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'type_key' => 'required|string|max:255|unique:team_notification_types,type_key',
                'description' => 'required|string',
                'is_active' => 'boolean',
            ], [
                'title.required' => 'Notification Type title is required.',
                'type_key.required' => 'Type key is required.',
                'type_key.unique' => 'This type key already exists.',
                'description.required' => 'Description is required.',
            ]);

            // Set default values
            $validated['is_active'] = $request->has('is_active');
            $validated['description'] = $request->description;
            $validated['type_key'] = $request->type_key;
            $validated['icon'] = $request->icon;
            $validated['color'] = $request->color;

            TeamNotificationType::create($validated);

            return redirect()->route('team.settings.notification-type.index')
                ->with('success', "Notification Type '{$validated['title']}' has been created successfully.");

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error creating Notification Type: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified Notification Type
     */
    public function show(TeamNotificationType $notificationType)
    {
        return view('team.settings.notification-type.show', compact('notificationType'));
    }

    /**
     * Show the form for editing the specified NotificationType
     */
    public function edit(TeamNotificationType $notificationType)
    {
        return view('team.settings.notification-type.edit', compact('notificationType'));
    }

    /**
     * Update the specified Notification Type
     */
    public function update(Request $request, TeamNotificationType $notificationType)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255|unique:team_notification_types,title,' . $notificationType->id,
                'type_key' => 'required|string|max:255|unique:team_notification_types,type_key,' . $notificationType->id,
                'description' => 'required|string',
                'is_active' => 'boolean',
            ], [
                'title.required' => 'Notification Type title is required.',
                'title.unique' => 'This Notification Type already exists.',
                'type_key.required' => 'Type key is required.',
                'type_key.unique' => 'This type key already exists.',
                'description.required' => 'Description is required.',
            ]);

            // Set default values
            $validated['description'] = $request->description;
            $validated['type_key'] = $request->type_key;
            $validated['icon'] = $request->icon;
            $validated['color'] = $request->color;
            $validated['is_active'] = $request->has('is_active');

            $notificationType->update($validated);

            return redirect()->route('team.settings.notification-type.index')
                ->with('success', "notification type '{$validated['title']}' has been updated successfully.");

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error updating notification type: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified Notification Type
     */
    public function destroy(TeamNotificationType $notificationType)
    {
        try {
            $name = $notificationType->title;
            $notificationType->delete();

            return redirect()->route('team.settings.notification-type.index')
                ->with('success', "Notification Type '{$name}' has been deleted successfully.");

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting Notification Type: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle Notification Type status
     */
    public function toggleStatus(TeamNotificationType $notificationType)
    {
        try {
            $notificationType->update(['is_active' => !$notificationType->is_active]);

            $status = $notificationType->is_active ? 'activated' : 'deactivated';

            return response()->json([
                'success' => true,
                'message' => "Notification Type '{$notificationType->title}' has been {$status} successfully.",
                'new_status' => $notificationType->is_active
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating Notification Type status: ' . $e->getMessage()
            ], 500);
        }
    }
}
