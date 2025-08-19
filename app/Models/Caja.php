<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use App\Models\Pago;
use App\Models\Salida;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class Caja extends BaseModel
{
	   protected $connection = 'tenant';
	   
	     public function resolveRouteBinding($value, $field = null)
    {
        // 1) Ajusta la conexión tenant según el usuario autenticado
        if ($user = Auth::user()) {
            $dbName = $user->peluqueria->db;                               // el nombre dynamic de la BD
            Config::set('database.connections.tenant.database', $dbName);
            DB::purge('tenant');
            DB::reconnect('tenant');
        }

        // 2) Resuelve el modelo usando esa conexión
        $field = $field ?: $this->getRouteKeyName();
        return $this->on('tenant')
                    ->where($field, $value)
                    ->firstOrFail();
    }
	   
	   
    protected $fillable = ['fecha_hora', 'base', 'valor', 'responsable'];
    protected $casts    = ['fecha_hora' => 'datetime', 'base' => 'float', 'valor' => 'float'];

    /* ------------------------------------------------------------------
       ╭─  Helpers de rango de fechas para el mismo día ─────────────────╮ */
    private function rangoDia(): array
    {
        $inicio = $this->fecha_hora->copy()->startOfDay(); // 00:00
        $fin    = $this->fecha_hora->copy()->endOfDay();   // 23:59:59
        return [$inicio, $fin];
    }

    private function pagosQuery()
    {
        [$inicio, $fin] = $this->rangoDia();
        return Pago::whereBetween('fecha_hora', [$inicio, $fin]);
    }

    private function salidasQuery()
    {
        [$inicio, $fin] = $this->rangoDia();
        return Salida::whereBetween('fecha', [$inicio, $fin]);
    }
	
	
    /* ------------------------------------------------------------------ */

    /* ====================  MÉTRICAS PÚBLICAS  ========================= */

    /** Total en caja: base + entradas – salidas */
    public function total(): float
    {
        return $this->base
             + $this->pagosEfectivo()
             + $this->pagosBanco()
             - $this->totalSalidas();
    }

    /** Entradas en efectivo */
    public function pagosEfectivo(): float
    {
        return (float) $this->pagosQuery()
            ->where('cuenta', 'Efectivo')
            ->sum('valor');
    }
	 /** Total salidas del día */
    public function base(): float
    {
        return (float) $this->base;
    }

    /** Entradas vía banco/tarjeta */
    public function pagosBanco(): float
    {
        return (float) $this->pagosQuery()
            ->where('cuenta', '!=', 'Efectivo')
            ->sum('valor');
    }

    /** Total salidas del día */
    public function totalSalidas(): float
    {
        return (float) $this->salidasQuery()->sum('valor');
    }

    /** Listados que la vista necesita --------------------------------- */
    public function salidas()
    {
        return $this->salidasQuery()->orderBy('fecha')->get();
    }

    public function entradas()
    {
        return $this->pagosQuery()
            ->selectRaw('cuenta as concepto, DATE(fecha_hora) as fecha, SUM(valor) as valor')
            ->groupBy('cuenta', 'fecha_hora')
            ->orderBy('fecha_hora')
            ->get();
    }

    public function pagosPorOrden()
    {
        return $this->pagosQuery()->orderBy('fecha_hora')->get();
    }

    public function totalEntradas(): float
    {
        return (float) $this->pagosQuery()->sum('valor');
    }

    public function totalFacturacion(): float
    {
        return $this->totalEntradas();
    }
}
