<template>
  <div class="mb-4 flex" :class="fullWidth ? 'w-full' : 'w-3/5'">
    <div class="flex flex-col">
      <div v-if="trixEnabled">
        <Trix
          ref="trixEditor"
          @keydown.stop
          @change="val => note.text = val"
          :value="note.text"
          :placeholder="placeholder"
          class="trix-content w-full form-control form-input form-input-bordered py-3 h-auto"
        />
      </div>

      <textarea
        v-else
        rows="3"
        :placeholder="placeholder"
        class="form-control w-full form-input form-input-bordered py-3 h-auto"
        v-model="note.text"
        @keydown.enter="onEnter"
      />

      <div class="mt-3">
        <label for="">Action date (optional)</label>
        <Input type="date" v-model="note.action_at" />
      </div>
    </div>

    <div class="whitespace-no-wrap ml-2 flex o1-items-end">
      <DefaultButton
        class="o1-inline-flex o1-items-center o1-relative o1-ml-auto o1-whitespace-nowrap"
        @click="submit"
        type="button"
        :disabled="loading || !note"
      >
        {{ editing ? __('novaNotesField.updateNote') : __('novaNotesField.addNote') }}
      </DefaultButton>
    </div>
  </div>
</template>

<script>
import { Input } from 'laravel-nova-ui'
import {format} from "date-fns";

export default {
  components: { Input },
  props: ['placeholder', 'note', 'loading', 'trixEnabled', 'fullWidth', 'editing'],
  mounted() {
    if (this.note?.action_at) {
      this.note.action_at = format(new Date(this.note.action_at), 'yyyy-MM-dd');
    }

    if (this.note) {
      this.renderTrix();
    }
  },
  methods: {
    onEnter(e) {
      if (e.shiftKey) return true;

      e.preventDefault();
      e.stopPropagation();
      this.submit();
      return true;
    },

    renderTrix() {
      if (this.trixEnabled && this.$refs.trixEditor) {
        this.$refs.trixEditor.$refs.theEditor.editor.loadHTML(this.note.text);
      }
    },

    submit() {
      this.$emit('onSubmit', this.note);
    },
  },

  watch: {
    'note.text'(newValue, oldValue) {
      if (!newValue && !!oldValue) {
        this.renderTrix();
      }
    },
  },
};
</script>
