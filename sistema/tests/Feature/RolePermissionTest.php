<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Venta;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RolePermissionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();   // roles, usuarios de la guía, medicamentos, clientes y ventas
    }

    private function user(string $email): User
    {
        return User::query()->where('email', $email)->firstOrFail();
    }

    public function test_cashier_gets_403_on_restricted_modules(): void
    {
        $cajero = $this->user('cmendoza@sanjuan.com');

        foreach (['roles.index', 'usuarios.index', 'categorias.index', 'medicamentos.index', 'reportes.ventas'] as $ruta) {
            $this->actingAs($cajero)->get(route($ruta))
                ->assertForbidden()
                ->assertSee('Acceso denegado');
        }
    }

    public function test_cashier_can_use_dashboard_customers_and_sales(): void
    {
        $cajero = $this->user('cmendoza@sanjuan.com');

        foreach (['dashboard.index', 'clientes.index', 'ventas.index', 'ventas.create'] as $ruta) {
            $this->actingAs($cajero)->get(route($ruta))->assertOk();
        }
    }

    public function test_only_the_administrator_can_cancel_a_sale(): void
    {
        $venta = Venta::query()->firstOrFail();

        $this->actingAs($this->user('cmendoza@sanjuan.com'))
            ->delete(route('ventas.destroy', $venta))
            ->assertForbidden();
        $this->actingAs($this->user('rquispe@sanjuan.com'))
            ->delete(route('ventas.destroy', $venta))
            ->assertForbidden();
        $this->assertDatabaseHas('ventas', ['id' => $venta->id]);

        $this->actingAs($this->user('admin@sanjuan.com'))
            ->delete(route('ventas.destroy', $venta))
            ->assertRedirect(route('ventas.index'));
        $this->assertDatabaseMissing('ventas', ['id' => $venta->id]);
    }

    public function test_pharmacist_opens_medicines_and_reports_but_not_settings(): void
    {
        $quimico = $this->user('rquispe@sanjuan.com');

        $this->actingAs($quimico)->get(route('medicamentos.index'))->assertOk();
        $this->actingAs($quimico)->get(route('reportes.ventas'))->assertOk();
        $this->actingAs($quimico)->get(route('roles.index'))->assertForbidden();
        $this->actingAs($quimico)->get(route('usuarios.index'))->assertForbidden();
    }

    public function test_administrator_opens_every_module(): void
    {
        $admin = $this->user('admin@sanjuan.com');

        foreach (['dashboard.index', 'clientes.index', 'ventas.index', 'medicamentos.index',
                  'reportes.ventas', 'roles.index', 'usuarios.index', 'categorias.index'] as $ruta) {
            $this->actingAs($admin)->get(route($ruta))->assertOk();
        }
    }
}
