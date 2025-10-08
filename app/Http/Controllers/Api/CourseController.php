<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CourseRequest;
use App\Models\Course;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->isDosen()) {
            $courses = Course::where('lecturer_id', $user->id)
                ->with(['lecturer', 'students'])
                ->paginate(10);
        } else {
            $courses = Course::with(['lecturer', 'students'])
                ->paginate(10);
        }

        return response()->json([
            'success' => true,
            'data' => $courses
        ]);
    }

    public function store(CourseRequest $request)
    {
        if (!$request->user()->isDosen()) {
            return response()->json([
                'success' => false,
                'message' => 'Hanya dosen yang bisa menambahkan mata kuliah'
            ], 403);
        }

        $course = Course::create([
            'name' => $request['name'],
            'description' => $request['description'],
            'lecturer_id' => $request->user()->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Mata kuliah berhasil ditambahkan',
            'data' => $course->load('lecturer')
        ], 201);
    }

    public function show($id)
    {
        $course = Course::with(['lecturer', 'students', 'materials', 'assignments'])
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $course
        ]);
    }

    public function update(CourseRequest $request, Course $course)
    {
        if ($course->lecturer_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses untuk mengedit mata kuliah ini'
            ], 403);
        }

        $course->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Mata kuliah berhasil diupdate',
            'data' => $course->load('lecturer')
        ]);
    }

    public function destroy(Request $request, Course $course)
    {
        if ($course->lecturer_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses untuk menghapus mata kuliah ini'
            ], 403);
        }

        $course->delete();

        return response()->json([
            'success' => true,
            'message' => 'Mata kuliah berhasil dihapus'
        ]);
    }

    public function enroll(Request $request, $id)
    {
        $user = $request->user();

        if (!$user->isMahasiswa()) {
            return response()->json([
                'success' => false,
                'message' => 'Hanya mahasiswa yang bisa mendaftar mata kuliah'
            ], 403);
        }

        $course = Course::findOrFail($id);

        if ($course->students()->where('student_id', $user->id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah terdaftar di mata kuliah ini'
            ], 400);
        }

        $course->students()->attach($user->id);

        return response()->json([
            'success' => true,
            'message' => 'Berhasil mendaftar mata kuliah',
            'data' => $course->load('lecturer')
        ]);
    }
}
