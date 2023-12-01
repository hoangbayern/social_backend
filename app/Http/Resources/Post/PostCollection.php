<?php

namespace App\Http\Resources\Post;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use function Symfony\Component\Translation\t;

class PostCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection,
            'links' => [
                'previousPageUrl' => $this->previousPageUrl(),
                'currentPage' => $this->currentPage(),
                'currentPageUrl' => $this->url($this->currentPage()),
                'nextPageUrl' => $this->nextPageUrl(),
                'totalPage' => $this->lastPage(),
                'totalRecord' => $this->total(),
            ]

        ];
    }
}
