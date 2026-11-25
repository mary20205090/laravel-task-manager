<?php

namespace App\Http\Requests;

class StoreTaskRequest extends SaveTaskRequest
{
    public function rules(): array
    {
        return [
            ...parent::rules(),
            'project_id' => ['required', 'integer', 'exists:projects,id'],
        ];
    }
}
