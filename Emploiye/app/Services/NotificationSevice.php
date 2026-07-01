<?php

namespace App\Services;

use App\Events\NotificationSent;
use App\Mail\EmployeeNotificationMail;
use App\Models\Notification;
use App\Models\Project;
use App\Models\Absence;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class NotificationSevice{

    public function notifyUser(User $user,string $type,string $title,string $message,array $data = []): void 
    {

        $notification = Notification::create([
            'user_id' => $user->id,
            'type'    => $type,
            'title'   => $title,
            'message' => $message,
            'data'    => $data,
            'is_read' => false,
        ]);

        broadcast(new NotificationSent($notification))->toOthers();

        Mail::to($user->email)->send(
            new EmployeeNotificationMail(
                $title,
                $message
            )
        );
    }

}