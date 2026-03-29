<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attachment;
use App\Models\PurchaseRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AttachmentController extends Controller
{
    public function store(Request $request, PurchaseRequest $purchaseRequest)
    {
        $request->validate([
            'file' => 'required|file|max:10240', // 10MB max
            'originalName' => 'required|string'
        ]);

        $path = $request->file('file')->store('attachments');

        $attachment = $purchaseRequest->attachments()->create([
            'filePath' => $path,
            'original_name' => $request->originalName,
            'user_id' => $request->user()->id,
        ]);

        return $attachment->load('user');
    }

    public function download(Attachment $attachment)
    {
        if (!Storage::exists($attachment->filePath)) {
            abort(404);
        }

        return Storage::download($attachment->filePath, $attachment->original_name);
    }
}
