<?php

namespace Unit;

use Illuminate\Support\ServiceProvider;
use Kalexhaym\LaravelTelegramBot\TelegramServiceProvider;
use Orchestra\Testbench\TestCase;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;

class TelegramServiceProviderTest extends TestCase
{
    /**
     * @throws ReflectionException
     */
    protected static function getMethod($name): ReflectionMethod
    {
        $class = new ReflectionClass(TelegramServiceProvider::class);

        return $class->getMethod($name);
    }

    /**
     * @return void
     */
    public function testInstance(): void
    {
        $provider = new TelegramServiceProvider($this->app);
        $this->assertInstanceOf(ServiceProvider::class, $provider);
    }

    /**
     * @return void
     */
    public function testBoot(): void
    {
        $provider = new TelegramServiceProvider($this->app);
        $provider->boot();
        $this->assertTrue(true);
    }

    /**
     * @return void
     */
    public function testRegister(): void
    {
        $provider = new TelegramServiceProvider($this->app);
        $provider->register();
        $this->assertTrue(true);
    }

    /**
     * @return void
     *
     * @throws ReflectionException
     */
    public function testConfigure(): void
    {
        $method = self::getMethod('configure');
        $class = new TelegramServiceProvider($this->app);
        $method->invokeArgs($class, []);
        $this->assertTrue(true);
    }

    /**
     * @return void
     *
     * @throws ReflectionException
     */
    public function testRegisterRoutes(): void
    {
        $method = self::getMethod('registerRoutes');
        $class = new TelegramServiceProvider($this->app);
        $method->invokeArgs($class, []);
        $this->assertTrue(true);
    }

    /**
     * @return void
     *
     * @throws ReflectionException
     */
    public function testRegisterCommands(): void
    {
        $method = self::getMethod('registerCommands');
        $class = new TelegramServiceProvider($this->app);
        $method->invokeArgs($class, []);
        $this->assertTrue(true);
    }

    /**
     * @return void
     *
     * @throws ReflectionException
     */
    public function testOfferPublishing(): void
    {
        $method = self::getMethod('offerPublishing');
        $class = new TelegramServiceProvider($this->app);
        $method->invokeArgs($class, []);
        $this->assertTrue(true);
    }
}
