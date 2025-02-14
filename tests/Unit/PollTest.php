<?php

declare(strict_types=1);

namespace Kalexhaym\LaravelTelegramBot\Tests\Unit;

use Kalexhaym\LaravelTelegramBot\Poll;
use Orchestra\Testbench\TestCase;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;

/**
 * Class AlertTest.
 */
class PollTest extends TestCase
{
    /**
     * @throws ReflectionException
     */
    protected static function getMethod($name): ReflectionMethod
    {
        $class = new ReflectionClass(Poll::class);

        return $class->getMethod($name);
    }

    /**
     * @return void
     */
    public function testNotAnonymous(): void
    {
        $poll = new Poll('tests', ['1', '2']);
        $this->assertSame(true, $poll->get()['is_anonymous']);

        $poll = new Poll('tests', ['1', '2']);
        $poll->notAnonymous();
        $this->assertSame(false, $poll->get()['is_anonymous']);
    }

    /**
     * @return void
     */
    public function testQuiz(): void
    {
        $poll = new Poll('tests', ['1', '2']);
        $poll->quiz(1, 'explanation');
        $this->assertSame('quiz', $poll->get()['type']);
        $this->assertSame(1, $poll->get()['correct_option_id']);
        $this->assertSame('explanation', $poll->get()['explanation']);
    }

    /**
     * @return void
     */
    public function testAllowsMultipleAnswers(): void
    {
        $poll = new Poll('tests', ['1', '2']);
        $this->assertSame(false, $poll->get()['allows_multiple_answers']);

        $poll = new Poll('tests', ['1', '2']);
        $poll->allowsMultipleAnswers();
        $this->assertSame(true, $poll->get()['allows_multiple_answers']);
    }

    /**
     * @return void
     */
    public function testOpenPeriod(): void
    {
        $poll = new Poll('tests', ['1', '2']);
        $poll->openPeriod(5);
        $this->assertSame(5, $poll->get()['open_period']);
    }

    /**
     * @return void
     */
    public function testIsClosed(): void
    {
        $poll = new Poll('tests', ['1', '2']);
        $this->assertSame(false, $poll->get()['is_closed']);

        $poll = new Poll('tests', ['1', '2']);
        $poll->isClosed();
        $this->assertSame(true, $poll->get()['is_closed']);
    }

    /**
     * @return void
     */
    public function testGet(): void
    {
        $poll = new Poll('tests', ['1', '2']);
        $this->assertSame([
            'question'                => 'tests',
            'options'                 => json_encode(['1', '2']),
            'type'                    => 'regular',
            'allows_multiple_answers' => false,
            'correct_option_id'       => 0,
            'is_anonymous'            => true,
            'is_closed'               => false,
        ], $poll->get());
    }
}
