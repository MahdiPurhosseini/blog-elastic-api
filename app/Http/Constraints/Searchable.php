<?php

namespace App\Http\Constraints;

trait Searchable
{
    /**
     * Get the data that should be synchronized with Elasticsearch
     */
    public function toSearchableArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'category' => $this->category,
            'body' => $this->body,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
            // Add any other fields you want to index
        ];
    }
}
