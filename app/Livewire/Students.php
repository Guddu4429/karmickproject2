<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Student;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class Students extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // Search & Filters
    public $search = '';
    public $class = '';
    public $stream = '';
    public $year = '';

    // Add Student Modal
    public $showAddModal = false;
    public $first_name = '';
    public $last_name = '';
    public $admission_no = '';
    public $roll_no = '';
    public $dob = '';
    public $gender = '';
    public $address = '';
    public $guardian_id = '';
    public $class_id = '';
    public $stream_id = '';
    public $selectedStreamId = '';
    public $admission_date = '';

    // Reset page when filters change
    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingClass()
    {
        $this->resetPage();
    }

    public function updatingStream()
    {
        $this->resetPage();
    }

    public function updatingYear()
    {
        $this->resetPage();
    }

    // Update stream_id when selectedStreamId changes
    public function updatedSelectedStreamId()
    {
        $this->stream_id = $this->selectedStreamId;
    }

    public function closeAddModal()
    {
        $this->showAddModal = false;
        $this->resetAddForm();
    }

    public function resetAddForm()
    {
        $this->first_name = '';
        $this->last_name = '';
        $this->admission_no = '';
        $this->roll_no = '';
        $this->dob = '';
        $this->gender = '';
        $this->address = '';
        $this->guardian_id = '';
        $this->class_id = '';
        $this->stream_id = '';
        $this->selectedStreamId = '';
        $this->admission_date = date('Y-m-d');
    }

    // Generate Admission Number
    public function generateAdmissionNo()
    {
        $lastStudent = Student::orderBy('id', 'desc')->first();
        
        if ($lastStudent && $lastStudent->admission_no) {
            preg_match('/\d+/', $lastStudent->admission_no, $matches);
            $lastNumber = $matches ? (int)$matches[0] : 0;
            $newNumber = $lastNumber + 1;
            $this->admission_no = 'ADM' . date('Y') . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
        } else {
            $this->admission_no = 'ADM' . date('Y') . '001';
        }
    }

    // Open Add Student Modal
    public function openAddModal()
    {
        $this->resetAddForm();
        $this->generateAdmissionNo();
        // Set default admission date to today
        if (empty($this->admission_date)) {
            $this->admission_date = date('Y-m-d');
        }
        $this->showAddModal = true;
    }

    // Add Student
    public function addStudent()
    {
        // Set stream_id from selectedStreamId if not already set
        if (empty($this->stream_id) && !empty($this->selectedStreamId)) {
            $this->stream_id = $this->selectedStreamId;
        }

        $this->validate([
            'admission_no' => 'required|string|unique:students,admission_no',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'roll_no' => 'nullable|string|max:255',
            'dob' => 'required|date',
            'gender' => 'required|in:Male,Female,Other',
            'address' => 'nullable|string',
            'guardian_id' => 'required|exists:guardians,id',
            'class_id' => 'required|exists:classes,id',
            'stream_id' => 'required|exists:streams,id',
            'admission_date' => 'nullable|date',
        ]);

        try {
            DB::beginTransaction();

            Student::create([
                'guardian_id' => $this->guardian_id,
                'admission_no' => $this->admission_no,
                'roll_no' => $this->roll_no,
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
                'dob' => $this->dob,
                'gender' => $this->gender,
                'address' => $this->address,
                'class_id' => $this->class_id,
                'stream_id' => $this->stream_id,
                'admission_date' => $this->admission_date ?: date('Y-m-d'),
            ]);

            DB::commit();

            session()->flash('success', 'Student added successfully!');
            $this->closeAddModal();
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error adding student: ' . $e->getMessage());
        }
    }

    // Delete
    public function deleteStudent($id)
    {
        Student::where('id', $id)->delete();

        session()->flash('success', 'Student deleted successfully');
    }

    public function render()
    {
        // Get statistics
        $totalStudents = Student::count();
        
        $studentsPerStream = DB::table('students')
            ->join('streams', 'students.stream_id', '=', 'streams.id')
            ->select('streams.name as stream_name', DB::raw('count(*) as count'))
            ->groupBy('streams.id', 'streams.name')
            ->get();

        $studentsPerClass = DB::table('students')
            ->join('classes', 'students.class_id', '=', 'classes.id')
            ->select('classes.name as class_name', DB::raw('count(*) as count'))
            ->groupBy('classes.id', 'classes.name')
            ->get();

        // Get students with relationships
        $students = Student::query()
            ->when($this->search, function ($q) {
                $searchTerm = '%'.$this->search.'%';
                $guardianIds = DB::table('guardians')
                    ->where('phone', 'like', $searchTerm)
                    ->pluck('id')
                    ->toArray();
                
                $q->where(function ($query) use ($searchTerm, $guardianIds) {
                    $query->where('first_name', 'like', $searchTerm)
                          ->orWhere('last_name', 'like', $searchTerm)
                          ->orWhere('admission_no', 'like', $searchTerm)
                          ->orWhere('roll_no', 'like', $searchTerm);
                    
                    if (!empty($guardianIds)) {
                        $query->orWhereIn('guardian_id', $guardianIds);
                    }
                });
            })

            ->when($this->class, function ($q) {
                $q->where('class_id', $this->class);
            })

            ->when($this->stream, function ($q) {
                $q->where('stream_id', $this->stream);
            })

            ->when($this->year, function ($q) {
                $q->whereYear('created_at', $this->year);
            })

            ->latest()
            ->paginate(10);

        // Get data for filters and forms
        $classes = DB::table('classes')->orderBy('name')->get();
        $streams = DB::table('streams')->orderBy('name')->get();
        $guardians = DB::table('guardians')->orderBy('name')->get();

        // Get subjects filtered by stream_id if stream is selected
        $subjects = collect([]);
        if (!empty($this->selectedStreamId)) {
            $subjects = DB::table('subjects')
                ->where('stream_id', (int)$this->selectedStreamId)
                ->orderBy('name')
                ->get();
        }

        return view('livewire.students', [
            'students' => $students,
            'totalStudents' => $totalStudents,
            'studentsPerStream' => $studentsPerStream,
            'studentsPerClass' => $studentsPerClass,
            'classes' => $classes,
            'streams' => $streams,
            'guardians' => $guardians,
            'subjects' => $subjects,
        ])->layout('layouts.student');
    }
}
