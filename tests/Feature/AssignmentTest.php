<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Course;
use App\Models\Assignment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AssignmentTest extends TestCase
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
        $this->assignment = Assignment::factory()->create(['course_id' => $this->course->id]);
    }

    /** @test */
    public function dosen_can_create_assignment()
    {
        $data = [
            'course_id' => $this->course->id,
            'title' => 'Tugas 1 - Pemrograman Web',
            'description' => 'Buat project CRUD Laravel',
            'deadline' => now()->addDays(7),
        ];

        $response = $this->actingAs($this->dosen)->postJson("/api/assignments", $data);

        $response->assertStatus(201)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('assignments', [
            'title' => 'Tugas 1 - Pemrograman Web'
        ]);
    }

    /** @test */
    public function mahasiswa_cannot_create_assignment()
    {
        $response = $this->actingAs($this->mahasiswa)->postJson("/api/assignments", [
            'title' => 'Coba Tambah',
            'course_id' => $this->course->id,
            'description' => 'Coba Buat project CRUD Laravel',
            'deadline' => now()->addDays(7),
        ]);

        $response->assertStatus(403);
    }

    /** @test */
    public function mahasiswa_can_submit_assignment()
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->create('tugas1.pdf', 200, 'application/pdf');

        $response = $this->actingAs($this->mahasiswa)->postJson('/api/submissions', [
            'assignment_id' => $this->assignment->id,
            'file' => $file,
        ]);

        $response->assertStatus(201)
            ->assertJson(['success' => true]);

        Storage::disk('public')->assertExists("submissions/{$file->hashName()}");

        $this->assertDatabaseHas('submissions', [
            'assignment_id' => $this->assignment->id,
            'student_id' => $this->mahasiswa->id,
        ]);
    }
}
