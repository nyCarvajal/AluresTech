<?php

namespace App\Http\Controllers;

use App\Models\OrdenDeCompra;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrdenPdfMail;

class OrdenDeCompraController extends Controller
{
    public function index()
    {
        $ordenes = OrdenDeCompra::with('clienterel')
            ->withSum('ventas as total_ventas', 'valor_total')
            ->withSum('pagos as total_pagado', 'valor')
            ->orderBy('fecha_hora', 'desc')
            ->whereNotNull('cliente')
            ->paginate(15);
        return view('ordendecompras.index', compact('ordenes'));
    }
	
	 public function pdf(OrdenDeCompra $orden)
    {
        $orden->load(['ventas.item', 'pagos']);

        $pdf = Pdf::loadView('ordendecompras.pdf', ['orden' => $orden])->setPaper('a4');

        // Descargar (o usa ->stream() para ver en el navegador)
        return $pdf->download("Orden-{$orden->id}.pdf");
    }

    public function sendEmail(Request $request, OrdenDeCompra $orden)
    {
        $data = $request->validate([
            'to'      => ['required', 'email'],
            'mensaje' => ['nullable', 'string', 'max:2000'],
        ]);

        $orden->load(['ventas.item', 'pagos']);

        $pdf = Pdf::loadView('ordendecompras.pdf', ['orden' => $orden])->setPaper('a4');

        Mail::to($data['to'])
            ->send(new OrdenPdfMail($orden, $pdf->output(), $data['mensaje'] ?? null));

        return back()->with('success', 'Enviamos la orden por correo correctamente.');
    }

    public function create()
    {
        return view('ordendecompras.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'fecha_hora'  => 'required|date',
            'responsable' => 'required|string|max:255',
            'cliente'     => 'required|string|max:255',
            'activa'      => 'required|boolean',
        ]);

        OrdenDeCompra::create($data);

        return redirect()->route('ordendecompras.index')
                         ->with('success', 'Orden de compra creada correctamente.');
    }

    public function show(OrdenDeCompra $ordenDeCompra)
    {
        $ordenDeCompra->loadMissing(['ventas.item', 'pagos']);

        return view('ordendecompras.view', [
            'orden' => $ordenDeCompra,
        ]);
    }

    public function edit(OrdenDeCompra $ordenDeCompra)
    {
        return view('ordendecompras.edit', compact('ordenDeCompra'));
    }

    public function update(Request $request, OrdenDeCompra $ordenDeCompra)
    {
        $data = $request->validate([
            'fecha_hora'  => 'required|date',
            'responsable' => 'required|string|max:255',
            'cliente'     => 'required|string|max:255',
            'activa'      => 'required|boolean',
        ]);

        $ordenDeCompra->update($data);

        return redirect()->route('ordendecompras.index')
                         ->with('success', 'Orden de compra actualizada correctamente.');
    }

    public function destroy(OrdenDeCompra $ordenDeCompra)
    {
        $ordenDeCompra->delete();

        return redirect()->route('orden_de_compras.index')
                         ->with('success', 'Orden de compra eliminada correctamente.');
    }
}
