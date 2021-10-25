<template>
  <div>

    <vs-select
      v-if="editing"
      :value="valueLocal"
      @blur.native="cancel"
      @keyup.escape.native="cancel"
      @input="update"
      v-focus=""
      autofocus
      >
      <vs-select-item :key="index" :value="item" :text="item" v-for="(item, index) in options" />
    </vs-select>

    <span v-else>
      <span @dblclick="edit">
        <slot></slot>
      </span>
    </span>

  </div>
</template>

<script>
export default {
  name: 'FastEditableValueSelect',
  props: {
    value: { type: [String, Number], required: true },
    options: { type: Array, required: true },
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
    cancel() {
      this.editing = false
      this.$emit('onCancel', this.valueLocal)
    },
  },
}
</script>
