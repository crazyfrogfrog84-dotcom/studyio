<?php

namespace App\Http\Requests;

use App\Models\Portfolio;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class PortfolioRequest extends FormRequest
{
    public function authorize(): bool
    {
        if ($this->isMethod('POST')) {
            return $this->user()?->can('create', Portfolio::class) ?? false;
        }

        return $this->user()?->can('update', $this->route('portfolio')) ?? false;
    }

    public function rules(): array
    {
        return [
            'full_name' => ['required', 'string', 'max:160'],
            'profile_picture' => [
                $this->route('portfolio')?->profile_picture_path ? 'nullable' : 'required',
                'image',
                'max:5120',
            ],
            'email' => ['required', 'email', 'max:255'],
            'contact_number' => ['required', 'string', 'regex:/^\\+?[0-9]+$/', 'max:60'],
            'address' => ['required', 'string', 'max:1000'],
            'about_me' => ['required', 'string', 'max:5000'],
            'education' => ['nullable', 'array', 'max:20'],
            'education.*.institution' => ['nullable', 'string', 'max:180'],
            'education.*.qualification' => ['nullable', 'string', 'max:180'],
            'education.*.field_of_study' => ['nullable', 'string', 'max:180'],
            'education.*.start_date' => ['nullable', 'date'],
            'education.*.end_date' => ['nullable', 'date', 'after_or_equal:education.*.start_date'],
            'education.*.description' => ['nullable', 'string', 'max:2000'],
            'skills' => ['nullable', 'array', 'max:40'],
            'skills.*.name' => ['nullable', 'string', 'max:120'],
            'skills.*.proficiency' => ['nullable', 'string', 'max:60'],
            'projects' => ['nullable', 'array', 'max:20'],
            'projects.*.title' => ['nullable', 'string', 'max:180'],
            'projects.*.id' => ['nullable', 'integer'],
            'projects.*.description' => ['nullable', 'string', 'max:3000'],
            'projects.*.project_url' => ['nullable', 'url', 'max:2048'],
            'projects.*.tags' => ['nullable', 'string', 'max:500'],
            'projects.*.screenshot' => ['nullable', 'image', 'max:5120'],
            'work_experience' => ['nullable', 'array', 'max:20'],
            'work_experience.*.company' => ['nullable', 'string', 'max:180'],
            'work_experience.*.role' => ['nullable', 'string', 'max:180'],
            'work_experience.*.start_date' => ['nullable', 'date'],
            'work_experience.*.end_date' => ['nullable', 'date', 'after_or_equal:work_experience.*.start_date'],
            'work_experience.*.description' => ['nullable', 'string', 'max:3000'],
            'social_links' => ['nullable', 'array', 'max:20'],
            'social_links.*.platform' => ['nullable', 'string', 'max:80'],
            'social_links.*.label' => ['nullable', 'string', 'max:120'],
            'social_links.*.url' => ['nullable', 'url', 'max:2048'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            foreach ([
                'skills' => ['name'],
                'projects' => ['title'],
                'social_links' => ['url'],
            ] as $collection => $requiredFields) {
                foreach ($this->input($collection, []) as $index => $row) {
                    $hasContent = collect($row)->contains(fn ($value) => filled($value))
                        || collect($this->file($collection, [])[$index] ?? [])->isNotEmpty();
                    if ($hasContent) {
                        foreach ($requiredFields as $field) {
                            if (blank($row[$field] ?? null)) {
                                $validator->errors()->add("{$collection}.{$index}.{$field}", 'This field is required when this item has other information.');
                            }
                        }
                    }
                }
            }
        });
    }
}
