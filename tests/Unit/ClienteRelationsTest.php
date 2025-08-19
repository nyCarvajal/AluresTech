<?php

namespace Tests\Unit;

use App\Models\Cliente;
use App\Models\Clase;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use PHPUnit\Framework\TestCase;

class ClienteRelationsTest extends TestCase
{
    /** @test */
    public function clases_relation_references_clase_model(): void
    {
        $cliente = new Cliente();

        $relation = $cliente->clases();

        $this->assertInstanceOf(BelongsToMany::class, $relation);
        $this->assertInstanceOf(Clase::class, $relation->getRelated());
    }
}
