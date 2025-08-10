<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AuditLogController extends Controller
{
    public function __construct()
    {
        // Only superadmins can access audit logs
        $this->middleware('superadmin');
    }

    /**
     * Display audit logs with filtering
     */
    public function index(Request $request): Response
    {
        $query = AuditLog::with('user')
            ->orderBy('created_at', 'desc');

        // Date range filtering
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        // Table filtering
        if ($request->filled('table_name')) {
            $query->where('table_name', $request->table_name);
        }

        // Action filtering
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        // User filtering
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Search by record ID
        if ($request->filled('record_id')) {
            $query->where('record_id', $request->record_id);
        }

        $perPage = $request->input('per_page', 25);
        $logs = $query->paginate($perPage);

        // Get available tables for filter dropdown
        $tables = AuditLog::distinct()->pluck('table_name')->sort()->values();

        // Get available actions for filter dropdown
        $actions = AuditLog::distinct()->pluck('action')->sort()->values();

        // Get available users for filter dropdown
        $userIds = AuditLog::distinct()->pluck('user_id')->filter()->values();
        $users = $userIds->isNotEmpty()
            ? User::whereIn('id', $userIds)->select('id', 'name', 'email')->get()
            : collect();

        return Inertia::render('audit/Index', [
            'logs' => $logs,
            'filters' => [
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'table_name' => $request->table_name,
                'action' => $request->action,
                'user_id' => $request->user_id,
                'record_id' => $request->record_id,
                'per_page' => $perPage,
            ],
            'filterOptions' => [
                'tables' => $tables,
                'actions' => $actions,
                'users' => $users,
            ],
        ]);
    }

    /**
     * Show specific audit log details
     */
    public function show(AuditLog $auditLog): Response
    {
        $auditLog->load('user');

        return Inertia::render('audit/Show', [
            'log' => $auditLog,
        ]);
    }

    /**
     * Get audit logs for a specific table
     */
    public function table(string $tableName, Request $request): Response
    {
        $query = AuditLog::with('user')
            ->where('table_name', $tableName)
            ->orderBy('created_at', 'desc');

        // Apply date filters
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $perPage = $request->input('per_page', 25);
        $logs = $query->paginate($perPage);

        return Inertia::render('audit/Table', [
            'tableName' => $tableName,
            'logs' => $logs,
            'filters' => [
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'per_page' => $perPage,
            ],
        ]);
    }

    /**
     * Get audit logs for a specific record
     */
    public function record(string $tableName, int $recordId): Response
    {
        $logs = AuditLog::with('user')
            ->where('table_name', $tableName)
            ->where('record_id', $recordId)
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('audit/Record', [
            'tableName' => $tableName,
            'recordId' => $recordId,
            'logs' => $logs,
        ]);
    }

    /**
     * Get audit logs for a specific user
     */
    public function user(int $userId, Request $request): Response
    {
        $user = User::findOrFail($userId);

        $query = AuditLog::with('user')
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc');

        // Apply date filters
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $perPage = $request->input('per_page', 25);
        $logs = $query->paginate($perPage);

        return Inertia::render('audit/User', [
            'user' => $user,
            'logs' => $logs,
            'filters' => [
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'per_page' => $perPage,
            ],
        ]);
    }

    /**
     * Export audit logs to CSV
     */
    public function export(Request $request)
    {
        $query = AuditLog::with('user')
            ->orderBy('created_at', 'desc');

        // Apply filters
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        if ($request->filled('table_name')) {
            $query->where('table_name', $request->table_name);
        }

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        $logs = $query->get();

        $filename = 'audit_logs_'.now()->format('Y-m-d_H-i-s').'.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ];

        $callback = function () use ($logs) {
            $file = fopen('php://output', 'w');

            // CSV headers
            fputcsv($file, [
                'ID', 'Table', 'Action', 'Record ID', 'User', 'IP Address',
                'User Agent', 'Created At', 'Updated At',
            ]);

            // CSV data
            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->id,
                    $log->table_name,
                    $log->action,
                    $log->record_id,
                    $log->user ? $log->user->name : 'Unknown',
                    $log->ip_address,
                    $log->user_agent,
                    $log->created_at,
                    $log->updated_at,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
