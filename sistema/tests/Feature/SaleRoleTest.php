<?php

namespace Tests\Feature;

use App\Models\Categoria;
use App\Models\Cliente;
use App\Models\Medicamento;
use App\Models\Rol;
use App\Models\User;
use App\Models\Venta;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SaleRoleTest extends TestCase
{
    use RefreshDatabase;

    public function test_cashier_sees_only_permitted_menu_items_and_cannot_cancel_sales(): void
    {
        $cashier = $this->createUser('Cajero', 'cajero@sanjuan.com', 'Carlos Mendoza', '12345678');
        $this->createSaleFor($cashier);

        $this->actingAs($cashier)
            ->get(route('ventas.index'))
            ->assertOk()
            ->assertSee('Dashboard')
            ->assertSee('Clientes')
            ->assertSee('Ventas')
            ->assertDontSee('Reportes')
            ->assertDontSee('Medicamentos')
            ->assertDontSee('Configuraciones')
            ->assertDontSee('Categorías')
            ->assertDontSee('Roles')
            ->assertDontSee('Usuarios')
            ->assertDontSee('Anular');
    }

    public function test_administrator_sees_the_full_menu_and_can_cancel_sales(): void
    {
        $administrator = $this->createUser('Administrador', 'admin@sanjuan.com', 'Ana Torres', '87654321');
        $this->createSaleFor($administrator);

        $this->actingAs($administrator)
            ->get(route('ventas.index'))
            ->assertOk()
            ->assertSee('Reportes')
            ->assertSee('Medicamentos')
            ->assertSee('Configuraciones')
            ->assertSee('Categorías')
            ->assertSee('Roles')
            ->assertSee('Usuarios')
            ->assertSee('Anular');
    }

    public function test_sale_form_shows_the_authenticated_seller_without_a_user_list(): void
    {
        $cashier = $this->createUser('Cajero', 'cajero@sanjuan.com', 'Carlos Mendoza', '12345678');

        $this->actingAs($cashier)
            ->get(route('ventas.create'))
            ->assertOk()
            ->assertSee('value="Carlos Mendoza" disabled', false)
            ->assertDontSee('name="user_id"', false);
    }

    public function test_store_assigns_the_authenticated_user_as_seller(): void
    {
        $cashier = $this->createUser('Cajero', 'cajero@sanjuan.com', 'Carlos Mendoza', '12345678');
        $otherUser = $this->createUser('Administrador', 'admin@sanjuan.com', 'Ana Torres', '87654321');
        $customer = Cliente::query()->create([
            'nombre' => 'Cliente de prueba',
            'dni' => '11112222',
        ]);
        $category = Categoria::query()->create(['nombre' => 'Medicamentos']);
        $medicine = Medicamento::query()->create([
            'categoria_id' => $category->id,
            'nombre' => 'Paracetamol',
            'precio' => 10,
            'stock' => 5,
        ]);

        $response = $this->actingAs($cashier)->post(route('ventas.store'), [
            'cliente_id' => $customer->id,
            'user_id' => $otherUser->id,
            'items' => [
                ['medicamento_id' => $medicine->id, 'cantidad' => 2],
            ],
        ]);

        $response->assertRedirect(route('ventas.show', Venta::query()->firstOrFail()));
        $this->assertDatabaseHas('ventas', [
            'cliente_id' => $customer->id,
            'user_id' => $cashier->id,
        ]);
        $this->assertDatabaseHas('medicamentos', [
            'id' => $medicine->id,
            'stock' => 3,
        ]);
    }

    private function createUser(string $roleName, string $email, string $name, string $dni): User
    {
        $role = Rol::query()->create([
            'nombre' => $roleName,
            'descripcion' => 'Rol para pruebas.',
        ]);

        return User::query()->create([
            'rol_id' => $role->id,
            'name' => $name,
            'dni' => $dni,
            'email' => $email,
            'password' => 'password',
        ]);
    }

    private function createSaleFor(User $user): Venta
    {
        $customer = Cliente::query()->create([
            'nombre' => 'Cliente de prueba',
            'dni' => '11112222',
        ]);

        return Venta::query()->create([
            'cliente_id' => $customer->id,
            'user_id' => $user->id,
            'fecha' => now(),
            'total' => 10,
            'igv' => 1.53,
        ]);
    }
}
