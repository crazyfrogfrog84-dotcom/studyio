<?php

namespace Tests\Feature;

use App\Models\Portfolio;
use App\Models\Template;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthPortfolioTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_login_before_creating_a_portfolio(): void
    {
        $this->get(route('portfolios.create'))
            ->assertRedirect(route('login'));

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Create portfolio')
            ->assertSee('Create account');
    }

    public function test_registration_login_logout_and_database_sessions_work(): void
    {
        config(['session.driver' => 'database']);

        $this->post(route('register.store'), [
            'name' => 'Maya Chen',
            'email' => 'maya.test@example.net',
            'password' => 'PortfolioTest42',
            'password_confirmation' => 'PortfolioTest42',
        ])->assertRedirect(route('portfolios.index'));

        $user = User::query()->where('email', 'maya.test@example.net')->firstOrFail();
        $this->assertAuthenticatedAs($user);
        $this->assertTrue(Hash::check('PortfolioTest42', $user->password));
        $this->assertNotSame('PortfolioTest42', $user->password);
        $this->assertDatabaseHas('sessions', ['user_id' => $user->id]);

        $this->post(route('logout'))
            ->assertRedirect(route('home'));
        $this->assertGuest();

        $this->post(route('login.store'), [
            'email' => 'maya.test@example.net',
            'password' => 'PortfolioTest42',
        ])->assertRedirect(route('portfolios.index'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_portfolios_are_private_to_their_owner_and_cascade_on_account_deletion(): void
    {
        $owner = User::create([
            'name' => 'Owner',
            'email' => 'owner@example.net',
            'password' => 'OwnerTest42',
        ]);
        $otherUser = User::create([
            'name' => 'Other User',
            'email' => 'other@example.net',
            'password' => 'OtherTest42',
        ]);
        $portfolio = Portfolio::create([
            'user_id' => $owner->id,
            'full_name' => 'Owner Portfolio',
            'email' => 'portfolio@example.net',
        ]);

        $this->actingAs($otherUser)
            ->get(route('portfolios.show', $portfolio))
            ->assertForbidden();
        $this->actingAs($otherUser)
            ->put(route('portfolios.update', $portfolio), [])
            ->assertForbidden();
        $this->actingAs($otherUser)
            ->delete(route('portfolios.destroy', $portfolio))
            ->assertForbidden();

        $this->actingAs($owner)
            ->get(route('portfolios.show', $portfolio))
            ->assertOk();

        $owner->delete();
        $this->assertDatabaseMissing('portfolios', ['id' => $portfolio->id]);
    }

    public function test_all_three_templates_render_without_empty_sections(): void
    {
        $owner = User::create([
            'name' => 'Template Reviewer',
            'email' => 'templates@example.net',
            'password' => 'ReviewTest42',
        ]);

        foreach (['simple', 'modern', 'creative'] as $index => $slug) {
            $template = Template::create([
                'slug' => $slug,
                'name' => ucfirst($slug),
                'description' => $slug.' portfolio layout',
                'sort_order' => $index,
            ]);
            $portfolio = Portfolio::create([
                'user_id' => $owner->id,
                'template_id' => $template->id,
                'full_name' => 'Taylor Morgan',
                'email' => 'taylor@example.net',
            ]);

            $this->actingAs($owner)
                ->get(route('portfolios.show', $portfolio))
                ->assertOk()
                ->assertSee('template-'.$slug)
                ->assertDontSee('id="projects"')
                ->assertDontSee('id="skills"')
                ->assertDontSee('id="experience"')
                ->assertDontSee('id="education"');
        }
    }

    public function test_project_images_can_only_be_retained_from_the_current_portfolio(): void
    {
        $owner = User::create([
            'name' => 'Image Owner',
            'email' => 'images@example.net',
            'password' => 'ImageTest42',
        ]);
        $otherOwner = User::create([
            'name' => 'Other Owner',
            'email' => 'other-images@example.net',
            'password' => 'OtherImageTest42',
        ]);
        $portfolio = $owner->portfolios()->create([
            'full_name' => 'Image Owner',
            'email' => 'portfolio-images@example.net',
        ]);
        $ownProject = $portfolio->projects()->create([
            'title' => 'My project',
            'screenshot_path' => 'projects/owned-image.png',
            'tags' => [],
            'sort_order' => 0,
        ]);
        $foreignPortfolio = $otherOwner->portfolios()->create([
            'full_name' => 'Other Owner',
            'email' => 'foreign-portfolio@example.net',
        ]);
        $foreignProject = $foreignPortfolio->projects()->create([
            'title' => 'Other project',
            'screenshot_path' => 'projects/foreign-image.png',
            'tags' => [],
            'sort_order' => 0,
        ]);

        $this->actingAs($owner)
            ->put(route('portfolios.update', $portfolio), [
                'full_name' => 'Image Owner',
                'email' => 'portfolio-images@example.net',
                'projects' => [
                    ['id' => $ownProject->id, 'title' => 'Updated project', 'screenshot_path' => 'projects/forged-image.png'],
                    ['id' => $foreignProject->id, 'title' => 'Copied project', 'screenshot_path' => 'projects/foreign-image.png'],
                ],
            ])
            ->assertRedirect(route('portfolios.show', $portfolio));

        $this->assertDatabaseHas('projects', [
            'portfolio_id' => $portfolio->id,
            'title' => 'Updated project',
            'screenshot_path' => 'projects/owned-image.png',
        ]);
        $this->assertDatabaseHas('projects', [
            'portfolio_id' => $portfolio->id,
            'title' => 'Copied project',
            'screenshot_path' => null,
        ]);
        $this->assertDatabaseHas('projects', [
            'portfolio_id' => $foreignPortfolio->id,
            'screenshot_path' => 'projects/foreign-image.png',
        ]);
    }
}
