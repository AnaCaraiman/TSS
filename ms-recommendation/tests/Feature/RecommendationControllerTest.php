<?php

namespace Tests\Feature;

use App\Http\Controllers\RecommendationController;
use App\Models\Action;
use App\Services\RecommendationService;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use ReflectionException;
use Tests\TestCase;

class RecommendationControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Route::post('/api/ms-recommendation/{id}', [RecommendationController::class, 'getMostPopularProducts']);
        Route::post('/api/ms-recommendation/personalised/{id}', [RecommendationController::class, 'getPersonalisedProducts']);
        Route::post('/api/ms-recommendation/action', [RecommendationController::class, 'addUserAction']);
        Route::get('/api/ms-recommendation/actions', [RecommendationController::class, 'getActions']);
    }

    #[Test]
    public function it_returns_most_popular_products()
    {
        Http::fake([
            'http://ms-ai-recommendation:8000/api/recommend' => Http::response(['product_ids' => [1, 2, 3]]),
        ]);

        $response = $this->postJson('/api/ms-recommendation/1');

        $response->assertStatus(200);
        $response->assertJson([
            'user_id' => 1,
            'recommended_products' => json_encode(['product_ids' => [1, 2, 3]]),
        ]);
    }

    #[Test]
    public function it_returns_500_if_ai_service_fails_for_popular()
    {
        Http::fake([
            'http://ms-ai-recommendation:8000/api/recommend' => Http::response(null, 500),
        ]);

        $response = $this->postJson('/api/ms-recommendation/1');

        $response->assertStatus(500);
        $response->assertJsonFragment(['error' => 'AI service unavailable']);
    }

    #[Test]
    public function it_returns_personalised_products()
    {
        Http::fake([
            'http://ms-ai-recommendation:8000/api/recommend/personalised' => Http::response(['product_ids' => [5, 6]]),
        ]);

        $response = $this->postJson('/api/ms-recommendation/personalised/2');

        $response->assertStatus(200);
        $response->assertJson([
            'user_id' => 2,
            'recommended_products' => json_encode(['product_ids' => [5, 6]]),
        ]);
    }

    #[Test]
    public function it_returns_500_if_ai_service_fails_for_personalised()
    {
        Http::fake([
            'http://ms-ai-recommendation:8000/api/recommend/personalised' => Http::response(null, 500),
        ]);

        $response = $this->postJson('/api/ms-recommendation/personalised/2');

        $response->assertStatus(500);
        $response->assertJsonFragment(['error' => 'AI service unavailable']);
    }

    #[Test]
    public function it_adds_a_user_action_successfully()
    {
        $mock = Mockery::mock(RecommendationService::class);
        $mock->shouldReceive('addAction')
            ->once()
            ->with(1, 101, 3)
            ->andReturn(new Action([
                'user_id' => 1,
                'product_id' => 101,
                'action_id' => 3,
            ]));

        $this->app->instance(RecommendationService::class, $mock);

        Route::post('/api/recommendation', function (Request $request) {
            return app(RecommendationController::class)->addUserAction($request);
        });

        $payload = [
            'user_id' => 1,
            'product_id' => 101,
            'action_id' => 3,
        ];

        $this->withoutMiddleware();
        $response = $this->postJson('/api/recommendation', $payload);
        dump($response->json());

        $response->assertStatus(201);
        $response->assertJsonFragment(['message' => 'Action added']);
    }

    /**
     * @throws ReflectionException
     */
    #[Test]
    public function it_returns_400_if_adding_user_action_fails()
    {
        $mock = Mockery::mock(RecommendationService::class);

        $this->beforeApplicationDestroyed(fn () => Mockery::close());
        $this->app->bind(RecommendationService::class, fn () => $mock);

        $mock->shouldReceive('addAction')
            ->once()
            ->andThrow(new Exception('Invalid action'));

        $this->app->instance(RecommendationService::class, $mock);

        $payload = [
            'user_id' => 1,
            'product_id' => 101,
            'action_id' => 999,
        ];

        $response = $this->postJson('/api/recommendation', $payload);

        $response->assertStatus(400);
        $response->assertJsonFragment(['error' => 'Invalid action']);
    }

    #[Test]
    public function it_returns_all_user_actions()
    {
        $mock = $this->mock(RecommendationService::class);
        $mock->shouldReceive('getActions')
            ->once()
            ->andReturn([
                ['user_id' => 1, 'product_id' => 101, 'action_id' => 2],
                ['user_id' => 2, 'product_id' => 102, 'action_id' => 3],
            ]);

        $response = $this->getJson('/api/recommendation');

        $response->assertStatus(201);
        $response->assertJsonFragment(['message' => 'Actions retrieved successfully']);
        $this->assertCount(2, $response->json('actions'));
    }
}
