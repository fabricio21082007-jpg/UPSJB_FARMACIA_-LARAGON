<?php

namespace Tests\Feature;

use App\Models\Categoria;
use App\Models\Rol;
use App\Models\User;
use App\Models\Venta;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardReportTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->admin = User::query()->where('email', 'admin@sanjuan.com')->firstOrFail();
    }

    public function test_seeders_create_the_guide_data(): void
    {
        $this->assertSame(3, Rol::count());
        $this->assertSame(4, User::count());
        $this->assertSame(60, Venta::count());
        $this->assertTrue(Venta::whereDate('fecha', today())->exists());
    }

    public function test_dashboard_shows_indicators_and_charts(): void
    {
        $ventasHoy = Venta::whereDate('fecha', today())->sum('total');

        $this->actingAs($this->admin)->get(route('dashboard.index'))
            ->assertOk()
            ->assertSee('Ventas de hoy')
            ->assertSee('S/ '.number_format($ventasHoy, 2))
            ->assertSee('Medicamentos con stock bajo')
            ->assertSee('js/chart.umd.min.js')
            ->assertSee('id="chartDays"', false)
            ->assertSee('id="chartCategories"', false)
            ->assertSee('id="chartTop"', false);
    }

    public function test_report_without_filters_matches_the_month_total(): void
    {
        $ventasMes = Venta::whereBetween('fecha', [now()->startOfMonth(), now()->endOfDay()])->sum('total');

        $this->actingAs($this->admin)->get(route('reportes.ventas'))
            ->assertOk()
            ->assertSee('Total vendido')
            ->assertSee('S/ '.number_format($ventasMes, 2));
    }

    public function test_report_filters_by_seller_and_category(): void
    {
        $vendedor = User::query()->where('email', 'ltorres@sanjuan.com')->firstOrFail();
        $categoria = Categoria::query()->where('nombre', 'Antibióticos')->firstOrFail();

        $response = $this->actingAs($this->admin)->get(route('reportes.ventas', [
            'desde' => now()->subDays(89)->toDateString(),
            'hasta' => now()->toDateString(),
            'user_id' => $vendedor->id,
            'categoria_id' => $categoria->id,
        ]))->assertOk();

        $esperadas = Venta::where('user_id', $vendedor->id)
            ->whereHas('detalles.medicamento', fn ($m) => $m->where('categoria_id', $categoria->id))
            ->count();
        $response->assertViewHas('ventas', fn ($ventas) => $ventas->count() === $esperadas
            && $ventas->every(fn ($venta) => $venta->user_id === $vendedor->id));
    }

    public function test_report_rejects_an_inverted_date_range_in_spanish(): void
    {
        $this->actingAs($this->admin)
            ->from(route('reportes.ventas'))
            ->get(route('reportes.ventas', ['desde' => '2026-11-20', 'hasta' => '2026-11-01']))
            ->assertRedirect(route('reportes.ventas'))
            ->assertSessionHasErrors([
                'hasta' => 'El campo fecha final debe ser una fecha igual o posterior a fecha inicial.',
            ]);
    }
}
