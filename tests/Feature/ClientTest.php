<?php

namespace Tests\Feature;

use App\Models\Client;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientTest extends TestCase
{
    use RefreshDatabase;

    private function validData(array $overrides = []): array
    {
        return array_merge([
            'name'         => 'Test Client',
            'email'        => 'test@example.com',
            'phone'        => '9876543210',
            'company_name' => 'Test Pvt. Ltd.',
            'city'         => 'Rajkot',
            'address'      => 'Rajkot, Gujarat',
            'status'       => 'active',
        ], $overrides);
    }

    // ---------------------------------------------------------------- routes

    public function test_home_redirects_to_client_list(): void
    {
        $this->get('/')->assertRedirect('/clients');
    }

    public function test_index_create_show_and_edit_pages_load(): void
    {
        $client = Client::factory()->create(['name' => 'Amit Shah']);

        $this->get('/clients')->assertOk()->assertSee('Client List');
        $this->get('/clients/create')->assertOk()->assertSee('Add New Client');
        $this->get("/clients/{$client->id}")->assertOk()->assertSee('Amit Shah');
        $this->get("/clients/{$client->id}/edit")->assertOk()->assertSee('Edit Client');
    }

    public function test_nonexistent_client_returns_404(): void
    {
        $this->get('/clients/9999')->assertNotFound();
        $this->get('/clients/9999/edit')->assertNotFound();
        $this->put('/clients/9999', $this->validData())->assertNotFound();
        $this->delete('/clients/9999')->assertNotFound();
    }

    public function test_empty_list_shows_empty_state(): void
    {
        $this->get('/clients')->assertOk()->assertSee('No clients found');
    }

    // ------------------------------------------------------------------ CRUD

    public function test_client_can_be_created_with_flash_message(): void
    {
        $this->followingRedirects()
            ->post('/clients', $this->validData())
            ->assertOk()
            ->assertSee('Client added successfully.');

        $this->assertDatabaseHas('clients', ['email' => 'test@example.com', 'status' => 'active']);
    }

    public function test_optional_fields_can_be_empty(): void
    {
        $this->post('/clients', $this->validData([
            'company_name' => '', 'address' => '', 'city' => '',
        ]))->assertRedirect('/clients');

        $this->assertDatabaseHas('clients', ['email' => 'test@example.com']);
    }

    public function test_client_can_be_updated(): void
    {
        $client = Client::factory()->create(['city' => 'Surat']);

        $this->put("/clients/{$client->id}", $this->validData([
            'email' => $client->email,      // the client's own e-mail is allowed
            'city'  => 'Rajkot',
        ]))->assertRedirect('/clients')->assertSessionHas('success');

        $this->assertDatabaseHas('clients', ['id' => $client->id, 'city' => 'Rajkot']);
    }

    public function test_client_can_be_deleted(): void
    {
        $client = Client::factory()->create();

        $this->delete("/clients/{$client->id}")
            ->assertRedirect('/clients')
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('clients', ['id' => $client->id]);
    }

    // ------------------------------------------------------------ validation

    public function test_name_is_required_and_old_input_is_kept(): void
    {
        $this->from('/clients/create')
            ->post('/clients', $this->validData(['name' => '', 'city' => 'Keep Me']))
            ->assertRedirect('/clients/create')
            ->assertSessionHasErrors('name')
            ->assertSessionHasInput('city', 'Keep Me');

        $this->assertDatabaseCount('clients', 0);
    }

    public function test_invalid_email_is_rejected(): void
    {
        $this->post('/clients', $this->validData(['email' => 'abc@']))
            ->assertSessionHasErrors('email');
    }

    public function test_duplicate_email_is_rejected_on_create(): void
    {
        Client::factory()->create(['email' => 'test@example.com']);

        $this->post('/clients', $this->validData())->assertSessionHasErrors('email');
        $this->assertDatabaseCount('clients', 1);
    }

    public function test_duplicate_email_check_ignores_case_and_spaces(): void
    {
        Client::factory()->create(['email' => 'test@example.com']);

        $this->post('/clients', $this->validData(['email' => '  TEST@Example.com ']))
            ->assertSessionHasErrors('email');
    }

    public function test_another_clients_email_is_rejected_on_update(): void
    {
        $first  = Client::factory()->create(['email' => 'first@example.com']);
        $second = Client::factory()->create(['email' => 'second@example.com']);

        $this->put("/clients/{$second->id}", $this->validData(['email' => $first->email]))
            ->assertSessionHasErrors('email');
    }

    public function test_phone_must_be_10_to_15_digits(): void
    {
        foreach (['abcdefghij', '12345', '1234567890123456', '98765 43210', '+919876543210'] as $bad) {
            $this->post('/clients', $this->validData(['phone' => $bad]))
                ->assertSessionHasErrors('phone');
        }

        $this->post('/clients', $this->validData(['phone' => '987654321012345']))
            ->assertSessionDoesntHaveErrors('phone');
    }

    public function test_status_must_be_active_or_inactive(): void
    {
        $this->post('/clients', $this->validData(['status' => 'unknown']))
            ->assertSessionHasErrors('status');
    }

    public function test_field_lengths_are_limited(): void
    {
        $this->post('/clients', $this->validData(['name' => str_repeat('a', 101)]))
            ->assertSessionHasErrors('name');
        $this->post('/clients', $this->validData(['city' => str_repeat('a', 81)]))
            ->assertSessionHasErrors('city');
    }

    // -------------------------------------------------- search + pagination

    public function test_search_by_name_email_and_phone(): void
    {
        Client::factory()->create(['name' => 'Amit Shah', 'email' => 'amit@example.com', 'phone' => '9876543210']);
        Client::factory()->create(['name' => 'Isha Desai', 'email' => 'isha@example.org', 'phone' => '9012345678']);

        $this->get('/clients?search=Amit')->assertSee('Amit Shah')->assertDontSee('Isha Desai');
        $this->get('/clients?search=isha@example.org')->assertSee('Isha Desai')->assertDontSee('Amit Shah');
        $this->get('/clients?search=0123')->assertSee('Isha Desai')->assertDontSee('Amit Shah');
    }

    public function test_search_is_partial_and_case_insensitive(): void
    {
        Client::factory()->create(['name' => 'Amit Shah']);

        $this->get('/clients?search=mit sha')->assertSee('Amit Shah');
        $this->get('/clients?search=AMIT')->assertSee('Amit Shah');
    }

    public function test_empty_blank_or_array_search_is_safe(): void
    {
        Client::factory()->create(['name' => 'Amit Shah']);

        $this->get('/clients?search=')->assertOk()->assertSee('Amit Shah');
        $this->get('/clients?search=%20%20')->assertOk()->assertSee('Amit Shah');
        $this->get('/clients?search[]=x')->assertOk();
    }

    public function test_search_treats_wildcards_and_sql_as_plain_text(): void
    {
        Client::factory()->create(['name' => 'Amit Shah']);

        $this->get('/clients?search=%25')->assertOk()->assertDontSee('Amit Shah');
        $this->get('/clients?search=_____')->assertOk()->assertDontSee('Amit Shah');
        $this->get("/clients?search=' OR 1=1 --")->assertOk()->assertDontSee('Amit Shah');
    }

    public function test_pagination_shows_10_per_page_and_keeps_search(): void
    {
        Client::factory()->count(15)->create(['name' => 'Searchable Person']);

        $page1 = $this->get('/clients?search=Searchable')->assertOk();
        $this->assertSame(10, substr_count($page1->getContent(), 'class="nm"'));
        $page1->assertSee('search=Searchable', false)->assertSee('page=2', false);

        $page2 = $this->get('/clients?search=Searchable&page=2')->assertOk();
        $this->assertSame(5, substr_count($page2->getContent(), 'class="nm"'));
        $page2->assertSee('11 to 15 of 15');
    }

    // -------------------------------------------------------------- security

    public function test_delete_is_not_possible_with_get(): void
    {
        $client = Client::factory()->create();

        $this->get("/clients/{$client->id}/delete")->assertNotFound();
        $this->get("/clients/{$client->id}")->assertOk();

        $this->assertDatabaseHas('clients', ['id' => $client->id]);
    }

    public function test_forms_contain_csrf_token_and_correct_method(): void
    {
        $client = Client::factory()->create();

        $this->get('/clients/create')->assertSee('name="_token"', false);
        $this->get("/clients/{$client->id}/edit")
            ->assertSee('name="_token"', false)
            ->assertSee('name="_method" value="PUT"', false);
        $this->get('/clients')->assertSee('name="_method" value="DELETE"', false);
    }

    public function test_user_input_is_escaped_in_output(): void
    {
        Client::factory()->create(['name' => '<script>alert(1)</script>']);

        $this->get('/clients')
            ->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false)
            ->assertDontSee('<script>alert(1)</script>', false);
    }

    public function test_mass_assignment_of_id_and_timestamps_is_ignored(): void
    {
        $this->post('/clients', $this->validData(['id' => 999, 'created_at' => '2000-01-01 00:00:00']));

        $this->assertDatabaseMissing('clients', ['id' => 999]);
        $this->assertNotSame(2000, Client::firstOrFail()->created_at->year);
    }

    // ------------------------------------------------- interface features

    public function test_list_page_has_live_results_block_and_mobile_labels(): void
    {
        Client::factory()->create(['name' => 'Amit Shah']);

        $this->get('/clients')
            ->assertOk()
            ->assertSee('id="results"', false)
            ->assertSee('id="clear-search"', false)
            ->assertSee('data-label="Email"', false);
    }

    public function test_clear_link_is_hidden_without_search_and_visible_with_search(): void
    {
        $this->get('/clients')->assertSee('id="clear-search"', false);
        $this->assertMatchesRegularExpression('/id="clear-search"\s+hidden\s*>/', $this->get('/clients')->getContent());

        $html = $this->get('/clients?search=abc')->getContent();
        $this->assertMatchesRegularExpression('/id="clear-search"\s*>/', $html);
    }
}
