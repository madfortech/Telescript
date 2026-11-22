<?php

namespace App\NativeComponents\Concerns;

use Native\Mobile\Attributes\Computed;
use Native\Mobile\Attributes\Poll;

trait HasTeleprompter
{
    public string $script = '';

    public bool $scrolling = false;

    public int $position = 0;

    protected function scriptChunks(): array
    {
        $words = preg_split('/\s+/u', trim($this->script), -1, PREG_SPLIT_NO_EMPTY);

        return array_map(fn ($c) => implode(' ', $c), array_chunk($words, 6));
    }

    #[Computed]
    public function visibleLines(): array
    {
        return array_slice($this->scriptChunks(), $this->position, 6);
    }

    #[Computed]
    public function scriptLines(): array
    {
        return $this->scriptChunks();
    }

    #[Poll(2000)]
    public function tickTeleprompter(): void
    {
        if (! $this->scrolling) {
            return;
        }

        if ($this->position < count($this->scriptChunks()) - 1) {
            $this->position++;
        } else {
            $this->scrolling = false;
        }
    }
}
