<template>
  <vs-input v-model="localValue" @keyup="forcePhoneFormat" icon-pack="feather" icon="icon-phone" icon-no-border placeholder="Phone Number" />
</template>

<script>
export default {
  name: 'PhoneInput',
  props: {
    value: { type: String, required: true },
  },
  data () {
    return {
      localValue: '',
    }
  },
  methods: {
    forcePhoneFormat() {
      let val = this.localValue
      let phonePrefix = '+(39)'
      let pos = val.indexOf(phonePrefix)
      if (pos < 0) {
        val = phonePrefix + val;
      }

      let newVal = phonePrefix
      for (let i = phonePrefix.length; i < val.length; i++) {
        if ((val[i] >= '0' && val[i] <= '9')) {
          newVal += val[i]
        }
      }

      this.localValue = newVal
    },
    setValue(newValue) {
      this.localValue = newValue
      this.forcePhoneFormat()
    },
  },
  created() {
    this.localValue = this.value

    this.$watch('localValue', value => {
      this.$emit('input', value)
    })
  },
}
</script>
