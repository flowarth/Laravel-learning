<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Course;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CourseTest extends TestCase
{
    use RefreshDatabase;

    protected $dosen;
    protected $mahasiswa;

    protected function setUp(): void
    {
        parent::setUp();

        $this->dosen = User::factory()->create([
            'role' => 'dosen',
        ]);

        $this->mahasiswa = User::factory()->create([
            'role' => 'mahasiswa',
        ]);
    }

    /** @test */
    public function dosen_can_create_course()
    {
        $response = $this->actingAs($this->dosen)->postJson('/api/courses', [
            'name' => 'Pemrograman Web',
            'description' => 'Belajar Laravel dan Vue',
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Mata kuliah berhasil ditambahkan',
            ]);

        $this->assertDatabaseHas('courses', [
            'name' => 'Pemrograman Web',
            'lecturer_id' => $this->dosen->id,
        ]);
    }

    /** @test */
    public function mahasiswa_cannot_create_course()
    {
        $response = $this->actingAs($this->mahasiswa)->postJson('/api/courses', [
            'name' => 'Hacking 101',
        ]);

        $response->assertStatus(403);
    }

    /** @test */
    public function dosen_can_update_own_course()
    {
        $course = Course::factory()->create([
            'lecturer_id' => $this->dosen->id,
            'name' => 'Dasar Laravel',
        ]);

        $response = $this->actingAs($this->dosen)->putJson("/api/courses/{$course->id}", [
            'name' => 'Dasar Laravel Updated',
            'description' => 'Perubahan deskripsi',
        ]);

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'message' => 'Mata kuliah berhasil diupdate',
            ]);

        $this->assertDatabaseHas('courses', [
            'id' => $course->id,
            'name' => 'Dasar Laravel Updated',
        ]);
    }

    /** @test */
    public function dosen_cannot_update_other_dosen_course()
    {
        $otherDosen = User::factory()->create(['role' => 'dosen']);

        $course = Course::factory()->create([
            'lecturer_id' => $otherDosen->id,
        ]);

        $response = $this->actingAs($this->dosen)->putJson("/api/courses/{$course->id}", [
            'name' => 'Coba Edit',
        ]);

        $response->assertStatus(403);
    }

    /** @test */
    public function mahasiswa_can_enroll_course()
    {
        $course = Course::factory()->create(['lecturer_id' => $this->dosen->id]);

        $response = $this->actingAs($this->mahasiswa)->postJson("/api/courses/{$course->id}/enroll");

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'message' => 'Berhasil mendaftar mata kuliah',
            ]);

        $this->assertDatabaseHas('course_student', [
            'student_id' => $this->mahasiswa->id,
            'course_id' => $course->id,
        ]);
    }

    /** @test */
    public function dosen_cannot_enroll_course()
    {
        $course = Course::factory()->create(['lecturer_id' => $this->dosen->id]);

        $response = $this->actingAs($this->dosen)->postJson("/api/courses/{$course->id}/enroll");

        $response->assertStatus(403);
    }
}
