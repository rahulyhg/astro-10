<?php
namespace Tests\Unit\Http\Controllers\Api\v1;

use Gate;
use Mockery;
use App\Models\Page;
use App\Models\Block;
use App\Models\Route;
use App\Models\Redirect;
use App\Http\Controllers\Api\v1\RouteController;
use App\Http\Transformers\Api\v1\PageTransformer;
use Illuminate\Auth\Access\AuthorizationException;

class RouteControllerTest extends ApiControllerTestCase {

    /**
     * @test
     * @group authentication
     */
    public function resolve_WhenUnauthenticated_Returns401(){
        $route = factory(Route::class)->states([ 'withPage', 'isRoot' ])->create();
        $route->page->publish(new PageTransformer);

        $page = $route->page;
        $page->publish(new PageTransformer);

        $response = $this->action('GET', RouteController::class . '@resolve', [ 'path' => $route->path ]);
        $response->assertStatus(401);
    }

    /**
     * @test
     * @group authorization
     *
     * Resolves via a Route model
     */
    public function resolve_WhenAuthenticatedAndRouteFound_ChecksAuthorization(){
        $route = factory(Route::class)->states([ 'withPage', 'isRoot' ])->create();
        $route->page->publish(new PageTransformer);

        $page = $route->page;
        $page->publish(new PageTransformer);

        Gate::shouldReceive('allows')->with('read', Mockery::type(Route::class))->once();

        $this->authenticated();
        $response = $this->action('GET', RouteController::class . '@resolve', [ 'path' => $route->path ]);
    }

    /**
     * @test
     * @group authorization
     *
     * Resolves via a Route model
     */
    public function resolve_WhenAuthenticatedAndRouteFoundButUnauthorized_Returns404(){
        $route = factory(Route::class)->states([ 'withPage', 'isRoot' ])->create();
        $route->page->publish(new PageTransformer);

        $page = $route->page;
        $page->publish(new PageTransformer);

        $this->authenticatedAndUnauthorized();
        $response = $this->action('GET', RouteController::class . '@resolve', [ 'path' => $route->path ]);
        $response->assertStatus(404);
    }

    /**
     * @test
     *
     * Resolves via a Route model
     */
    public function resolve_WhenAuthorizedAndRouteFoundButNotPublished_Returns200(){
        $route = factory(Route::class)->states([ 'withPage', 'isRoot' ])->create();
        $route->page->publish(new PageTransformer);

        $page = $route->page;
        $page->publish(new PageTransformer);

        $this->authenticatedAndAuthorized();
        $response = $this->action('GET', RouteController::class . '@resolve', [ 'path' => $route->path ]);

        $response->assertStatus(200);
    }

    /**
     * @test
     *
     * Resolves via a Route model
     */
    public function resolve_WhenAuthorizedAndRouteFoundButNotPublished_ReturnsJson(){
        $route = factory(Route::class)->states([ 'withPage', 'isRoot' ])->create();
        $route->page->publish(new PageTransformer);

        $this->authenticatedAndAuthorized();
        $response = $this->action('GET', RouteController::class . '@resolve', [ 'path' => $route->path ]);

        $json = $response->json();
        $this->assertArrayHasKey('data', $json);
        $this->assertEquals($route->page->id, $json['data']['id']);
    }

    /**
     * @test
     *
     * Resolves via a Route model
     */
    public function resolve_WhenAuthorizedAndRouteFoundAndPublished_Returns200(){
        $route = factory(Route::class)->states([ 'withPage', 'isRoot' ])->create();
        $route->page->publish(new PageTransformer);

        $this->authenticatedAndAuthorized();
        $response = $this->action('GET', RouteController::class . '@resolve', [ 'path' => $route->path ]);

        $response->assertStatus(200);
    }

    /**
     * @test
     *
     * Resolves via a Route model
     */
    public function resolve_WhenAuthorizedAndRouteFoundAndPublished_ReturnsJson(){
        $route = factory(Route::class)->states([ 'withPage', 'isRoot' ])->create();
        $route->page->publish(new PageTransformer);

        $this->authenticatedAndAuthorized();
        $response = $this->action('GET', RouteController::class . '@resolve', [ 'path' => $route->path ]);

        $json = $response->json();
        $this->assertArrayHasKey('data', $json);
        $this->assertEquals($route->page->id, $json['data']['id']);
    }

    /**
     * @test
     *
     * Resolves via a Route model
     */
    public function resolve_WhenAuthorizedAndRouteFoundAndPublished_IncludesActiveRouteInJson(){
        $active = factory(Route::class)->states([ 'withPage', 'isRoot' ])->create();
        $active->page->publish(new PageTransformer);

        sleep(1);

        $draft = factory(Route::class)->create(array_except(attrs_for($active), [ 'id', 'is_active' ]));

        $this->authenticatedAndAuthorized();
        $response = $this->action('GET', RouteController::class . '@resolve', [ 'path' => $active->path ]);

        $json = $response->json();
        $this->assertArrayHasKey('data', $json);
        $this->assertArrayHasKey('active_route', $json['data']);
        $this->assertEquals($active->slug, $json['data']['active_route']['slug']);
    }

    /**
     * @test
     *
     * Resolves via a Route model
     */
    public function resolve_WhenAuthorizedAndRouteFoundAndPublished_IncludesPageBlocksByRegionInJson(){
        $route = factory(Route::class)->states([ 'withPage', 'isRoot' ])->create();
        $block = factory(Block::class)->create([ 'page_id' => $route->page->getKey() ]);

        $route->page->publish(new PageTransformer);

        $this->authenticatedAndAuthorized();
        $response = $this->action('GET', RouteController::class . '@resolve', [ 'path' => $route->path ]);

        $json = $response->json();
        $this->assertArrayHasKey('data', $json);
        $this->assertArrayHasKey('blocks', $json['data']);
        $this->assertArrayHasKey('test-region', $json['data']['blocks']);
        $this->assertCount(1, $json['data']['blocks']);
    }

    /**
     * @test
     * @group authorization
     *
     * Resolves via a Redirect model
     */
    public function resolve_WhenAuthenticatedAndRedirectFound_ChecksAuthorization(){
        $page = factory(Page::class)->create();
        $page->publish(new PageTransformer);

        $redirect = new Redirect([ 'path' => '/foobar', 'page_id' => $page->getKey() ]);
        $redirect->save();

        Gate::shouldReceive('allows')->with('read', Mockery::type(Redirect::class))->once();

        $this->authenticated();
        $response = $this->action('GET', RouteController::class . '@resolve', [ 'path' => $redirect->path ]);
    }

    /**
     * @test
     * @group authorization
     *
     * Resolves via a Redirect model
     */
    public function resolve_WhenAuthenticatedAndRedirectFoundButUnauthorized_Returns404(){
        $page = factory(Page::class)->create();
        $page->publish(new PageTransformer);

        $redirect = new Redirect([ 'path' => '/foobar', 'page_id' => $page->getKey() ]);
        $redirect->save();

        $this->authenticatedAndUnauthorized();
        $response = $this->action('GET', RouteController::class . '@resolve', [ 'path' => $redirect->path ]);
        $response->assertStatus(404);
    }

    /**
     * @test
     *
     * Resolves via a Redirect model
     */
    public function resolve_WhenAuthorizedAndRedirectFoundButNotPublished_Returns200(){
        $page = factory(Page::class)->create();

        $redirect = new Redirect([ 'path' => '/foobar', 'page_id' => $page->getKey() ]);
        $redirect->save();

        $this->authenticatedAndAuthorized();
        $response = $this->action('GET', RouteController::class . '@resolve', [ 'path' => $redirect->path ]);

        $response->assertStatus(200);
    }

    /**
     * @test
     *
     * Resolves via a Redirect model
     */
    public function resolve_WhenAuthorizedAndRedirectFoundButNotPublished_ReturnsJson(){
        $page = factory(Page::class)->create();

        $redirect = new Redirect([ 'path' => '/foobar', 'page_id' => $page->getKey() ]);
        $redirect->save();

        $this->authenticatedAndAuthorized();
        $response = $this->action('GET', RouteController::class . '@resolve', [ 'path' => $redirect->path ]);

        $json = $response->json();
        $this->assertArrayHasKey('data', $json);
        $this->assertEquals($redirect->page->id, $json['data']['id']);
    }

    /**
     * @test
     *
     * Resolves via a Redirect model
     */
    public function resolve_WhenAuthorizedAndRedirectFoundAndPublished_Returns200(){
        $page = factory(Page::class)->create();
        $page->publish(new PageTransformer);

        $redirect = new Redirect([ 'path' => '/foobar', 'page_id' => $page->getKey() ]);
        $redirect->save();

        $this->authenticatedAndAuthorized();
        $response = $this->action('GET', RouteController::class . '@resolve', [ 'path' => $redirect->path ]);

        $response->assertStatus(200);
    }

    /**
     * @test
     *
     * Resolves via a Redirect model
     */
    public function resolve_WhenAuthorizedAndRedirectFoundAndPublished_ReturnsJson(){
        $page = factory(Page::class)->create();
        $page->publish(new PageTransformer);

        $redirect = new Redirect([ 'path' => '/foobar', 'page_id' => $page->getKey() ]);
        $redirect->save();

        $this->authenticatedAndAuthorized();
        $response = $this->action('GET', RouteController::class . '@resolve', [ 'path' => $redirect->path ]);

        $json = $response->json();
        $this->assertArrayHasKey('data', $json);
        $this->assertEquals($redirect->page->id, $json['data']['id']);
    }

    /**
     * @test
     *
     * Resolves via a Redirect model
     */
    public function resolve_WhenAuthorizedAndRedirectFoundAndPublished_IncludesPageBlocksByRegionInJson(){
        $page = factory(Page::class)->create();
        $block = factory(Block::class)->create([ 'page_id' => $page->getKey() ]);

        $page->publish(new PageTransformer);

        $redirect = new Redirect([ 'path' => '/foobar', 'page_id' => $page->getKey() ]);
        $redirect->save();

        $this->authenticatedAndAuthorized();
        $response = $this->action('GET', RouteController::class . '@resolve', [ 'path' => $redirect->path ]);

        $json = $response->json();
        $this->assertArrayHasKey('data', $json);
        $this->assertArrayHasKey('blocks', $json['data']);
        $this->assertArrayHasKey('test-region', $json['data']['blocks']);
        $this->assertCount(1, $json['data']['blocks']);
    }


    /**
     * @test
     */
    public function resolve_WhenBothRouteAndRedirectAreNotFound_Returns404(){
        $this->authenticatedAndAuthorized();

        $response = $this->action('GET', RouteController::class . '@resolve', [ 'path' => '/foobar' ]);
        $response->assertStatus(404);
    }


}
