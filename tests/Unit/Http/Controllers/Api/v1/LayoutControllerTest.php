<?php
namespace Tests\Unit\Http\Controllers\Api\v1;

use Gate;
use Config;
use App\Models\Definitions\Layout as Definition;
use App\Http\Controllers\Api\v1\LayoutController;

class LayoutControllerTest extends ApiControllerTestCase {

    /**
     * @test
     * @group authentication
     */
    public function definitions_WhenUnauthenticated_Returns403(){
        $response = $this->action('GET', LayoutController::class . '@definitions');
        $response->assertStatus(401);
    }

    /**
     * @test
     * @group authorization
     */
    public function definitions_WhenAuthenticated_ChecksAuthorization(){
        Gate::shouldReceive('authorize')->with('index', Definition::class)->once();

        $this->authenticated();
        $this->action('GET', LayoutController::class . '@definitions');
    }

    /**
     * @test
     * @group authorization
     */
    public function definitions_WhenAuthenticatedAndUnauthorized_Returns403(){
        $this->authenticatedAndUnauthorized();

        $response = $this->action('GET', LayoutController::class . '@definitions');
        $response->assertStatus(403);
    }

    /**
     * @test
     */
    public function definitions_WhenAuthorized_Returns200(){
        $this->authenticatedAndAuthorized();

        $response = $this->action('GET', LayoutController::class . '@definitions');
        $response->assertStatus(200);
    }



    /**
     * @test
     * @group authentication
     */
    public function definition_WhenUnauthenticated_Returns403(){
        $response = $this->action('GET', LayoutController::class . '@definition', 'test-layout');
        $response->assertStatus(401);
    }

    /**
     * @test
     */
    public function definition_WhenAuthenticatedButNotFound_Returns404(){
        $this->authenticatedAndAuthorized();

        $response = $this->action('GET', LayoutController::class . '@definition', 'foobar');
        $response->assertStatus(404);
    }

    /**
     * @test
     * @group authorization
     */
    public function definition_WhenAuthenticatedAndFound_ChecksAuthorization(){
        Gate::shouldReceive('authorize')->with('read', Definition::class)->once();

        $this->authenticated();
        $this->action('GET', LayoutController::class . '@definition', 'test-layout');
    }

    /**
     * @test
     * @group authorization
     */
    public function definition_WhenAuthenticatedAndUnauthorized_Returns403(){
        $this->authenticatedAndUnauthorized();

        $response = $this->action('GET', LayoutController::class . '@definition', 'test-layout');
        $response->assertStatus(403);
    }

    /**
     * @test
     */
    public function definition_WhenAuthorized_Returns200(){
        $this->authenticatedAndAuthorized();

        $response = $this->action('GET', LayoutController::class . '@definition', 'test-layout');
        $response->assertStatus(200);
    }

    /**
     * @test
     */
    public function definition_WhenAuthorized_ReturnsJson(){
        $this->authenticatedAndAuthorized();

        $response = $this->action('GET', LayoutController::class . '@definition', 'test-layout');

        $json = $response->json();
        $this->assertArrayHasKey('data', $json);
        $this->assertEquals('test-layout', $json['data']['name']);
    }



    /**
     * @test
     * @group authentication
     */
    public function regions_WhenUnauthenticated_Returns403(){
        $response = $this->action('GET', LayoutController::class . '@regions', 'test-layout');
        $response->assertStatus(401);
    }

    /**
     * @test
     */
    public function regions_WhenAuthenticatedButNotFound_Returns404(){
        $this->authenticatedAndAuthorized();

        $response = $this->action('GET', LayoutController::class . '@regions', 'foobar');
        $response->assertStatus(404);
    }

    /**
     * @test
     * @group authorization
     */
    public function regions_WhenAuthenticatedAndFound_ChecksAuthorization(){
        Gate::shouldReceive('authorize')->with('read', Definition::class)->once();

        $this->authenticated();
        $this->action('GET', LayoutController::class . '@regions', 'test-layout');
    }

    /**
     * @test
     * @group authorization
     */
    public function regions_WhenAuthenticatedAndUnauthorized_Returns403(){
        $this->authenticatedAndUnauthorized();

        $response = $this->action('GET', LayoutController::class . '@regions', 'test-layout');
        $response->assertStatus(403);
    }

    /**
     * @test
     */
    public function regions_WhenAuthorized_Returns200(){
        $this->authenticatedAndAuthorized();

        $response = $this->action('GET', LayoutController::class . '@regions', 'test-layout');
        $response->assertStatus(200);
    }

    /**
     * @test
     */
    public function regions_WhenAuthorized_ReturnsJson(){
        $this->authenticatedAndAuthorized();

        $response = $this->action('GET', LayoutController::class . '@regions', 'test-layout');

        $json = $response->json();
        $this->assertArrayHasKey('data', $json);
        $this->assertCount(1, $json['data']);
        $this->assertEquals('test-region', $json['data'][0]['name']);
    }

}

