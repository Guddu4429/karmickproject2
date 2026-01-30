<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EnquirySent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $enquiryId;
    public string $emailTo;
    public string $emailFrom;
    public string $subject;
    public string $messageText;
    public ?string $attachmentPath;
    public ?string $attachmentName;
    public string $studentName;
    public ?string $studentClass;
    public ?string $studentStream;
    public ?string $studentRollNo;

    /**
     * Create a new event instance.
     */
    public function __construct(
        int $enquiryId,
        string $emailTo,
        string $emailFrom,
        string $subject,
        string $messageText,
        ?string $attachmentPath = null,
        ?string $attachmentName = null,
        string $studentName = '',
        ?string $studentClass = null,
        ?string $studentStream = null,
        ?string $studentRollNo = null
    ) {
        $this->enquiryId = $enquiryId;
        $this->emailTo = $emailTo;
        $this->emailFrom = $emailFrom;
        $this->subject = $subject;
        $this->messageText = $messageText;
        $this->attachmentPath = $attachmentPath;
        $this->attachmentName = $attachmentName;
        $this->studentName = $studentName;
        $this->studentClass = $studentClass;
        $this->studentStream = $studentStream;
        $this->studentRollNo = $studentRollNo;
    }
}
