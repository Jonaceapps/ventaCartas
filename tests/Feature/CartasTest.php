<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CartasTest extends TestCase
{
    
    use DatabaseTransactions;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_CartaVacia()
    {
        $response = $this->putJson('/api/usuarios/altaCarta?api_token=$2y$04$tPO/RaMCHBHSMhBj1gUkt.a3DEZp.ZvwmpRNAajzjLH9g1eQX.Lkq', ['nombre' => '', 'descripcion' => '', 'coleccion' => '']);

        $response
        -> assertStatus(200)
        -> assertJson([
            'status' => 0,
            'msg'    => 'Coleccion o cartas vacias, intentalo de nuevo',
        ]);
    }

    public function test_ColeccionError()
    {
        $response = $this->putJson('/api/usuarios/altaCarta?api_token=$2y$04$tPO/RaMCHBHSMhBj1gUkt.a3DEZp.ZvwmpRNAajzjLH9g1eQX.Lkq', ['nombre' => 'Elfo oscuro v2', 'descripcion' => 'Elfo nocturno de color verde', 'coleccion' => [99]]);

        $response
        -> assertStatus(200)
        -> assertJson([
            'status' => 0,
            'msg'    => 'Alguna coleccion asocida no es valida o no existe, intentalo de nuevo',
        ]);
    }

    public function test_cartaCreada()
    {
        $response = $this->putJson('/api/usuarios/altaCarta?api_token=$2y$04$tPO/RaMCHBHSMhBj1gUkt.a3DEZp.ZvwmpRNAajzjLH9g1eQX.Lkq',
        ['nombre' => 'Elfo oscuro v2', 'descripcion' => 'Elfo nocturno de color verde', 'coleccion' => [1]]);

        $response
        -> assertStatus(200)
        -> assertJson([
            'status' => 1,
            'msg'    => 'Carta Guardada',
        ]);
    }
}
