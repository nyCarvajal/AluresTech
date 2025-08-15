<?php

namespace Tests\Unit;

use App\Models\Alumno;
use App\Models\Clase;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use PHPUnit\Framework\TestCase;

class AlumnoRelationsTest extends TestCase
{
    /** @test */
    public function clases_relation_references_clase_model(): void
    {
        $alumno = new Alumno();

        $relation = $alumno->clases();

        $this->assertInstanceOf(BelongsToMany::class, $relation);
        $this->assertInstanceOf(Clase::class, $relation->getRelated());
    }
}
