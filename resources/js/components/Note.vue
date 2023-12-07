<template>
  <template v-if="isEditing">
    <NoteInput
      :note="editedNote"
      @onSubmit="editNote"
      @onCancel="stopEditing"
      :loading="loading"
      :fullWidth="fullWidth"
      :trixEnabled="trixEnabled"
      :editing="true"
    />
  </template>
  <template v-else>
    <div
      class="o1-bg-white dark:o1-bg-slate-800 o1-rounded-md o1-border o1-border-gray-200 dark:o1-border-gray-700 o1-px-2 o1-py-2 o1-flex o1-mb-2 o1-mt-2"
      :class="{ 'w-full': fullWidth, 'o1-w-3/5': !fullWidth }"
    >
      <div class="o1-rounded-lg o1-w-12 o1-h-12 o1-mr-3 o1-overflow-hidden o1-text-center" style="flex-shrink: 0">
        <!-- Image -->
        <div
          v-if="note.system"
          class="o1-w-12 o1-h-12 o1-text-sm o1-font-bold o1-bg-gray-50 o1-text-gray-700 dark:o1-text-gray-400"
          style="line-height: 3rem"
        >
          {{ __('novaNotesField.systemUserAbbreviation') }}
        </div>
        <img class="o1-w-12 o1-h-12" v-else-if="note.created_by_avatar_url" :src="note.created_by_avatar_url" alt=""/>
        <div
          v-else
          class="o1-w-12 o1-h-12 o1-text-sm o1-font-bold o1-bg-gray-50 o1-text-gray-700 dark:o1-text-gray-400"
          style="line-height: 3rem"
        >
          {{ !!note.created_by_name ? (note.created_by_name || '').substr(0, 3).toUpperCase() : '??' }}
        </div>
      </div>

      <div class="o1-w-full">
        <!-- Title area -->
        <div class="o1-mb-2">
          <div class="o1-flex o1-justify-between o1-items-center">
            <div>
              <span class="o1-font-bold o1-text-base o1-text-gray-700 o1-mr-2 dark:o1-text-gray-400">
                {{ note.created_by_name ? note.created_by_name : __('novaNotesField.systemUserName') }}
              </span>

              <a :href="'/resources/notes/' + note.id" class="o1-text-xs o1-text-gray-700 o1-mr-2 dark:o1-text-gray-400">
                {{ formattedCreatedAtDate }}{{ note.system ? ` [${__('novaNotesField.systemUserName')}]` : '' }}{{ note?.history?.length > 1 ? ` - ${__('novaNotesField.edited')}` : '' }}
              </a>

              <span
                v-if="note.action_at"
                class="o1-text-xs o1-text-orange-500 o1-mr-2 dark:o1-text-amber-400 o1-bg-primary-500"
              >[Action date: {{ formattedActionAtDate }}]</span>
            </div>

            <div>
              <a :href="'/resources/notes/' + note.id" class="o1-text-xs o1-text-blue-500 o1-mr-2 dark:o1-text-blue-400">
                [{{ __('novaNotesField.history') }}]
              </a>

              <span
                v-if="!note.system && note.can_edit"
                class="o1-text-xs hover:o1-underline o1-cursor-pointer o1-text-primary-400 o1-mr-2"
                @click="onEditRequested"
              >[{{ __('novaNotesField.edit') }}]</span>

              <span
                v-if="!note.system && note.can_delete"
                class="o1-text-xs hover:o1-underline o1-cursor-pointer o1-mr-2"
                style="color: #e74c3c"
                @click="$emit('onDeleteRequested', note)"
              >[{{ __('novaNotesField.delete') }}]</span>
            </div>
          </div>
        </div>

        <!-- Content -->
        <p v-html="note.text" class="o1-whitespace-pre-wrap o1-text-gray-800 dark:o1-text-gray-400"></p>
      </div>
    </div>
  </template>
</template>

<script>
import NoteInput from './NoteInput';
import {format} from 'date-fns';

export default {
  components: { NoteInput },
  props: ['note', 'dateFormat', 'fullWidth', 'trixEnabled'],
  data: () => ({
    isEditing: false,
    editedNote: {},
    loading: false,
  }),
  computed: {
    formattedActionAtDate() {
      return format(new Date(this.note.action_at), 'dd MMM yyyy');
    },
    formattedCreatedAtDate() {
      return format(new Date(this.note.created_at), this.dateFormat);
    },
  },
  methods: {
    onEditRequested() {
      this.editedNote = this.note;
      this.isEditing = true;
    },
    async editNote() {
      this.loading = true;

      try {
        await Nova.request().patch(`/nova-vendor/nova-notes/notes/${this.note.id}`, {
          note: this.editedNote,
        });
        this.isEditing = false;
        this.$emit('noteEdited', { note: this.note, editedNote: this.editedNote });
      } catch (e) {
        Nova.error('Unknown error when trying to edit the note.');
      }

      this.loading = false;
    },
    stopEditing() {
      this.isEditing = false;
      this.editedNote = {};
    },
  },
};
</script>
