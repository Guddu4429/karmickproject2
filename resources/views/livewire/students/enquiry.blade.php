<div>
    <!-- Header -->
    <div class="bg-primary text-white rounded-4 p-4 mb-4">
        <h4 class="mb-1">Enquiry to Principal</h4>
        <small>
            @if(!empty($student))
                {{ $student->first_name }} {{ $student->last_name }} • Class {{ $student->class_name ?? '-' }}
            @else
                Send enquiries or concerns to the school principal
            @endif
        </small>
    </div>

    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4">
        <!-- New Enquiry Form -->
        <div class="col-lg-5">
            <div class="card shadow-sm border-0 rounded-4 p-4">
                <h5 class="fw-semibold mb-3">
                    <i class="bi bi-envelope-plus me-2 text-primary"></i>New Enquiry
                </h5>

                <form wire:submit.prevent="submit">
                    <div class="mb-3">
                        <label class="form-label">Subject <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control @error('subject') is-invalid @enderror" 
                               wire:model.defer="subject"
                               placeholder="e.g., Query about fee structure">
                        @error('subject')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Message <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('message') is-invalid @enderror" 
                                  wire:model.defer="message"
                                  rows="5"
                                  placeholder="Describe your query or concern in detail..."></textarea>
                        @error('message')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Attachment <small class="text-muted">(optional)</small></label>
                        <input type="file" 
                               class="form-control @error('attachment') is-invalid @enderror" 
                               wire:model="attachment"
                               accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                        @error('attachment')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Max 5MB. Allowed: PDF, DOC, DOCX, JPG, PNG</small>
                        
                        @if($attachment)
                            <div class="mt-2 p-2 bg-light rounded d-flex justify-content-between align-items-center">
                                <span class="small">
                                    <i class="bi bi-paperclip me-1"></i>
                                    {{ $attachment->getClientOriginalName() }}
                                    <span class="text-muted">({{ number_format($attachment->getSize() / 1024, 1) }} KB)</span>
                                </span>
                                <button type="button" class="btn btn-sm btn-outline-danger" wire:click="removeAttachment">
                                    <i class="bi bi-x"></i>
                                </button>
                            </div>
                        @endif

                        <div wire:loading wire:target="attachment" class="mt-2">
                            <span class="spinner-border spinner-border-sm text-primary"></span>
                            <small class="text-muted">Uploading...</small>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-muted">
                            <i class="bi bi-info-circle me-1"></i>Sent to: Principal
                        </small>
                        <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                            <span wire:loading wire:target="submit">
                                <span class="spinner-border spinner-border-sm me-1"></span>Sending...
                            </span>
                            <span wire:loading.remove wire:target="submit">
                                <i class="bi bi-send me-1"></i>Send Enquiry
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Enquiry History -->
        <div class="col-lg-7">
            <div class="card shadow-sm border-0 rounded-4 p-4">
                <h5 class="fw-semibold mb-3">
                    <i class="bi bi-clock-history me-2 text-primary"></i>Previous Enquiries
                </h5>

                @if(count($enquiries) > 0)
                    <div class="enquiry-list" style="max-height: 450px; overflow-y: auto;">
                        @foreach($enquiries as $enq)
                            <div class="enquiry-item p-3 mb-2 rounded-3 border {{ $enq->is_read ? 'bg-light' : 'bg-white border-primary' }}" 
                                 style="cursor: pointer;"
                                 wire:click="viewEnquiry({{ $enq->id }})">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1 fw-semibold">
                                            @if(!$enq->is_read)
                                                <span class="badge bg-primary me-1">New</span>
                                            @endif
                                            {{ Str::limit($enq->subject, 40) }}
                                        </h6>
                                        <p class="mb-1 small text-muted">
                                            {{ Str::limit($enq->message_text, 80) }}
                                        </p>
                                        <small class="text-muted">
                                            <i class="bi bi-calendar me-1"></i>
                                            {{ \Carbon\Carbon::parse($enq->created_at)->format('d M Y, g:i A') }}
                                            @if($enq->attachment_path)
                                                <span class="ms-2"><i class="bi bi-paperclip"></i> Attachment</span>
                                            @endif
                                        </small>
                                    </div>
                                    <i class="bi bi-chevron-right text-muted"></i>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                        <p class="mb-0">No enquiries sent yet.</p>
                        <small>Your sent enquiries will appear here.</small>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Enquiry Detail Modal -->
    @if($selectedEnquiry)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content rounded-4 border-0 shadow">
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title fw-semibold">{{ $selectedEnquiry->subject }}</h5>
                        <button type="button" class="btn-close" wire:click="closeEnquiry"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <small class="text-muted d-block mb-2">
                                <i class="bi bi-calendar me-1"></i>
                                Sent on {{ \Carbon\Carbon::parse($selectedEnquiry->created_at)->format('d M Y, g:i A') }}
                            </small>
                            <small class="text-muted d-block">
                                <i class="bi bi-envelope me-1"></i>
                                To: {{ $selectedEnquiry->email_to }}
                            </small>
                        </div>
                        <hr>
                        <div class="message-content">
                            {!! $selectedEnquiry->message_html ?? nl2br(e($selectedEnquiry->message_text)) !!}
                        </div>

                        @if($selectedEnquiry->attachment_path)
                            <div class="mt-3 p-3 bg-light rounded">
                                <strong class="small"><i class="bi bi-paperclip me-1"></i>Attachment:</strong>
                                <a href="{{ asset('storage/' . $selectedEnquiry->attachment_path) }}" 
                                   target="_blank" 
                                   class="btn btn-sm btn-outline-primary ms-2">
                                    <i class="bi bi-download me-1"></i>
                                    {{ $selectedEnquiry->attachment_name ?? 'Download' }}
                                </a>
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <span class="badge {{ $selectedEnquiry->is_read ? 'bg-success' : 'bg-warning text-dark' }}">
                            {{ $selectedEnquiry->is_read ? 'Read by Principal' : 'Pending' }}
                        </span>
                        <button type="button" class="btn btn-secondary btn-sm" wire:click="closeEnquiry">Close</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <style>
    .enquiry-item {
        transition: all 0.15s ease;
    }
    .enquiry-item:hover {
        background-color: #f8f9fa !important;
        transform: translateX(2px);
    }
    .message-content {
        white-space: pre-wrap;
        line-height: 1.6;
    }
    </style>
</div>
