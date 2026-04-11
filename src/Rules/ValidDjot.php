<?php

declare(strict_types=1);

namespace PhpCollective\LaravelDjot\Rules;

use Closure;
use Djot\DjotConverter;
use Djot\Exception\ParseException;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidDjot implements ValidationRule
{
    private DjotConverter $converter;

    /**
     * @param bool $strict If true, parse warnings are also treated as errors
     * @param string|null $message Custom error message template ({error} placeholder is replaced)
     */
    public function __construct(
        private bool $strict = false,
        private ?string $message = null,
    ) {
        $this->converter = new DjotConverter(
            warnings: true,
            strict: false,
        );
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($value === null || $value === '') {
            return;
        }

        if (!is_string($value)) {
            $fail($this->formatMessage('value must be a string'));

            return;
        }

        try {
            $this->converter->convert($value);

            if ($this->strict && $this->converter->hasWarnings()) {
                $warnings = $this->converter->getWarnings();
                $firstWarning = $warnings[0] ?? null;
                $errorMessage = $firstWarning?->getMessage() ?? 'Parse warnings detected';
                $fail($this->formatMessage($errorMessage));
            }
        } catch (ParseException $e) {
            $fail($this->formatMessage($e->getMessage()));
        }
    }

    private function formatMessage(string $error): string
    {
        $template = $this->message ?? 'The :attribute is not valid Djot markup: {error}';

        return str_replace('{error}', $error, $template);
    }
}
