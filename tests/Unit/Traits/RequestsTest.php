<?php

namespace Traits;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Kalexhaym\LaravelTelegramBot\Traits\Requests;
use Orchestra\Testbench\TestCase;

class RequestsTest extends TestCase
{
    /**
     * @var string
     */
    private string $testUrl = 'https://api.example.com';

    /**
     * @var string
     */
    private string $testMethod = '/test';

    protected function setUp(): void
    {
        parent::setUp();

        $this->app['config']->set('telegram.api.url', $this->testUrl);
    }

    /**
     * @throws ConnectionException
     *
     * @return void
     */
    public function testPost(): void
    {
        Http::fake([
            $this->testUrl.$this->testMethod => Http::response(['success' => true], 200),
        ]);

        $class = new TestClass();

        $response = $class->post($this->testMethod, ['key' => 'value']);

        $this->assertArrayHasKey('data', $response);
        $this->assertEquals(['success' => true], $response['data']);

        Http::assertSent(function (Request $request) {
            return $request->url() === $this->testUrl.$this->testMethod &&
                $request->method() === 'POST' &&
                $request->data() === ['key' => 'value'];
        });
    }

    /**
     * @throws ConnectionException
     *
     * @return void
     */
    public function testGet(): void
    {
        Http::fake([
            $this->testUrl.$this->testMethod.'?query=param' => Http::response(['data' => 'value'], 200),
        ]);

        $class = new TestClass();

        $response = $class->get($this->testMethod, ['query' => 'param']);

        $this->assertArrayHasKey('data', $response);
        $this->assertEquals(['data' => 'value'], $response['data']);

        Http::assertSent(function (Request $request) {
            return $request->url() === $this->testUrl.$this->testMethod.'?query=param' &&
                $request->method() === 'GET';
        });
    }

    /**
     * @throws ConnectionException
     *
     * @return void
     */
    public function testPut(): void
    {
        Http::fake([
            $this->testUrl.$this->testMethod => Http::response(['updated' => true], 200),
        ]);

        $class = new TestClass();

        $response = $class->put($this->testMethod, ['id' => 1]);

        $this->assertArrayHasKey('data', $response);
        $this->assertEquals(['updated' => true], $response['data']);

        Http::assertSent(function (Request $request) {
            return $request->url() === $this->testUrl.$this->testMethod &&
                $request->method() === 'PUT' &&
                $request->data() === ['id' => 1];
        });
    }

    /**
     * @throws ConnectionException
     *
     * @return void
     */
    public function testDelete(): void
    {
        Http::fake([
            $this->testUrl.$this->testMethod => Http::response(['deleted' => true], 200),
        ]);

        $class = new TestClass();

        $response = $class->delete($this->testMethod, ['id' => 1]);

        $this->assertArrayHasKey('data', $response);
        $this->assertEquals(['deleted' => true], $response['data']);

        Http::assertSent(function (Request $request) {
            return $request->url() === $this->testUrl.$this->testMethod &&
                $request->method() === 'DELETE' &&
                $request->data() === ['id' => 1];
        });
    }
}

class TestClass
{
    use Requests;
}
