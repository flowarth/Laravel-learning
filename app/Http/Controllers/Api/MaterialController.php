<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\MaterialRequest;
use App\Models\Course;
use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MaterialController extends Controller
{
    public function store(MaterialRequest $request)
    {
        $this->authorize('create', Material::class);

        $file = $request->file('file');
        $path = $file->store('materials', 'public');

        $material = Material::create([
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
        $material = Material::findOrFail($id);

        if (!Storage::disk('public')->exists($material->file_path)) {
            return response()->json([
                'success' => false,
                'message' => 'File tidak ditemukan'
            ], 404);
        }

        return Storage::disk('public')->download($material->file_path);
    }

    public function destroy($id)
    {
        $material = Material::findOrFail($id);

        $this->authorize('delete', $material);

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
