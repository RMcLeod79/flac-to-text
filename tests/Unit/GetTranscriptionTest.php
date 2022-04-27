<?php

namespace Tests\Unit;

use App\Models\Transcription;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class GetTranscriptionTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function an_error_is_returned_if_the_transcription_can_not_be_found()
    {
        $transcription = Transcription::factory()->count(1)->create();
        $response = $this->get('/api/transcription/2');
        $this->assertEquals(404, $response->status());
    }

    /** @test */
    public function json_is_returned_on_success()
    {
        $transcription = Transcription::factory()->count(1)->create();
        $response = $this->get('/api/transcription/1');
        $response->assertStatus(200)
            ->assertJson();
    }
}
