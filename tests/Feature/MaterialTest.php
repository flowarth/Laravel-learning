<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Course;
use App\Models\Material;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MaterialTest extends TestCase
{
    use RefreshDatabase;

    protected $dosen;
    protected $mahasiswa;
    protected $course;

    protected function setUp(): void
    {
        parent::setUp();

        $this->dosen = User::factory()->create(['role' => 'dosen']);
        $this->mahasiswa = User::factory()->create(['role' => 'mahasiswa']);

        $this->course = Course::factory()->create(['lecturer_id' => $this->dosen->id]);
    }

    /** @test */
    public function dosen_can_upload_material()
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->create('materi1.pdf', 200, 'application/pdf');

        $data = [
            'course_id' => $this->course->id,
            'title' => 'Materi 1 - Laravel',
            'file' => $file,
        ];

        $response = $this->actingAs($this->dosen)->postJson('/api/materials', $data);

        $response->assertStatus(201)
            ->assertJson(['success' => true]);

        Storage::disk('public')->assertExists("materials/{$file->hashName()}");

        $this->assertDatabaseHas('materials', [
            'title' => 'Materi 1 - Laravel',
            'course_id' => $this->course->id,
        ]);
    }

    /** @test */
    public function mahasiswa_can_download_material()
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->create('materi2.pdf', 200, 'application/pdf');
        $path = $file->store('materials', 'public');

        $material = Material::factory()->create([
            'course_id' => $this->course->id,
            'title' => 'Materi 2 - Vue.js',
            'file_path' => $path,
        ]);

        $response = $this->actingAs($this->mahasiswa)
            ->get("/api/materials/{$material->id}/download");

        $response->assertOk();
        $response->assertHeader('Content-Disposition');
    }

    /** @test */
    public function dosen_can_delete_material()
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->create('materi3.pdf', 200, 'application/pdf');
        $path = $file->store('materials', 'public');

        $material = Material::factory()->create([
            'course_id' => $this->course->id,
            'title' => 'Materi 3 - React.js',
            'file_path' => $path,
        ]);

        $response = $this->actingAs($this->dosen)
            ->deleteJson("/api/materials/{$material->id}");

        $response->assertOk()
            ->assertJson(['success' => true]);

        $this->assertSoftDeleted('materials', [
            'id' => $material->id]
        );
    }
}
