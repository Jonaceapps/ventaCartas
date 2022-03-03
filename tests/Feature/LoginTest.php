<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LoginTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */

    public function test_usuarioExist()
    {
        $response = $this->postJson('/api/usuarios/login', ['nombre' => 'ajkfnakjfna knf', 'pass' => 'Jona1234']);

        $response
         -> assertStatus(200)
         -> assertJson([
             'status' => 0,
             'msg'    => 'Usuario no encontrado',
         ]);

    }

    public function test_PasswordError()
    {
        $response = $this->postJson('/api/usuarios/login', ['nombre' => 'Jonathan', 'pass' => 'hkjfhakjfakjnfa']);

        $response
         -> assertStatus(200)
         -> assertJson([
             'status' => 0,
             'msg'    => 'La contraseña no es correcta',
         ]);

    }

}
