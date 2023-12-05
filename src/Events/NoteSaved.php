<?php

namespace Outl1ne\NovaNotesField\Events;

use Illuminate\Queue\SerializesModels;
use Outl1ne\NovaNotesField\Models\Note;

class NoteSaved
{
    use SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public Note $note,
    ) {
    }
}
