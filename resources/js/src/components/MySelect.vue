<template>
    <vs-select
      :value="value"
      @change="onChange"
      @input="onInput"
      :label="label"
      :autocomplete="autocomplete"
      >
      <vs-select-item :key="index" :value="typeof(item) === 'object' ? item.value : item" :text="typeof(item) === 'object' ? item.label : item" v-for="(item, index) in options" />
    </vs-select>
</template>

<script>
export default {
  name: 'MySelect',
  props: {
    value: { type: [String, Number, Object], required: true },
    options: { type: Array, required: true },
    label: { type: String, required: false },
    autocomplete: { type: [String, Number, Boolean], required: false },
  },
  data () {
    return {
    }
  },
  methods: {
    onChange(val) {
      this.$emit('change', val)
    },
    onInput(val) {
      this.$emit('input', val)
    },
  },
}
</script>
