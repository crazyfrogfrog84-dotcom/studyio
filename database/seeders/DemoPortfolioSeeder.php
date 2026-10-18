<?php

namespace Database\Seeders;

use App\Models\Template;
use App\Models\User;
use App\Services\SupabaseStorageService;
use Illuminate\Database\Seeder;
use Illuminate\Http\UploadedFile;

class DemoPortfolioSeeder extends Seeder
{
    public function run(): void
    {
        $email = (string) config('studyio.demo_portfolio_email');

        if ($email === '') {
            return;
        }

        $user = User::query()->where('email', $email)->first();

        if (! $user) {
            return;
        }

        if ($user->portfolios()->where('full_name', 'Maya Chen')->exists()) {
            return;
        }

        $storage = app(SupabaseStorageService::class);
        $portfolio = $user->portfolios()->create([
            'full_name' => 'Maya Chen',
            'email' => 'maya.chen@example.net',
            'contact_number' => '+886 912 345 678',
            'address' => 'Taipei, Taiwan',
            'about_me' => 'I am an information management student interested in how thoughtful research and clear interfaces can make everyday campus life easier. I enjoy turning small observations into useful digital tools.',
            'profile_picture_path' => $storage->upload($this->asset('maya-chen.png'), 'profiles'),
            'template_id' => Template::query()->where('slug', 'simple')->value('id'),
        ]);

        foreach ([
            ['name' => 'UX Research', 'proficiency' => 'Advanced'],
            ['name' => 'Figma', 'proficiency' => 'Advanced'],
            ['name' => 'Information Architecture', 'proficiency' => 'Advanced'],
            ['name' => 'HTML', 'proficiency' => 'Proficient'],
            ['name' => 'CSS', 'proficiency' => 'Proficient'],
            ['name' => 'JavaScript', 'proficiency' => 'Proficient'],
            ['name' => 'PHP', 'proficiency' => 'Working knowledge'],
            ['name' => 'Laravel', 'proficiency' => 'Working knowledge'],
            ['name' => 'PostgreSQL', 'proficiency' => 'Working knowledge'],
            ['name' => 'Prototyping', 'proficiency' => 'Advanced'],
        ] as $order => $skill) {
            $portfolio->skills()->create([...$skill, 'sort_order' => $order]);
        }

        foreach ([
            [
                'institution' => 'National Taipei University',
                'qualification' => 'Bachelor of Business Administration',
                'field_of_study' => 'Information Management',
                'start_date' => '2022-09-01',
                'end_date' => '2026-06-30',
                'description' => 'Coursework in human-computer interaction, data systems and service design.',
            ],
            [
                'institution' => 'Taipei Municipal Zhongshan Girls High School',
                'qualification' => 'Senior High School Diploma',
                'field_of_study' => 'Humanities and Social Sciences',
                'start_date' => '2019-09-01',
                'end_date' => '2022-06-30',
                'description' => 'Organized the student media club and its first digital archive.',
            ],
        ] as $order => $education) {
            $portfolio->education()->create([...$education, 'sort_order' => $order]);
        }

        foreach ([
            [
                'company' => 'NTPU Human-Computer Interaction Lab',
                'role' => 'UX Research Assistant',
                'start_date' => '2025-02-01',
                'end_date' => null,
                'description' => 'Help plan student interviews, organize research notes and turn findings into low-fidelity service prototypes.',
            ],
            [
                'company' => 'Formosa Digital Studio',
                'role' => 'Product Design Intern',
                'start_date' => '2024-07-01',
                'end_date' => '2024-08-31',
                'description' => 'Prepared interface flows and responsive page concepts for a small nonprofit events platform.',
            ],
        ] as $order => $experience) {
            $portfolio->workExperiences()->create([...$experience, 'sort_order' => $order]);
        }

        foreach ([
            [
                'title' => 'Campus Commons',
                'description' => 'A student community finder shaped around the real pauses in a busy campus day.',
                'tags' => ['UX Research', 'Service Design', 'Figma'],
                'image' => 'campus-commons.png',
            ],
            [
                'title' => 'Quiet Hours',
                'description' => 'A gentle focus planner that helps students make room for one useful task at a time.',
                'tags' => ['Product Design', 'Prototyping', 'Accessibility'],
                'image' => 'quiet-hours.png',
            ],
            [
                'title' => 'Fieldwork Notes',
                'description' => 'An editorial research archive for observations, patterns and small moments in shared spaces.',
                'tags' => ['Information Design', 'Research', 'Editorial'],
                'image' => 'field-notes.png',
            ],
        ] as $order => $project) {
            $image = $project['image'];
            $screenshotPath = $storage->upload($this->asset($image), 'projects');
            $portfolio->projects()->create([
                'title' => $project['title'],
                'description' => $project['description'],
                'tags' => $project['tags'],
                'screenshot_path' => $screenshotPath,
                'sort_order' => $order,
            ]);
        }

        $portfolio->socialLinks()->create([
            'platform' => 'LinkedIn',
            'label' => 'LinkedIn',
            'url' => 'https://www.linkedin.com/',
            'sort_order' => 0,
        ]);
    }

    private function asset(string $name): UploadedFile
    {
        $path = database_path('seeders/assets/'.$name);

        return new UploadedFile($path, $name, 'image/png', UPLOAD_ERR_OK, true);
    }
}
