<template>
  <Modal :show="show" @modal-close="handleClose" @close-via-escape="$emit('close')">
    <form
      @submit.prevent="handleConfirm"
      class="mx-auto bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden"
    >
      <slot>
        <ModalHeader v-text="__('novaNotesField.deleteNoteTitle')" />
        <ModalContent>
          <p class="leading-normal">
            {{ __('novaNotesField.deleteConfirmation') }}
          </p>
        </ModalContent>
      </slot>

      <ModalFooter>
        <div class="ml-auto">
          <LinkButton
            type="button"
            dusk="cancel-delete-button"
            @click.prevent="handleClose"
            class="mr-3"
          >
            {{ __('Cancel') }}
          </LinkButton>

          <Button
            type="submit"
            ref="confirmButton"
            dusk="confirm-delete-button"
            :loading="working"
            state="danger"
            :label="__('novaNotesField.delete')"
          />
        </div>
      </ModalFooter>
    </form>
  </Modal>
</template>

<script>
import {Button} from "laravel-nova-ui";

export default {
  components: {
    Button,
  },

  props: ['show'],

  emits: ['confirm', 'close'],

  data: () => ({
    working: false,
  }),

  methods: {
    handleClose() {
      this.$emit('close')
      this.working = false
    },

    handleConfirm() {
      this.$emit('confirm')
      this.working = true
    },
  },
};
</script>
