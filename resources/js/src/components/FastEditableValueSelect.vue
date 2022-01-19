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
      <vs-select-item :key="keyPrefix + index" :value="typeof(item) === 'object' ? item.id : item" :text="typeof(item) === 'object' ? item.label : item" v-for="(item, index) in options" />
    </vs-select>

    <span v-else>
      <span @dblclick="edit">
        <slot></slot>
      </span>
    </span>

  </div>
</template>

<script>
function randomString(length) {
  let result           = '';
  let characters       = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
  let charactersLength = characters.length;
  for (var i = 0; i < length; i++) {
    result += characters.charAt(Math.floor(Math.random()*charactersLength));
  }
  return result;
}

export default {
  name: 'FastEditableValueSelect',
  props: {
    value: { type: [String, Number, Object], required: true },
    options: { type: Array, required: true },
  },
  data () {
    return {
      editing: false,
      valueLocal: this.value,
      keyPrefix: '',
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
  created() {
    this.keyPrefix = 'k' + randomString(10)
  },
}
</script>
