<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CourseRequest;
use App\Models\Course;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', Course::class);

        $courses = Course::with(['lecturer', 'students'])->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $courses
        ]);
    }

    public function store(CourseRequest $request)
    {
        $this->authorize('create', Course::class);

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

    public function update(CourseRequest $request, $id)
    {
        $course = Course::findOrFail($id);

        $this->authorize('update', $course);

        $course->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Mata kuliah berhasil diupdate',
            'data' => $course->load('lecturer')
        ]);
    }

    public function destroy($id)
    {
        $course = Course::findOrFail($id);

        $this->authorize('delete', $course);

        $course->delete();

        return response()->json([
            'success' => true,
            'message' => 'Mata kuliah berhasil dihapus'
        ]);
    }

    public function enroll(Request $request, $id)
    {
        $user = $request->user();

        $course = Course::findOrFail($id);

        $this->authorize('enroll', $course);

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
