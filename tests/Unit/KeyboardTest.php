<?php

declare(strict_types=1);

namespace Kalexhaym\LaravelTelegramBot\Tests\Unit;

use Illuminate\Foundation\Testing\TestCase;
use Kalexhaym\LaravelTelegramBot\Keyboard;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;

/**
 * Class AlertTest.
 */
class KeyboardTest extends TestCase
{
    /**
     * @throws ReflectionException
     */
    protected static function getMethod($name): ReflectionMethod
    {
        $class = new ReflectionClass(Keyboard::class);
        return $class->getMethod($name);
    }

    /**
     * @return void
     */
    public function testResizable(): void
    {
        $keyboard = new Keyboard();
        $keyboard->resizable();
        $this->assertSame(true, $keyboard->get()['resize_keyboard']);

        $keyboard = new Keyboard();
        $this->assertSame(false, key_exists('resize_keyboard', $keyboard->get()));
    }

    /**
     * @return void
     */
    public function testInline(): void
    {
        $keyboard = new Keyboard();
        $keyboard->inline();
        $this->assertSame(true, key_exists('inline_keyboard', $keyboard->get()));
        $this->assertSame(false, key_exists('keyboard', $keyboard->get()));

        $keyboard = new Keyboard();
        $this->assertSame(true, key_exists('keyboard', $keyboard->get()));
        $this->assertSame(false, key_exists('inline_keyboard', $keyboard->get()));
    }

    /**
     * @return void
     */
    public function testOneTimeKeyboard(): void
    {
        $keyboard = new Keyboard();
        $keyboard->oneTimeKeyboard();
        $this->assertSame(true, $keyboard->get()['one_time_keyboard']);

        $keyboard = new Keyboard();
        $this->assertSame(false, key_exists('one_time_keyboard', $keyboard->get()));
    }

    /**
     * @return void
     */
    public function testSelective(): void
    {
        $keyboard = new Keyboard();
        $keyboard->selective();
        $this->assertSame(true, $keyboard->get()['selective']);

        $keyboard = new Keyboard();
        $this->assertSame(false, key_exists('selective', $keyboard->get()));
    }

    /**
     * @return void
     */
    public function testButtonsInRow(): void
    {
        $keyboard = new Keyboard();
        foreach(range(1, 10) as $index) {
            $keyboard->addButton((string) $index, (string) $index);
        }
        $this->assertSame(3, count($keyboard->get()['keyboard']));
        $this->assertSame(4, count($keyboard->get()['keyboard'][0]));

        $keyboard = new Keyboard();
        $keyboard->buttonsInRow(5);
        foreach(range(1, 10) as $index) {
            $keyboard->addButton((string) $index, (string) $index);
        }
        $this->assertSame(2, count($keyboard->get()['keyboard']));
        $this->assertSame(5, count($keyboard->get()['keyboard'][0]));

        $keyboard = new Keyboard();
        $keyboard->buttonsInRow(5);
        foreach(range(1, 10) as $index) {
            $keyboard->addLink((string) $index, (string) $index);
        }
        $this->assertSame(2, count($keyboard->get()['keyboard']));
        $this->assertSame(5, count($keyboard->get()['keyboard'][0]));
    }

    /**
     * @return void
     */
    public function testInputPlaceholder(): void
    {
        $keyboard = new Keyboard();
        $keyboard->inputPlaceholder('inputPlaceholderString');
        $this->assertSame('inputPlaceholderString', $keyboard->get()['input_field_placeholder']);

        $keyboard = new Keyboard();
        $this->assertSame(false, key_exists('input_field_placeholder', $keyboard->get()));
    }

    /**
     * @return void
     */
    public function testAddButton(): void
    {
        $keyboard = new Keyboard();
        $keyboard->addButton('testButton', 'testButtonCallback', ['param' => 'value']);
        $keyboard->addButton('testButton2', 'testButtonCallback2', ['param2' => 'value2']);
        $keyboard->addButton('testButton3');
        $this->assertSame([
            [
                'text' => 'testButton',
                'callback_data' => 'callback=testButtonCallback param=value',
            ],
            [
                'text' => 'testButton2',
                'callback_data' => 'callback=testButtonCallback2 param2=value2',
            ],
            [
                'text' => 'testButton3',
            ],
        ], $keyboard->get()['keyboard'][0]);
    }

    /**
     * @return void
     */
    public function testAddLink(): void
    {
        $keyboard = new Keyboard();
        $keyboard->addLink('testLink', 'testLinkHref');
        $keyboard->addLink('testLink2', 'testLinkHref2');
        $keyboard->addLink('testLink3');
        $this->assertSame([
            [
                'text' => 'testLink',
                'url' => 'testLinkHref',
            ],
            [
                'text' => 'testLink2',
                'url' => 'testLinkHref2',
            ],
            [
                'text' => 'testLink3',
            ],
        ], $keyboard->get()['keyboard'][0]);
    }

    /**
     * @return void
     */
    public function testGet(): void
    {
        $keyboard = new Keyboard();

        $keyboard->resizable();
        $keyboard->inline();
        $keyboard->oneTimeKeyboard();
        $keyboard->selective();
        $keyboard->buttonsInRow(5);
        $keyboard->inputPlaceholder('inputPlaceholderString');

        $keyboard->addButton('testButton', 'testButtonCallback', ['param' => 'value']);
        $keyboard->addButton('testButton2', 'testButtonCallback2', ['param2' => 'value2']);
        $keyboard->addButton('testButton3');

        $keyboard->addLink('testLink', 'testLinkHref');
        $keyboard->addLink('testLink2', 'testLinkHref2');
        $keyboard->addLink('testLink3');

        $this->assertSame([
            'inline_keyboard' => [
                0 => [
                    [
                        'text' => 'testButton',
                        'callback_data' => 'callback=testButtonCallback param=value',
                    ],
                    [
                        'text' => 'testButton2',
                        'callback_data' => 'callback=testButtonCallback2 param2=value2',
                    ],
                    [
                        'text' => 'testButton3',
                    ],
                    [
                        'text' => 'testLink',
                        'url' => 'testLinkHref',
                    ],
                    [
                        'text' => 'testLink2',
                        'url' => 'testLinkHref2',
                    ],
                ],
                1 => [
                    [
                        'text' => 'testLink3',
                    ],
                ]
            ],
            'resize_keyboard' => true,
            'one_time_keyboard' => true,
            'selective' => true,
            'input_field_placeholder' => 'inputPlaceholderString',
        ], $keyboard->get());
    }
}
