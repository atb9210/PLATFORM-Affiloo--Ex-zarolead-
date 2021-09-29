<template>
  <div>

    <vs-input
      type="text"
      v-if="editing"
      :value="valueLocal"
      @blur.native="cancel($event.target.value)"
      @keyup.escape.native="cancel($event.target.value)"
      @keyup.enter.native="update($event.target.value)"
      v-focus=""
      autofocus
      />

    <span v-else>
      <slot></slot>
      <span class="underline" @click="edit">(edit)</span>
    </span>

  </div>
</template>

<script>
export default {
  name: 'editable-value',
  props: {
    value: { type: [String, Number], required: true },
  },
  data () {
    return {
      editing: false,
      valueLocal: this.value,
    }
  },
  watch: {
    value: function () {
      this.valueLocal = this.value
    }
  },
  directives: {
    focus: {
      inserted(el) {
        el.focus()
      },
    },
  },
  methods: {
    edit() {
      this.$emit('onEdit')
      this.editing = true
    },
    update(value) {
      this.valueLocal = value
      this.editing = false
      this.$emit('input', this.valueLocal)
      this.$emit('onUpdate', this.valueLocal)
    },
    cancel(value) {
      this.editing = false
      this.$emit('onCancel', this.valueLocal)
    },
  },
}
</script>
