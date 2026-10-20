<?php

namespace App\Http\Controllers;

use App\Http\Requests\PortfolioRequest;
use App\Models\Portfolio;
use App\Models\Template;
use App\Services\SupabaseStorageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class PortfolioController extends Controller
{
    public function __construct(private readonly SupabaseStorageService $storage) {}

    public function index(): View
    {
        $this->authorize('viewAny', Portfolio::class);
        $portfolios = request()->user()->portfolios()->with('template')->latest()->paginate(12);

        return view('portfolios.index', compact('portfolios'));
    }

    public function create(): View
    {
        $this->authorize('create', Portfolio::class);

        return view('portfolios.form', ['portfolio' => new Portfolio, 'pictureUrl' => null, 'projectImageUrls' => []]);
    }

    public function store(PortfolioRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $profilePicturePath = $request->file('profile_picture')
            ? $this->storage->upload($request->file('profile_picture'), 'profiles')
            : null;

        $projectFiles = [];
        foreach ($request->file('projects', []) as $index => $files) {
            if (isset($files['screenshot'])) {
                $projectFiles[$index] = $this->storage->upload($files['screenshot'], 'projects');
            }
        }

        $portfolio = DB::transaction(function () use ($request, $data, $profilePicturePath, $projectFiles) {
            $portfolio = $request->user()->portfolios()->create([
                ...Arr::only($data, ['full_name', 'email', 'contact_number', 'address', 'about_me']),
                'profile_picture_path' => $profilePicturePath,
            ]);
            $this->syncDetails($portfolio, $data, $projectFiles);

            return $portfolio;
        });

        if ($request->boolean('save_for_later')) {
            return redirect()->route('portfolios.index')->with('status', 'Your portfolio has been saved for later.');
        }

        return redirect()->route('portfolios.templates.edit', $portfolio)->with('status', 'Your portfolio is saved. Choose a template to preview it.');
    }

    public function show(Portfolio $portfolio): View
    {
        $this->authorize('view', $portfolio);

        $portfolio->load(['template', 'education', 'skills', 'projects', 'workExperiences', 'socialLinks']);
        $pictureUrl = $this->storage->signedUrl($portfolio->profile_picture_path);
        $projectImageUrls = $portfolio->projects->mapWithKeys(fn ($project) => [$project->screenshot_path => $this->storage->signedUrl($project->screenshot_path)])->all();

        return view('portfolios.show', compact('portfolio', 'pictureUrl', 'projectImageUrls'));
    }

    public function edit(Portfolio $portfolio): View
    {
        $this->authorize('update', $portfolio);

        $portfolio->load(['education', 'skills', 'projects', 'workExperiences', 'socialLinks']);
        $pictureUrl = $this->storage->signedUrl($portfolio->profile_picture_path);
        $projectImageUrls = $portfolio->projects->mapWithKeys(fn ($project) => [$project->screenshot_path => $this->storage->signedUrl($project->screenshot_path)])->all();

        return view('portfolios.form', compact('portfolio', 'pictureUrl', 'projectImageUrls'));
    }

    public function confirmDelete(Portfolio $portfolio): View
    {
        $this->authorize('delete', $portfolio);

        return view('portfolios.confirm-delete', compact('portfolio'));
    }

    public function update(PortfolioRequest $request, Portfolio $portfolio): RedirectResponse
    {
        $this->authorize('update', $portfolio);

        $data = $request->validated();
        $oldPaths = [];
        $profilePicturePath = $portfolio->profile_picture_path;

        if ($request->file('profile_picture')) {
            $profilePicturePath = $this->storage->upload($request->file('profile_picture'), 'profiles');
            $oldPaths[] = $portfolio->profile_picture_path;
        }

        $projectFiles = [];
        foreach ($request->file('projects', []) as $index => $files) {
            if (isset($files['screenshot'])) {
                $projectFiles[$index] = $this->storage->upload($files['screenshot'], 'projects');
            }
        }

        $existingProjectImages = $portfolio->projects()->pluck('screenshot_path', 'id')->all();
        foreach ($existingProjectImages as $path) {
            $oldPaths[] = $path;
        }

        DB::transaction(function () use ($portfolio, $data, $profilePicturePath, $projectFiles, $existingProjectImages, &$oldPaths) {
            $portfolio->update([
                ...Arr::only($data, ['full_name', 'email', 'contact_number', 'address', 'about_me']),
                'profile_picture_path' => $profilePicturePath,
            ]);
            $this->syncDetails($portfolio, $data, $projectFiles, $existingProjectImages, $oldPaths);
        });

        $portfolio->load('projects');
        $currentPaths = array_filter([$portfolio->profile_picture_path, ...$portfolio->projects->pluck('screenshot_path')->all()]);
        $this->deleteStoragePaths(array_values(array_diff(array_filter($oldPaths), $currentPaths)));

        if ($request->boolean('save_for_later')) {
            return redirect()->route('portfolios.index')->with('status', 'Your portfolio has been saved for later.');
        }

        return redirect()->route('portfolios.show', $portfolio)->with('status', 'Your portfolio has been updated.');
    }

    public function destroy(Portfolio $portfolio): RedirectResponse
    {
        $this->authorize('delete', $portfolio);

        $portfolio->load('projects');
        $paths = [$portfolio->profile_picture_path, ...$portfolio->projects->pluck('screenshot_path')->all()];
        $portfolio->delete();
        $this->deleteStoragePaths($paths);

        return redirect()->route('portfolios.index')->with('status', 'Your portfolio has been deleted.');
    }

    public function templates(Portfolio $portfolio): View
    {
        $this->authorize('view', $portfolio);

        $templates = Template::orderBy('sort_order')->get();

        return view('portfolios.templates', compact('portfolio', 'templates'));
    }

    public function selectTemplate(Request $request, Portfolio $portfolio): RedirectResponse
    {
        $this->authorize('update', $portfolio);

        $validated = $request->validate(['template_id' => ['required', 'integer', 'exists:templates,id']]);
        $portfolio->update(['template_id' => $validated['template_id']]);

        return redirect()->route('portfolios.show', $portfolio)->with('status', 'Template applied. Your portfolio is ready to preview.');
    }

    private function syncDetails(Portfolio $portfolio, array $data, array $projectFiles, array $existingProjectImages = [], array &$oldPaths = []): void
    {
        foreach ([
            'education' => ['institution', 'qualification', 'field_of_study', 'start_date', 'end_date', 'description'],
            'skills' => ['name', 'proficiency'],
            'work_experience' => ['company', 'role', 'start_date', 'end_date', 'description'],
            'social_links' => ['platform', 'label', 'url'],
        ] as $key => $fields) {
            $relation = match ($key) {
                'work_experience' => $portfolio->workExperiences(),
                'social_links' => $portfolio->socialLinks(),
                default => $portfolio->{$key}(),
            };
            $relation->delete();
            $rows = $this->meaningfulRows($data[$key] ?? [], $fields);
            foreach ($rows as $order => $row) {
                $relation->create([...Arr::only($row, $fields), 'sort_order' => $order]);
            }
        }

        $portfolio->projects()->delete();
        foreach ($data['projects'] ?? [] as $index => $row) {
            if (blank($row['title'] ?? null)) {
                continue;
            }
            $existingPath = $existingProjectImages[(int) ($row['id'] ?? 0)] ?? null;
            $path = $projectFiles[$index] ?? $existingPath;
            $tags = collect(explode(',', $row['tags'] ?? ''))
                ->map(fn ($tag) => trim($tag))
                ->filter()
                ->unique()
                ->take(8)
                ->values()
                ->all();
            $portfolio->projects()->create([
                ...Arr::only($row, ['title', 'description', 'project_url']),
                'screenshot_path' => $path,
                'tags' => $tags,
                'sort_order' => $portfolio->projects()->count(),
            ]);
        }
    }

    private function meaningfulRows(array $rows, array $fields): array
    {
        return collect($rows)
            ->filter(fn ($row) => collect($fields)->contains(fn ($field) => filled($row[$field] ?? null)))
            ->values()
            ->all();
    }

    private function deleteStoragePaths(array $paths): void
    {
        try {
            $this->storage->delete($paths);
        } catch (\Throwable $exception) {
            Log::warning('Unable to remove portfolio assets from Supabase Storage.', ['exception' => $exception::class]);
        }
    }
}
