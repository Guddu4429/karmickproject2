<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;

class EnquiryMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public string $emailFrom;
    public string $enquirySubject;
    public string $messageText;
    public ?string $attachmentPath;
    public ?string $attachmentName;
    public string $studentName;
    public ?string $studentClass;
    public ?string $studentStream;
    public ?string $studentRollNo;

    /**
     * Create a new message instance.
     */
    public function __construct(
        string $emailFrom,
        string $enquirySubject,
        string $messageText,
        ?string $attachmentPath = null,
        ?string $attachmentName = null,
        string $studentName = '',
        ?string $studentClass = null,
        ?string $studentStream = null,
        ?string $studentRollNo = null
    ) {
        $this->emailFrom = $emailFrom;
        $this->enquirySubject = $enquirySubject;
        $this->messageText = $messageText;
        $this->attachmentPath = $attachmentPath;
        $this->attachmentName = $attachmentName;
        $this->studentName = $studentName;
        $this->studentClass = $studentClass;
        $this->studentStream = $studentStream;
        $this->studentRollNo = $studentRollNo;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Enquiry: ' . $this->enquirySubject,
            replyTo: [$this->emailFrom],
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.enquiry',
            with: [
                'emailFrom' => $this->emailFrom,
                'enquirySubject' => $this->enquirySubject,
                'messageText' => $this->messageText,
                'studentName' => $this->studentName,
                'studentClass' => $this->studentClass,
                'studentStream' => $this->studentStream,
                'studentRollNo' => $this->studentRollNo,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        if ($this->attachmentPath && file_exists(storage_path('app/public/' . $this->attachmentPath))) {
            return [
                Attachment::fromStorage('public/' . $this->attachmentPath)
                    ->as($this->attachmentName ?? basename($this->attachmentPath)),
            ];
        }

        return [];
    }
}
