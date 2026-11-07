<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\JobCard;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class JobCardController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $tenantId = $request->user()->tenant_id;

        $query = JobCard::where('tenant_id', $tenantId)
            ->with(['order.customer', 'orderItem.garmentType', 'assignedTailor', 'cutter'])
            ->latest();

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        if ($request->user()->role === 'tailor') {
            $query->where(function ($q) use ($request) {
                $q->where('assigned_tailor_user_id', $request->user()->id)
                  ->orWhere('cutter_user_id', $request->user()->id);
            });
        }

        $jobCards = $query->paginate($request->get('per_page', 20));

        return response()->json($jobCards);
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $tenantId = $request->user()->tenant_id;

        $jobCard = JobCard::where('tenant_id', $tenantId)
            ->with(['order.customer', 'orderItem', 'assignedTailor', 'cutter', 'tasks'])
            ->findOrFail($id);

        return response()->json(['job_card' => $jobCard]);
    }

    public function updateStatus(Request $request, int $id): JsonResponse
    {
        $tenantId = $request->user()->tenant_id;
        $jobCard = JobCard::where('tenant_id', $tenantId)->findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:pending,in_cutting,cutting_completed,in_stitching,stitching_completed,quality_inspection,completed',
            'notes' => 'nullable|string',
        ]);

        $updateData = ['status' => $validated['status']];

        if ($validated['status'] === 'in_cutting' && empty($jobCard->started_at)) {
            $updateData['started_at'] = now();
        }

        if ($validated['status'] === 'completed') {
            $updateData['completed_at'] = now();
        }

        $jobCard->update($updateData);

        return response()->json([
            'message' => "Job card status updated to {$validated['status']}.",
            'job_card' => $jobCard,
        ]);
    }
}
