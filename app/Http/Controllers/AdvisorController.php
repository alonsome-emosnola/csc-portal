<?php

namespace App\Http\Controllers;

use App\Models\AcademicSession;
use App\Models\User;
use App\Models\Advisor;
use App\Models\AcademicSet;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;

class AdvisorController extends Controller
{

    public function __construct()
    {
        //$this->middleware('role:staff');

    }



    public function index_classes(Request $request) {
        $advisor = $request->user()->advisor;
        $active_class = $advisor->class;
       

        $invitationLink = invitation_url($active_class->token);
        $classes = $advisor->classes()->paginate(10, ['*'])->map(function($item) {
            $item->active = $item->is_active();
            $item->students = $item->students->map(fn($student) => $student->user->account());
            return $item;
        });

        $active_class->students = $active_class->students->map(fn($student) => $student->user->account());

        return compact('classes', 'active_class', 'invitationLink');
    }

    public function show_students_course_reg()
    {
        $auth = auth()->user();
        $student = $auth->staff->students()->get();
        $students_ids =  $student->pluck('reg_no');
        $enrollments = Enrollment::whereIn('reg_no', $students_ids)
            ->with(['course', 'student.user'])
            ->groupBy('reg_no')->get();


        return view('pages.advisor.student-management.students-course-registrations', compact('enrollments'));
    }


    public function studentEnrollments(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'student_id' => 'required|exists:students,id',
            'semester' => 'required',
            'level' => 'required',
            'session' => 'required',
        ]);

        $semester = $request->semester;
        $level = $request->level;
        $session = $request->session;
        $student = Student::where('id', $request->student_id)->with('user')->first();


        $enrollments = Course::getEnrollments($semester, $session, $student->reg_no);

        if (!count($enrollments)) {
            return response()->json([
                'error' => 'Enrollment history doesnt exist'
            ], 400);
        }
        $total = $enrollments->sum('course.units');

        $level = $enrollments->first()->level;

        return response()->json(compact('semester', 'level', 'student', 'session', 'enrollments', 'total'));
    }


    public function show_class()
    {
        $current_session = AcademicSession::currentSession();
        return view('pages.advisor.class-management.class', compact('current_session'));
    }


    public function generate_transcript(Request $request)
    {
        
        $validator = Validator::make($request->all(), [
            'reg_no' => 'required|exists:results,reg_no'
        ], [
            'reg_no.required' => "Student's Registration Number must be provided",
            'reg_no.exists' => "Student's results not found",
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 400);
        }
        $reg_no = $request->reg_no;

        $results = \App\Models\Result::where('reg_no', $reg_no)
            ->where('status', 'APPROVED')
            ->with('course')
            ->get()
            ->groupBy(['session', 'semester']);

        $results = $results->map(function ($sessions) {
            return $sessions->map(function ($semesterResults) {
                $totalUnits = $semesterResults->sum('course.units');
                $totalGradePoints = $semesterResults->sum('grade_points'); 
                return [
                    'results' => $semesterResults,
                    'totalUnits' => $totalUnits,
                    'totalGradePoints' => $totalGradePoints
                ];
            })->put('sessionTotals', [
                'totalUnits' => $sessions->flatten(1)->sum('course.units'),
                'totalGradePoints' => $sessions->flatten(1)->sum('grade_points')
            ]);
        });
        $student = \App\Models\Student::where('reg_no', $reg_no)->with('user')->first();

        if (!$student) {
            return response()->json([
                'error' => "Student account doesn't exist"
            ], 400);
        }

        return compact('student', 'results');
    }





    public function getCourses(Request $request)
    {
        $myAccount = $request->user()->staff;
        $myAccount->user->name = $request->user()->name;
        $myCourses = $myAccount->courses()
            ->with('course')
            ->latest();
        $courses = $myCourses
            ->paginate(5,  ['*'], 'more_courses')
            ->map(function ($course) {
                $technologist = $course->technologists();
                $course->technologist = null;
                if (count($technologist)) {
                    $course->technologists = $technologist;
                }
                return $course;
            });

        return [
            'myAccount'=>$myAccount,
            'courses' => $courses,
            'count_courses' => $myCourses->count()
        ];
    }

    public function getStudents(Request $request)
    {

        $auth = $request->user();
        $myAccount = $auth->staff;
        $students = $myAccount->students()->orderBy('cgpa', 'desc')->with('user');

        $students = $students->paginate(5, ['*'], 'more_students')->map(fn($student)=>$student->user->account());
        $count_students = $students->count();

        return compact('students', 'count_students');
    }



    public function dashboard_api_data(Request $request)
    {
        $auth = $request->user();
        $courses = $this->getCourses($request);
        $students = $this->getStudents($request);
        

        return array_merge($courses, $students);
    }
}
