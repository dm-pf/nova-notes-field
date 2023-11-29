<template>
  <div>
    <div
      v-if="adding || editing"
      class="mb-4 flex" :class="fullWidth ? 'w-full' : 'w-3/5'"
    >
      <div class="flex flex-col w-full">
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

        <div class="o1-mt-3 o1-w-fit">
          <label for="">Action date (optional)</label>
          <Input type="date" v-model="note.action_at"/>
        </div>

        <div class="o1-my-5">
          <DefaultButton
            class="o1-inline-flex o1-items-center o1-relative o1-ml-auto o1-whitespace-nowrap"
            @click="submit"
            type="button"
            :disabled="loading || !note"
          >
            {{ editing ? __('novaNotesField.updateNote') : __('novaNotesField.addNote') }}
          </DefaultButton>

          <LinkButton
            type="button"
            @click="cancel"
            class="mr-3"
          >
            {{ __('Cancel') }}
          </LinkButton>
        </div>
      </div>


    </div>

    <div v-else>
      <input type="text"
             class="w-full o1-my-5 o1-cursor-pointer appearance-none placeholder:text-gray-400 dark:placeholder:text-gray-500 px-3 focus:outline-none text-gray-600 dark:text-gray-400 disabled:cursor-not-allowed bg-white dark:bg-gray-900 h-9"
             placeholder="Add a note..."
             @click="display"/>
    </div>
  </div>
</template>

<script>
import {Input} from 'laravel-nova-ui'
import {format} from "date-fns";

export default {
  components: { Input },
  props: ['placeholder', 'note', 'loading', 'trixEnabled', 'fullWidth', 'editing'],
  data: () => ({
    adding: false,
  }),
  mounted() {
    if (this.note?.action_at) {
      this.note.action_at = format(new Date(this.note.action_at), 'yyyy-MM-dd');
    }

    if (this.note) {
      this.renderTrix();
    }
  },
  methods: {
    cancel() {
      this.adding = false;
      this.$emit('onCancel');
    },
    display(event) {
      event.preventDefault();
      this.adding = true;

      setTimeout(() => {
        if (this.trixEnabled && this.$refs.trixEditor) {
          this.$refs.trixEditor.$refs.theEditor.focus();
        }
      });
    },
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
      this.cancel();
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
