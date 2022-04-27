<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transcription>
 */
class TranscriptionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'filename' => 'test.flac',
            'transcription' => 'Honi soit qui mal y pense',
            'submitted' => date('Y-m-d H:i:s'),
            'status' => 'Complete',
        ];
    }
}
