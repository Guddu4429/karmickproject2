<div>

    {{-- Page Title --}}
    <div class="d-flex justify-content-between mb-3">
        <h4>Students Management</h4>

        <a href="{{ route('students.create') }}" class="btn btn-primary">
            + Add Student
        </a>
    </div>

    {{-- Filters --}}
    <div class="row mb-3">

        {{-- Search --}}
        <div class="col-md-3">
            <input type="text"
                   class="form-control"
                   placeholder="Search name / ID / phone"
                   wire:model.debounce.500ms="search">
        </div>

        {{-- Class --}}
        <div class="col-md-2">
            <select class="form-control" wire:model="class">
                <option value="">All Classes</option>
                <option value="11">Class 11</option>
                <option value="12">Class 12</option>
            </select>
        </div>

        {{-- Faculty --}}
        <div class="col-md-3">
            <select class="form-control" wire:model="faculty">
                <option value="">All Faculties</option>
                @foreach(\App\Models\Faculty::all() as $faculty)
                    <option value="{{ $faculty->id }}">
                        {{ $faculty->name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Year --}}
        <div class="col-md-2">
            <select class="form-control" wire:model="year">
                <option value="">All Years</option>
                @for($y = date('Y'); $y >= 2020; $y--)
                    <option value="{{ $y }}">{{ $y }}</option>
                @endfor
            </select>
        </div>

    </div>


    {{-- Flash Message --}}
    @if (session()->has('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif


    {{-- Students Table --}}
    <div class="card">
        <div class="card-body p-0">

            <table class="table table-bordered mb-0">

                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Student ID</th>
                        <th>Name</th>
                        <th>Class</th>
                        <th>Faculty</th>
                        <th>Year</th>
                        <th>Phone</th>
                        <th>Status</th>
                        <th width="180">Actions</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse ($students as $key => $student)

                        <tr>
                            <td>
                                {{ $students->firstItem() + $key }}
                            </td>

                            <td>{{ $student->student_code }}</td>

                            <td>{{ $student->name }}</td>

                            <td>
                                {{ $student->admission->class ?? '-' }}
                            </td>

                            <td>
                                {{ $student->admission->faculty->name ?? '-' }}
                            </td>

                            <td>
                                {{ $student->admission->academic_year ?? '-' }}
                            </td>

                            <td>{{ $student->phone }}</td>

                            <td>
                                <span class="badge bg-success">
                                    Active
                                </span>
                            </td>

                            <td>

                                {{-- View --}}
                                <a href="{{ route('students.show',$student->id) }}"
                                   class="btn btn-sm btn-info">
                                    View
                                </a>

                                {{-- Edit --}}
                                <a href="{{ route('students.edit',$student->id) }}"
                                   class="btn btn-sm btn-warning">
                                    Edit
                                </a>

                                {{-- Delete --}}
                                <button
                                    wire:click="deleteStudent({{ $student->id }})"
                                    onclick="confirm('Are you sure?') || event.stopImmediatePropagation()"
                                    class="btn btn-sm btn-danger">

                                    Delete
                                </button>

                            </td>
                        </tr>

                    @empty

                        <tr>
                            <td colspan="9" class="text-center py-3">
                                No students found
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>
    </div>


    {{-- Pagination --}}
    <div class="mt-3">
        {{ $students->links() }}
    </div>

</div>
