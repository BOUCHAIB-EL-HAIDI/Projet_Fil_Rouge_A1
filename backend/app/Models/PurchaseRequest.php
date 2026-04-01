<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseRequest extends Model
{
    protected $fillable = ['title', 'description', 'status', 'priority', 'user_id', 'department_id'];

    protected $casts = [
        'status' => \App\Enums\PurchaseRequestStatus::class,
        'priority' => \App\Enums\PurchaseRequestPriority::class,
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function items()
    {
        return $this->hasMany(RequestItem::class);
    }

    public function approvals()
    {
        return $this->hasMany(Approval::class);
    }

    public function statusHistories()
    {
        return $this->hasMany(RequestStatusHistory::class);
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function attachments()
    {
        return $this->hasMany(Attachment::class);
    }

    public function devis()
    {
        return $this->hasMany(Devis::class);
    }

    public function notifyInvolvedUsers($title, $message, $excludeUserId = null)
    {
        $users = collect();
        if ($this->user) $users->push($this->user);
        if ($this->department?->manager) $users->push($this->department->manager);
        
        $directeurs = \App\Models\User::where('role', 'directeur')->get();
        $users = $users->merge($directeurs);
        
        $acheteurs = \App\Models\User::where('role', 'acheteur')->get();
        $users = $users->merge($acheteurs);

        $users = $users->unique('id');
        if ($excludeUserId) {
            $users = $users->reject(fn($u) => $u->id === $excludeUserId);
        }

        foreach ($users as $u) {
            $u->notify(new \App\Notifications\PurchaseRequestNotification([
                'title' => $title,
                'message' => $message,
                'purchase_request_id' => $this->id,
                'type' => 'status_update'
            ]));
        }
    }
}
