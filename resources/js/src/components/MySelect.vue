<template>
    <vs-select
      :value="value"
      @change="onChange"
      @input="onInput"
      :label="label"
      :autocomplete="autocomplete"
      :multiple="multiple"
      :icon-pack="iconPack"
      :icon="icon"
      >
      <vs-select-item :key="index" :value="typeof(item) === 'object' ? ((item.hasOwnProperty('label') && item.hasOwnProperty('value')) ? item.value : item) : item" :text="typeof(item) === 'object' ? ((item.hasOwnProperty('label')) ? item.label : item.name) : item" v-for="(item, index) in options" />
    </vs-select>
</template>

<script>
export default {
  name: 'MySelect',
  props: {
    value: { type: [String, Number, Object, Boolean, Array], required: true },
    options: { type: Array, required: true },
    label: { type: String, required: false },
    autocomplete: { type: [String, Number, Boolean], required: false },
    multiple: { type: [String, Number, Boolean], required: false },
    iconPack: { type: [String], required: false },
    icon: { type: [String], required: false },
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
