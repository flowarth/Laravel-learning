<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\MaterialRequest;
use App\Models\Course;
use App\Models\Materials;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MaterialController extends Controller
{
    public function index($courseId)
    {
        $materials = Materials::with('course')->where('course_id', $courseId)
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $materials
        ]);
    }

    public function store(MaterialRequest $request, ?Course $course = null)
    {
        if ($course->lecturer_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses untuk menambahkan materi di mata kuliah ini'
            ], 403);
        }

        $file = $request->file('file');
        $path = $file->store('materials', 'public');

        $material = Materials::create([
            'course_id' => $request['course_id'],
            'title' => $request['title'],
            'file_path' => $path,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Materi berhasil diupload',
            'data' => $material
        ], 201);
    }

    public function download($id)
    {
        $material = Materials::findOrFail($id);

        if (!Storage::disk('public')->exists($material->file_path)) {
            return response()->json([
                'success' => false,
                'message' => 'File tidak ditemukan'
            ], 404);
        }

        return Storage::disk('public')->download($material->file_path);
    }

    public function destroy(Request $request, $id)
    {
        $material = Materials::findOrFail($id);
        $course = $material->course;

        if ($course->lecturer_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses untuk menghapus materi ini'
            ], 403);
        }

        if (Storage::disk('public')->exists($material->file_path)) {
            Storage::disk('public')->delete($material->file_path);
        }

        $material->delete();

        return response()->json([
            'success' => true,
            'message' => 'Materi berhasil dihapus'
        ]);
    }
}
