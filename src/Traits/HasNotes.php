<?php

namespace Outl1ne\NovaNotesField\Traits;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Outl1ne\NovaNotesField\Models\Note;
use Outl1ne\NovaNotesField\NotesFieldServiceProvider;

trait HasNotes
{
    /**
     * Creates a new note and attaches it to the model.
     *
     * @param  bool  $user  Enables or disables the use of `Auth::user()` to set as the creator.
     * @param  bool  $system  Defines whether the note is system created and can be deleted or not.
     */
    public function addNote(array $noteForm, bool $user = true, bool $system = true): Note
    {
        $createdBy = $user ? Auth::user() : null;
        $text = Arr::get($noteForm, 'text');
        $actionAt = Arr::get($noteForm, 'action_at');

        return $this->notes()->create([
            'text' => $text,
            'action_at' => $actionAt ? now()->parse($actionAt) : null,
            'created_by' => $createdBy?->id,
            'system' => $system,
        ]);
    }

    /**
     * Edit a note with the given ID and text.
     *
     * @param  int  $noteId  The ID of the note to edit.
     */
    public function editNote(int $noteId, array $noteForm): Note
    {
        $text = Arr::get($noteForm, 'text');
        $actionAt = Arr::get($noteForm, 'action_at');

        $note = $this->notes()->where('id', '=', $noteId)->firstOrFail();
        $note->update([
            'text' => $text,
            'action_at' => $actionAt ? now()->parse($actionAt) : null,
        ]);

        return $note;
    }

    /**
     * Deletes a note with given ID and dissociates it from the model.
     *
     * @param  int|string  $noteId The ID of the note to delete.
     * @return void
     **/
    public function deleteNote($noteId)
    {
        $this->notes()->where('id', '=', $noteId)->delete();
    }

    public function notes()
    {
        return $this->morphMany(NotesFieldServiceProvider::getNotesModel(), 'notable');
    }
}
