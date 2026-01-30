<?php

namespace App\Listeners;

use App\Events\EnquirySent;
use App\Mail\EnquiryMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendEnquiryEmail implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(EnquirySent $event): void
    {
        Mail::to($event->emailTo)->send(
            new EnquiryMail(
                $event->emailFrom,
                $event->subject,
                $event->messageText,
                $event->attachmentPath,
                $event->attachmentName,
                $event->studentName,
                $event->studentClass,
                $event->studentStream,
                $event->studentRollNo
            )
        );
    }
}
