<?php

namespace App\Livewire\Students;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Events\EnquirySent;

class Enquiry extends Component
{
    use WithFileUploads;

    public $student = null;
    public $subject = '';
    public $message = '';
    public $attachment = null;
    public array $enquiries = [];
    public ?object $selectedEnquiry = null;

    protected $rules = [
        'subject' => 'required|min:5|max:200',
        'message' => 'required|min:10|max:2000',
        'attachment' => 'nullable|file|max:5120|mimes:pdf,doc,docx,jpg,jpeg,png',
    ];

    public function mount(): void
    {
        $user = Auth::user();
        if (! $user) {
            return;
        }

        $roleName = DB::table('roles')->where('id', $user->role_id)->value('name');
        if ($roleName !== 'Guardian') {
            return;
        }

        $studentId = session('active_student_id');
        if (! $studentId) {
            $this->redirect(route('guardian.children'), navigate: true);
            return;
        }

        $guardianId = DB::table('guardians')->where('user_id', $user->id)->value('id');

        $this->student = DB::table('students')
            ->leftJoin('classes', 'classes.id', '=', 'students.class_id')
            ->leftJoin('streams', 'streams.id', '=', 'students.stream_id')
            ->leftJoin('guardians', 'guardians.id', '=', 'students.guardian_id')
            ->where('students.id', $studentId)
            ->where('students.guardian_id', $guardianId)
            ->select([
                'students.id',
                'students.first_name',
                'students.last_name',
                'students.roll_no',
                'classes.name as class_name',
                'streams.name as stream_name',
                'guardians.email as guardian_email',
            ])
            ->first();

        if (! $this->student) {
            abort(403, 'You are not allowed to view this student.');
        }

        $this->loadEnquiries();
    }

    public function loadEnquiries(): void
    {
        if (! $this->student) {
            return;
        }

        $this->enquiries = DB::table('enquiries')
            ->where('student_id', $this->student->id)
            ->orderByDesc('created_at')
            ->get()
            ->all();
    }

    public function submit(): void
    {
        $this->validate();

        $user = Auth::user();
        $guardianEmail = $user->email;

        // Get principal's email
        $principalEmail = DB::table('users')
            ->join('roles', 'roles.id', '=', 'users.role_id')
            ->where('roles.name', 'Principal')
            ->value('users.email') ?? 'principal@dpsrpk.edu';

        // Handle attachment upload
        $attachmentPath = null;
        $attachmentName = null;
        if ($this->attachment) {
            $attachmentName = $this->attachment->getClientOriginalName();
            $attachmentPath = $this->attachment->store('enquiry-attachments', 'public');
        }

        $enquiryId = DB::table('enquiries')->insertGetId([
            'student_id' => $this->student->id,
            'email_from' => $guardianEmail,
            'email_to' => $principalEmail,
            'subject' => $this->subject,
            'message_text' => $this->message,
            'message_html' => nl2br(e($this->message)),
            'attachment_path' => $attachmentPath,
            'attachment_name' => $attachmentName,
            'is_read' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Dispatch event to send email with student details
        EnquirySent::dispatch(
            $enquiryId,
            $principalEmail,
            $guardianEmail,
            $this->subject,
            $this->message,
            $attachmentPath,
            $attachmentName,
            $this->student->first_name . ' ' . $this->student->last_name,
            $this->student->class_name,
            $this->student->stream_name,
            $this->student->roll_no
        );

        $this->reset('subject', 'message', 'attachment');
        $this->loadEnquiries();
        session()->flash('success', 'Your enquiry has been sent to the Principal.');
    }

    public function removeAttachment(): void
    {
        $this->attachment = null;
    }

    public function viewEnquiry(int $id): void
    {
        $this->selectedEnquiry = DB::table('enquiries')->where('id', $id)->first();
    }

    public function closeEnquiry(): void
    {
        $this->selectedEnquiry = null;
    }

    public function render()
    {
        return view('livewire.students.enquiry')
            ->layout('layouts.student');
    }
}
