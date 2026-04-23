<?php

use App\Http\Controllers\Api\AdminUserController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DepartmentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/departments', [DepartmentController::class, 'index']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::middleware('directeur')->group(function () {
        Route::post('/departments', [DepartmentController::class, 'store']);
        Route::patch('/departments/{department}', [DepartmentController::class, 'update']);
        Route::delete('/departments/{department}', [DepartmentController::class, 'destroy']);
        Route::get('/admin/users', [AdminUserController::class, 'index']);
        Route::patch('/admin/users/{user}/role', [AdminUserController::class, 'updateRole']);
        Route::get('analytics', [\App\Http\Controllers\Api\AnalyticsController::class, 'index']);
    });

    Route::apiResource('purchase-requests', \App\Http\Controllers\Api\PurchaseRequestController::class);
    Route::post('purchase-requests/{purchaseRequest}/approvals', [\App\Http\Controllers\Api\ApprovalController::class, 'store']);
    Route::apiResource('purchase-requests.devis', \App\Http\Controllers\Api\DevisController::class)->except(['show']);
    Route::post('purchase-requests/{purchaseRequest}/devis/{devi}/select', [\App\Http\Controllers\Api\DevisController::class, 'select']);
    Route::post('purchase-requests/{purchaseRequest}/attachments', [\App\Http\Controllers\Api\AttachmentController::class, 'store']);
    Route::get('attachments/{attachment}/download', [\App\Http\Controllers\Api\AttachmentController::class, 'download']);
    Route::delete('attachments/{attachment}', [\App\Http\Controllers\Api\AttachmentController::class, 'destroy']);
    Route::apiResource('purchase-requests.messages', \App\Http\Controllers\Api\MessageController::class)->only(['index', 'store']);
    Route::apiResource('suppliers', \App\Http\Controllers\Api\SupplierController::class);
    
    // Notifications
    Route::get('/notifications', [\App\Http\Controllers\Api\NotificationController::class, 'index']);
    Route::post('/notifications/{id}/read', [\App\Http\Controllers\Api\NotificationController::class, 'markAsRead']);
    Route::post('/notifications/read-all', [\App\Http\Controllers\Api\NotificationController::class, 'markAllAsRead']);
});
