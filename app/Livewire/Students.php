<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Student;
use App\Models\Admission;

class Students extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // Search & Filters
    public $search = '';
    public $class = '';
    public $faculty = '';
    public $year = '';

    // Reset page when filters change
    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingClass()
    {
        $this->resetPage();
    }

    public function updatingFaculty()
    {
        $this->resetPage();
    }

    public function updatingYear()
    {
        $this->resetPage();
    }

    // Delete (Soft)
    public function deleteStudent($id)
    {
        Student::where('id', $id)->update([
            'status' => 'inactive'
        ]);

        session()->flash('success', 'Student marked as inactive');
    }

    public function render()
    {
        $students = Student::query()

            ->with('admission.faculty')

            ->when($this->search, function ($q) {
                $q->where('name', 'like', '%'.$this->search.'%')
                  ->orWhere('student_code', 'like', '%'.$this->search.'%')
                  ->orWhere('phone', 'like', '%'.$this->search.'%');
            })

            ->when($this->class, function ($q) {
                $q->whereHas('admission', function ($q2) {
                    $q2->where('class', $this->class);
                });
            })

            ->when($this->faculty, function ($q) {
                $q->whereHas('admission.faculty', function ($q2) {
                    $q2->where('id', $this->faculty);
                });
            })

            ->when($this->year, function ($q) {
                $q->whereHas('admission', function ($q2) {
                    $q2->where('academic_year', $this->year);
                });
            })

            ->where('status', 'active')

            ->latest()
            ->paginate(10);

        return view('livewire.students', [
            'students' => $students
        ]);
    }
}
