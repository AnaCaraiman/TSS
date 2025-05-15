<?php

namespace Tests\Unit;

use App\Models\Action;
use App\Repositories\RecommendationRepository;
use App\Services\RecommendationService;
use Mockery;
use Tests\TestCase;

class RecommendationServiceTest extends TestCase
{
    protected $recommendationRepository;
    protected $recommendationService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->recommendationRepository = Mockery::mock(RecommendationRepository::class);
        $this->recommendationService = new RecommendationService($this->recommendationRepository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function testAddAction()
    {
        $userId = 1;
        $productId = 101;
        $actionId = 5;
        $action = new Action([
            'user_id' => $userId,
            'product_id' => $productId,
            'action_id' => $actionId,
        ]);

        $this->recommendationRepository
            ->shouldReceive('createAction')
            ->with($userId, $productId, $actionId)
            ->once()
            ->andReturn($action);

        $result = $this->recommendationService->addAction($userId, $productId, $actionId);

        $this->assertEquals($action, $result);
    }

    public function testGetActions()
    {
        $actions = [
            ['user_id' => 1, 'product_id' => 100, 'action_id' => 1],
            ['user_id' => 2, 'product_id' => 200, 'action_id' => 2],
        ];

        $this->recommendationRepository
            ->shouldReceive('getActions')
            ->once()
            ->andReturn($actions);

        $result = $this->recommendationService->getActions();

        $this->assertEquals($actions, $result);
    }
}
