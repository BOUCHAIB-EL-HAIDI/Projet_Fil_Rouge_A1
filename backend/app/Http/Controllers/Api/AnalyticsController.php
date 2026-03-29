<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PurchaseRequest;
use App\Models\Department;
use App\Models\Approval;
use App\Models\Devis;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        if ($user->role !== 'directeur') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $totalRequests = PurchaseRequest::count();
        $pendingRequests = PurchaseRequest::whereIn('status', ['pending_manager', 'pending_directeur'])->count();
        $approvedRequests = PurchaseRequest::where('status', 'approved')->count();
        $rejectedRequests = PurchaseRequest::where('status', 'rejected')->count();
        $deliveredRequests = PurchaseRequest::where('status', 'delivered')->count();

        $requestsByDept = Department::withCount('purchaseRequests')->get();
        
        $supplierStats = [
            'total_suppliers' => Supplier::count(),
            'devis_received' => Devis::count(),
            'devis_accepted' => Devis::where('status', 'selected')->count(),
        ];

        return response()->json([
            'metrics' => [
                'total_requests' => $totalRequests,
                'pending_requests' => $pendingRequests,
                'approved_requests' => $approvedRequests,
                'rejected_requests' => $rejectedRequests,
                'delivered_requests' => $deliveredRequests,
            ],
            'requests_by_department' => $requestsByDept,
            'supplier_analytics' => $supplierStats,
            'average_delivery_time' => '5.2 jours (moy)',
        ]);
    }
}
