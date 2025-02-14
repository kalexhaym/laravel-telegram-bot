<?php

namespace Kalexhaym\LaravelTelegramBot;

class Poll
{
    /**
     * @var string
     */
    private string $question;

    /**
     * @var string
     */
    private string $options;

    /**
     * @var string
     */
    private string $type = 'regular';

    /**
     * @var bool
     */
    private bool $allows_multiple_answers = false;

    /**
     * @var int
     */
    private int $correct_option_id = 0;

    /**
     * @var string
     */
    private string $explanation;

    /**
     * @var int
     */
    private int $open_period;

    /**
     * @var bool
     */
    private bool $is_anonymous = true;

    /**
     * @var bool
     */
    private bool $is_closed = false;

    /**
     * @param string $question - Poll question, 1-300 characters
     * @param array  $options  - List of 2-10 answer options
     */
    public function __construct(string $question, array $options)
    {
        $this->question = $question;
        $this->options = json_encode($options);
    }

    /**
     * @return $this
     */
    public function notAnonymous(): Poll
    {
        $this->is_anonymous = false;

        return $this;
    }

    /**
     * @param int         $correct_option_id - 0-based identifier of the correct answer option, required for polls in quiz mode
     * @param string|null $explanation       - Text that is shown when a user chooses an incorrect answer or taps on the lamp icon in a quiz-style poll, 0-200 characters with at most 2 line feeds after entities parsing
     *
     * @return $this
     */
    public function quiz(int $correct_option_id, ?string $explanation = null): Poll
    {
        $this->type = 'quiz';
        $this->correct_option_id = $correct_option_id;
        $this->explanation = $explanation;

        return $this;
    }

    /**
     * @return $this
     */
    public function allowsMultipleAnswers(): Poll
    {
        $this->allows_multiple_answers = true;

        return $this;
    }

    /**
     * @param int $open_period - Amount of time in seconds the poll will be active after creation, 5-600.
     *
     * @return $this
     */
    public function openPeriod(int $open_period): Poll
    {
        $this->open_period = true;

        return $this;
    }

    /**
     * @return $this
     */
    public function isClosed(): Poll
    {
        $this->is_closed = true;

        return $this;
    }

    /**
     * @return array
     */
    public function get(): array
    {
        $data = [
            'question'                => $this->question,
            'options'                 => $this->options,
            'type'                    => $this->type,
            'allows_multiple_answers' => $this->allows_multiple_answers,
            'correct_option_id'       => $this->correct_option_id,
            'is_anonymous'            => $this->is_anonymous,
            'is_closed'               => $this->is_closed,
        ];

        if (! empty($this->explanation)) {
            $data['explanation'] = $this->explanation;
        }

        if (! empty($this->open_period)) {
            $data['open_period'] = $this->open_period;
        }

        return $data;
    }
}
