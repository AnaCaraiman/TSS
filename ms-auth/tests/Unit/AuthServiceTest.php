<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\AuthService;
use App\Repositories\AuthRepository;
use App\Repositories\TokenRepository;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Symfony\Component\HttpFoundation\Cookie;
use Mockery;


class AuthServiceTest extends TestCase
{
    use WithFaker;

    protected $authRepositoryMock;
    protected $tokenRepositoryMock;
    protected $authService;

    protected function setUp(): void
    {
        parent::setUp();

        // Mock the dependencies
        $this->authRepositoryMock = Mockery::mock(AuthRepository::class);
        $this->tokenRepositoryMock = Mockery::mock(TokenRepository::class);

        // Create the service with mocked repositories
        $this->authService = new AuthService(
            $this->authRepositoryMock,
            $this->tokenRepositoryMock
        );
    }

    /// Structural Testing

    public function test_get_tokens_returns_tokens()
    {
        $user = new User();
        $expectedTokens = ['access_token' => 'abc', 'refresh_token' => 'xyz'];

        $this->tokenRepositoryMock
            ->shouldReceive('generateTokens')
            ->with($user)
            ->once()
            ->andReturn($expectedTokens);

        $tokens = $this->authService->getTokens($user);

        $this->assertEquals($expectedTokens, $tokens);
    }

    public function test_get_access_token_returns_token()
    {
        $user = new User();
        $expectedToken = 'access_token_123';

        $this->tokenRepositoryMock
            ->shouldReceive('generateAccessToken')
            ->with($user)
            ->once()
            ->andReturn($expectedToken);

        $token = $this->authService->getAccessToken($user);

        $this->assertEquals($expectedToken, $token);
    }

    public function test_get_cookie_returns_cookie()
    {
        $tokens = ['access_token' => 'abc', 'refresh_token' => 'xyz'];
        $fakeCookie = new Cookie('refresh_token', 'xyz'); // create a real Cookie object


        $this->tokenRepositoryMock
            ->shouldReceive('generateCookie')
            ->with($tokens)
            ->once()
            ->andReturn($fakeCookie);

        $cookie = $this->authService->getCookie($tokens);

        $this->assertEquals($fakeCookie, $cookie);
    }

    public function test_register_user_returns_user()
    {
        $data = ['name' => 'Victor', 'email' => 'victor@example.com'];
        $user = new User($data);

        $this->authRepositoryMock
            ->shouldReceive('createUser')
            ->with($data)
            ->once()
            ->andReturn($user);

        $createdUser = $this->authService->registerUser($data);

        $this->assertEquals($user->name, $createdUser->name);
        $this->assertEquals($user->email, $createdUser->email);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /// Functional tests
    
}