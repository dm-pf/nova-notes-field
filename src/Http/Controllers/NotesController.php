<?php

namespace Outl1ne\NovaNotesField\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Arr;
use Laravel\Nova\Nova;
use Outl1ne\NovaNotesField\Models\Note;

class NotesController extends Controller
{
    // GET /notes
    public function getNotes(Request $request)
    {
        $validationResult = $this->validateRequest($request);
        if ($validationResult['has_errors'] === true) {
            return response($validationResult['errors'], 400);
        }

        $notesRelationship = $request->get('field');
        $notesRelationshipAllowed = in_array(
            $request->get('field'),
            [
                'notesProjects',
                'notesApplications',
            ],
            true
        );

        $model = $validationResult['model'];
        $model->loadMissing('notes.history');

        $displayOrder = config('nova-notes-field.display_order', 'DESC');
        $sortingMethod = strtolower($displayOrder) === 'asc' ? 'sortBy' : 'sortByDesc';

        $notes = $notesRelationshipAllowed
            ? $model->getAttribute($notesRelationship)
            : $model->notes
                ->$sortingMethod('created_at')
                ->values()
                ->toArray();

        return response()->json([
            'date_format' => config('nova-notes-field.date_format', 'dd MMM yyyy HH:mm'),
            'trix_enabled' => config('nova-notes-field.use_trix_input', false),
            'notes' => $notes,
        ], 200);
    }

    // POST /notes
    public function addNote(Request $request)
    {
        $validationResult = $this->validateRequest($request);
        if ($validationResult['has_errors'] === true) {
            return response($validationResult['errors'], 400);
        }

        $model = $validationResult['model'];
        $note = $request->input('note');

        if (empty($note) || empty($note['text'])) {
            return response(['errors' => ['note' => 'required']], 400);
        }

        $model->addNote($note, true, false);

        return response('', 204);
    }

    // PATCH /notes/{note}
    public function editNote(Request $request, Note $note)
    {
        $noteForm = $request->input('note');
        $text = Arr::get($noteForm, 'text');
        $actionAt = Arr::get($noteForm, 'action_at');

        if (empty($text)) {
            return response(['errors' => ['note' => 'required']], 400);
        }

        if (! $note->can_edit) {
            return response()->json(['error' => 'unauthorized'], 400);
        }

        $note->update([
            'text' => $text,
            'action_at' => $actionAt ? now()->parse($actionAt) : null,
        ]);

        return response('', 204);
    }

    // DELETE /notes
    public function deleteNote(Request $request)
    {
        $validationResult = $this->validateRequest($request);
        if ($validationResult['has_errors'] === true) {
            return response()->json($validationResult['errors'], 400);
        }

        $model = $validationResult['model'];
        $noteId = $request->input('noteId');

        if (empty($noteId)) {
            return response()->json(['errors' => ['noteId' => 'required']], 400);
        }

        $note = Note::find($noteId);
        if (empty($note)) {
            return response('', 204);
        }

        if (! $note->canDelete) {
            return response()->json(['error' => 'unauthorized'], 400);
        }

        $note->delete();

        return response('', 204);
    }

    private function validateRequest(Request $request)
    {
        $resourceId = $request->get('resourceId');
        $resourceName = $request->get('resourceName');

        $errors = [];
        if (empty($resourceId)) {
            $errors['resourceId'] = 'required';
        }
        if (empty($resourceName)) {
            $errors['resourceName'] = 'required';
        }

        if (! empty($resourceName)) {
            $resourceClass = Nova::resourceForKey($resourceName);
            if (empty($resourceClass)) {
                $errors['resourceName'] = 'invalid_name';
            } else {
                $modelClass = $resourceClass::$model;

                if (method_exists($modelClass, 'trashed')) {
                    $model = $modelClass::withTrashed()->find($resourceId);
                } else {
                    $model = $modelClass::find($resourceId);
                }

                if (empty($model)) {
                    $errors['resourceId'] = 'not_found';
                }
            }
        }

        return [
            'has_errors' => count($errors) > 0,
            'errors' => $errors,
            'model' => isset($model) ? $model : null,
        ];
    }
}
