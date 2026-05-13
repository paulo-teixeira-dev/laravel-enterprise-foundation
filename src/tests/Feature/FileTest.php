<?php

namespace Tests\Feature;

use App\Services\FileService;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Tests\TestCase;

class FileTest extends TestCase
{
    public function test_index_returns_paginated_files(): void
    {
        $this->withoutMiddleware();

        $paginator = new LengthAwarePaginator([
            ['id' => 1, 'original_name' => 'document.pdf'],
        ], 1, 15);

        $this->mock(FileService::class)
            ->shouldReceive('paginate')
            ->once()
            ->with(['search' => 'document'])
            ->andReturn($paginator);

        $this->getJson('/api/v1/files?search=document')
            ->assertOk()
            ->assertJsonPath('status', 200)
            ->assertJsonPath('paginate.total', 1);
    }

    public function test_store_calls_file_service(): void
    {
        $this->withoutMiddleware();

        $file = UploadedFile::fake()->create('document.pdf', 100, 'application/pdf');

        $this->mock(FileService::class)
            ->shouldReceive('create')
            ->once()
            ->withArgs(fn ($uploadedFile) => $uploadedFile instanceof UploadedFile);

        $this->postJson('/api/v1/files', ['file' => $file])
            ->assertOk()
            ->assertJsonPath('status', 200);
    }

    public function test_store_validates_required_file(): void
    {
        $this->withoutMiddleware();

        $this->mock(FileService::class)
            ->shouldNotReceive('create');

        $this->postJson('/api/v1/files', [])
            ->assertStatus(422)
            ->assertJsonPath('status', 422);
    }

    public function test_show_streams_file_content(): void
    {
        $this->withoutMiddleware();

        $this->mock(FileService::class)
            ->shouldReceive('getById')
            ->once()
            ->with(1)
            ->andReturn([
                'content' => 'file-content',
                'headers' => ['Content-Type' => 'application/pdf'],
            ]);

        $this->get('/api/v1/files/1')
            ->assertOk()
            ->assertHeader('Content-Type', 'application/pdf')
            ->assertStreamedContent('file-content');
    }

    public function test_download_returns_service_response(): void
    {
        $this->withoutMiddleware();

        $response = new StreamedResponse(function (): void {
            echo 'download-content';
        }, 200, ['Content-Type' => 'application/pdf']);

        $this->mock(FileService::class)
            ->shouldReceive('download')
            ->once()
            ->with(1)
            ->andReturn($response);

        $this->get('/api/v1/files/1/download')
            ->assertOk()
            ->assertStreamedContent('download-content');
    }

    public function test_destroy_calls_file_service(): void
    {
        $this->withoutMiddleware();

        $this->mock(FileService::class)
            ->shouldReceive('delete')
            ->once()
            ->with(1);

        $this->deleteJson('/api/v1/files/1')
            ->assertOk()
            ->assertJsonPath('status', 200);
    }
}
