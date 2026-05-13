<?php

namespace App\Http\Responses;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Symfony\Component\HttpFoundation\Response;

class ApiResponse
{
    private array|object $data = [];

    private array $paginate = [];

    private string $message = '';

    private int $status = 200;

    private const HTTP_STATUS = [
        200 => 'success',
        201 => 'created',
        204 => 'no_content',
        400 => 'bad_request',
        401 => 'unauthorized',
        403 => 'forbidden',
        404 => 'not_found',
        409 => 'conflict',
        422 => 'unprocessable_entity',
        500 => 'unexpected_error',
    ];

    public function data(mixed $data): self
    {
        if ($data instanceof LengthAwarePaginator) { // Melhorar a verificação de tipo
            $this->paginate = $this->formatPagination($data);
            $this->data = $data->items();
        } else {
            $this->data = $data;
        }

        return $this;
    }

    public function parent(string $parent): self
    {
        if (empty(trim($parent))) {
            return $this;
        }

        $envelopedData = [];
        Arr::set($envelopedData, trim($parent), $this->data);

        $this->data = $envelopedData;

        return $this;
    }

    public function message(string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function validator(Validator $validator): self
    {
        $this->message = $validator->errors()->first();
        $this->status = 422;

        return $this;
    }

    public function status(int $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function json(): JsonResponse
    {
        return response()->json($this->buildResponse(), $this->status);
    }

    private function buildResponse(): array
    {
        $response = [
            'status' => $this->status,
            'message' => $this->message ?: (__('messages.'.self::HTTP_STATUS[$this->status]) ?? 'Unknown Status'),
            'data' => $this->data,
        ];

        if ($this->paginate) {
            $response['paginate'] = $this->paginate;
        }

        return $response;
    }

    public function stream(string $content, array $headers = []): Response
    {
        return response()->stream(function () use ($content) {
            echo $content;
        }, $this->status, $headers);
    }

    private function formatPagination(LengthAwarePaginator $data, int $perRange = 5): array
    {
        if ($data->isEmpty()) {
            return [
                'currentPage' => $data->currentPage(),
                'lastPage' => $data->lastPage(),
                'previous' => false,
                'next' => false,
                'range' => [],
                'total' => 0,
                'perPage' => $data->perPage(),
            ];
        }

        $range = [];
        $lastPage = $data->lastPage();
        $currentPage = $data->currentPage();
        $leftRange = max(1, $currentPage - $perRange);
        $rigthRange = min($lastPage, $currentPage + $perRange);

        for ($i = $leftRange; $i <= $rigthRange; $i++) {
            $range[] = [
                'page' => $i,
                'active' => ($currentPage === $i),
            ];
        }

        return [
            'currentPage' => $currentPage,
            'lastPage' => $lastPage,
            'previous' => ! $data->onFirstPage(),
            'next' => $data->hasMorePages(),
            'range' => $range,
            'total' => $data->total(),
            'perPage' => $data->perPage(),
        ];
    }
}
