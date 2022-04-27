<?php

namespace Tests\Unit;

use Tests\TestCase;

class UploadTest extends TestCase
{
    public function __construct(?string $name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
    }

    /** @test */
    public function an_error_will_be_returned_if_no_content_is_provided()
    {
        $json = [
            'filename' => 'test',
            'content' => '',
        ];

        $response = $this->json('POST', '/api/file/upload', $json);
        $this->assertEquals(400, $response->status());
        $this->assertEquals('No content provided', $response->content());
    }

    /** @test */
    public function an_error_will_be_returned_if_the_content_is_not_valid_base64()
    {
        $json = [
            'filename' => 'test',
            'content' => 'test string',
        ];

        $response = $this->json('POST', '/api/file/upload', $json);
        $this->assertEquals(400, $response->status());
        $this->assertEquals('Improperly encoded Base64', $response->content());
    }

    /** @test */
    public function an_error_will_be_returned_if_the_content_is_not_a_flac_file()
    {
        $json = [
            'filename' => 'test',
            'content' => base64_encode('test'),
        ];

        $response = $this->json('POST', '/api/file/upload', $json);
        $this->assertEquals(400, $response->status());
        $this->assertEquals('File is not a valid flac file', $response->content());
    }
}
