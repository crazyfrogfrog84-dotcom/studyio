<?php

namespace Database\Seeders;

use App\Models\Template;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            ['slug' => 'simple', 'name' => 'Simple', 'description' => 'A clean, professional layout focused on your story and experience.', 'sort_order' => 1],
            ['slug' => 'modern', 'name' => 'Modern', 'description' => 'A card-based layout with clear sections and visual detail.', 'sort_order' => 2],
            ['slug' => 'creative', 'name' => 'Creative', 'description' => 'An expressive split layout with a bold, editorial feel.', 'sort_order' => 3],
        ];

        DB::transaction(function () use ($templates): void {
            Template::query()
                ->whereNotIn('slug', array_column($templates, 'slug'))
                ->delete();

            foreach ($templates as $template) {
                Template::updateOrCreate(['slug' => $template['slug']], $template);
            }
        });
    }
}
