<?php

namespace Tests\Feature;

use App\Models\Discussion;
use App\Models\Reply;
use App\Models\User;
use App\Models\Course;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DiscussionTest extends TestCase
{
    use RefreshDatabase;

    protected $dosen;
    protected $mahasiswa;
    protected $course;
    protected $discussion;

    protected function setUp(): void
    {
        parent::setUp();

        $this->dosen = User::factory()->create(['role' => 'dosen']);
        $this->mahasiswa = User::factory()->create(['role' => 'mahasiswa']);
        $this->course = Course::factory()->create(['lecturer_id' => $this->dosen->id]);
        $this->discussion = Discussion::factory()->create([
            'course_id' => $this->course->id,
            'user_id' => $this->mahasiswa->id,
            'content' => 'Pak, saya bingung di bagian controller',
        ]);
    }

    /** @test */
    public function mahasiswa_can_create_forum_post()
    {
        $data = [
            'course_id' => $this->course->id,
            'user_id' => $this->mahasiswa->id,
            'content' => 'Pak, saya bingung di bagian route resource.',
        ];

        $response = $this->actingAs($this->mahasiswa)->postJson("/api/discussions", $data);

        $response->assertStatus(201)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('discussions', [
            'course_id' => $this->course->id,
            'user_id' => $this->mahasiswa->id,
            'content' => 'Pak, saya bingung di bagian route resource.',
        ]);
    }

    /** @test */
    public function dosen_can_reply_forum_post()
    {
        $data = [
            'discussion_id' => $this->discussion->id,
            'user_id' => $this->dosen->id,
            'content' => 'Coba baca dokumentasi Laravel Routing.',
        ];

        $response = $this->actingAs($this->dosen)->postJson("/api/discussions/{$this->discussion->id}/replies", $data);

        $response->assertStatus(201)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('replies', [
            'discussion_id' => $this->discussion->id,
            'user_id' => $this->dosen->id,
            'content' => 'Coba baca dokumentasi Laravel Routing.',
        ]);
    }
}
