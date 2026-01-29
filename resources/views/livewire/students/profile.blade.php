<div>
    <!-- Header -->
    <div class="bg-primary text-white rounded-4 p-4 mb-4">
        <h4 class="mb-1">Student Profile</h4>
        <small>View your personal and academic information</small>
    </div>

    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Personal Information -->
    <div class="card shadow-sm border-0 rounded-4 p-4 mb-4">
        <h5 class="fw-semibold mb-3">Personal Information</h5>

        <div class="row g-4 align-items-center">
            <div class="col-md-3 text-center">
                @php
                    $photoPath = $student->profile_photo_path ?? null;
                @endphp

                <!-- Hidden File Input -->
                <input type="file" id="photoInput" class="d-none" wire:model="photo" accept="image/*">

                <!-- Clickable Profile Image -->
                <label for="photoInput" style="cursor: pointer;">
                    <img src="{{ $photo
                        ? $photo->temporaryUrl()
                        : ($photoPath
                            ? asset('storage/' . $photoPath)
                            : asset('images/profile-placeholder.jpg')) }}"
                        class="rounded-circle img-fluid mb-2 border" alt="Student Photo"
                        style="width:120px;height:120px;object-fit:cover;" title="Click to change photo">
                </label>

                <p class="fw-semibold mb-0">
                    @if (!empty($student))
                        {{ $student->first_name }} {{ $student->last_name }}
                    @else
                        {{ Auth::user()->name ?? 'Student Name' }}
                    @endif
                </p>

                <small class="text-muted d-block mb-2">
                    Roll No: {{ $student->roll_no ?? '—' }}
                </small>

                @error('photo')
                    <div class="text-danger small">{{ $message }}</div>
                @enderror

                <!-- Action Buttons -->
                <div class="d-flex justify-content-center gap-2 mt-2">
                    <!-- Save / Change icon button -->
                    {{-- <button type="button"
                            class="btn btn-sm btn-outline-primary"
                            wire:click.prevent="updatePhoto"
                            @if (!$photo) disabled @endif
                            title="Save new profile picture">
                        <i class="bi bi-check-circle"></i>
                    </button> --}}

                    <!-- Delete icon button -->
                    @if ($student->profile_photo_path)
                        <button type="button"
                                class="btn btn-sm btn-outline-danger"
                                wire:click.prevent="deletePhoto"
                                onclick="confirm('Delete profile picture?') || event.stopImmediatePropagation()"
                                title="Delete profile picture">
                            <i class="bi bi-trash"></i>
                        </button>
                    @endif
                </div>
            </div>


            <div class="col-md-9">
                <div class="row g-3">
                    <div class="col-md-4">
                        <small class="text-muted">Class & Stream</small>
                        <p class="fw-semibold mb-0">
                            @if (!empty($student))
                                {{ $student->class_name ?? '-' }}
                                @if (!empty($student->stream_name))
                                    • {{ $student->stream_name }}
                                @endif
                            @else
                                X - A
                            @endif
                        </p>
                    </div>
                    <div class="col-md-4">
                        <small class="text-muted">Date of Birth</small>
                        <p class="fw-semibold mb-0">
                            @if (!empty($student) && $student->dob)
                                {{ \Carbon\Carbon::parse($student->dob)->format('d M Y') }}
                            @else
                                12 Jan 2009
                            @endif
                        </p>
                    </div>
                    <div class="col-md-4">
                        <small class="text-muted">Gender</small>
                        <p class="fw-semibold mb-0">{{ $student->gender ?? '—' }}</p>
                    </div>

                    <div class="col-md-12">
                        <small class="text-muted">Address</small>
                        <p class="fw-semibold mb-0">
                            {{ $student->address ?? ($guardian->address ?? 'Kolkata, West Bengal, India') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Guardian Information -->
    <div class="card shadow-sm border-0 rounded-4 p-4 mb-4">
        <h5 class="fw-semibold mb-3">Guardian Information</h5>

        <div class="row g-3">
            <div class="col-md-4">
                <small class="text-muted">Guardian Name</small>
                <p class="fw-semibold mb-0">{{ $guardian->name ?? '—' }}</p>
            </div>
            <div class="col-md-4">
                <small class="text-muted">Contact Number</small>
                <p class="fw-semibold mb-0">{{ $guardian->phone ?? '—' }}</p>
            </div>
            <div class="col-md-4">
                <small class="text-muted">Email</small>
                <p class="fw-semibold mb-0">{{ $guardian->email ?? '—' }}</p>
            </div>
        </div>
    </div>

    <!-- Previous Academic Records -->
    <div class="card shadow-sm border-0 rounded-4 p-4">
        <h5 class="fw-semibold mb-3">Previous Academic Records</h5>

        <div class="table-responsive">
            <table class="table table-sm align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Board</th>
                        <th>School Name</th>
                        <th>Year</th>
                        <th>Total Marks</th>
                        <th>Percentage</th>
                        <th>Rank</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($previousEducations as $edu)
                        <tr>
                            <td>{{ $edu->board }}</td>
                            <td>{{ $edu->school_name }}</td>
                            <td>{{ $edu->passing_year }}</td>
                            <td>{{ $edu->total_marks }}</td>
                            <td>{{ $edu->percentage }}%</td>
                            <td>{{ $edu->rank ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted">No previous academic records found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
