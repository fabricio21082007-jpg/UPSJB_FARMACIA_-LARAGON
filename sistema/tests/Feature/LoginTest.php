<?php

namespace Tests\Feature;

use App\Models\Rol;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_shows_the_prototype_design_and_form(): void
    {
        $response = $this->get(route('login'));

        $response->assertOk()
            ->assertSee('Gestiona tu farmacia de forma simple y segura.')
            ->assertSee('name="email"', false)
            ->assertSee('name="password"', false)
            ->assertSee('name="remember"', false)
            ->assertDontSee('id="loginForm"', false)
            ->assertDontSee('js/app.js');
    }

    public function test_guest_is_redirected_to_login_from_medicines(): void
    {
        $this->get(route('medicamentos.index'))->assertRedirect(route('login'));
    }

    public function test_authenticated_user_is_redirected_from_login_to_dashboard(): void
    {
        $user = $this->createAdmin();

        $this->actingAs($user)
            ->get(route('login'))
            ->assertRedirect(route('dashboard.index'));
    }

    public function test_sidebar_shows_the_authenticated_users_identity_and_role(): void
    {
        $role = Rol::query()->create([
            'nombre' => 'Químico farmacéutico',
            'descripcion' => 'Rol para pruebas.',
        ]);
        $user = User::query()->create([
            'name' => 'Ana Martínez',
            'email' => 'ana@sanjuan.com',
            'password' => 'password',
            'rol_id' => $role->id,
            'dni' => '87654321',
        ]);

        $this->actingAs($user)
            ->get(route('dashboard.index'))
            ->assertOk()
            ->assertSee('Ana Martínez')
            ->assertSee('ana@sanjuan.com')
            ->assertSee('Químico farmacéutico')
            ->assertSee('>AN</span>', false);
    }

    public function test_valid_credentials_authenticate_and_redirect_to_dashboard(): void
    {
        $this->seed();
        $user = User::query()->where('email', 'admin@sanjuan.com')->firstOrFail();

        $response = $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'password',
            'remember' => '1',
        ]);

        $response->assertRedirect(route('dashboard.index'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_invalid_credentials_return_to_login_with_the_error_and_email(): void
    {
        $user = $this->createAdmin();

        $response = $this->from(route('login'))
            ->followingRedirects()
            ->post(route('login'), [
                'email' => $user->email,
                'password' => 'incorrecta',
            ]);

        $response->assertOk()
            ->assertSee('El correo o la contraseña no son correctos.')
            ->assertSee('value="'.$user->email.'"', false);
    }

    public function test_invalid_credentials_flash_only_the_email_as_old_input(): void
    {
        $user = $this->createAdmin();

        $this->from(route('login'))
            ->post(route('login'), [
                'email' => $user->email,
                'password' => 'incorrecta',
            ])
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors([
                'email' => 'El correo o la contraseña no son correctos.',
            ])
            ->assertSessionHas('_old_input', ['email' => $user->email]);
    }

    public function test_logout_invalidates_the_authenticated_session(): void
    {
        $user = $this->createAdmin();

        $response = $this->actingAs($user)->post(route('logout'));

        $response->assertRedirect(route('login'))
            ->assertSessionHas('ok', 'Cerraste sesión correctamente.');
        $this->assertGuest();

        $this->get(route('login'))->assertSee('Cerraste sesión correctamente.');
    }

    private function createAdmin(): User
    {
        $role = Rol::query()->create([
            'nombre' => 'Administrador',
            'descripcion' => 'Rol para pruebas.',
        ]);

        return User::query()->create([
            'name' => 'Administrador',
            'email' => 'admin@sanjuan.com',
            'password' => 'password',
            'rol_id' => $role->id,
            'dni' => '12345678',
        ]);
    }
}
