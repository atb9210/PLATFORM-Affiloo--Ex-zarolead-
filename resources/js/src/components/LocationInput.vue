<template>
  <div class="vs-component vs-con-input-label vs-input w-full vs-input-primary">
 
    <div class="vs-con-input">
        <GmapAutocomplete class="vs-inputx vs-input--input" @place_changed="setPlace" />
    </div>

  </div>
</template>

<script>
import GmapAutocomplete from 'vue2-google-maps/dist/components/autocomplete'
export default {
  name: 'LocationInput',
  data () {
    return {
      place: '',
    }
  },
  methods: {
    setPlace(place) {
      this.place = {
        street_number: '',
        street: '',
        city: '',
        province: '',
        state: '',
        country: '',
        postcode: '',
      }

      if (place.hasOwnProperty('address_components') && place.address_components !== null) {
        for (let i = 0; i < place.address_components.length; i++) {
          let a = place.address_components[i]
          if (!a.hasOwnProperty('types') || a.types.length == 0) {
            continue
          }
          let t = a.types
          let v = a.short_name
          if (t.includes('street_number')) {
            this.place.street_number = v
          } else if (t.includes('route')) {
            this.place.street = v
          } else if (t.includes('administrative_area_level_3')) {
            this.place.city = v
          } else if (t.includes('administrative_area_level_2')) {
            this.place.province = v
          } else if (t.includes('administrative_area_level_1')) {
            this.place.state = v
          } else if (t.includes('country')) {
            this.place.country = v
          } else if (t.includes('postal_code')) {
            this.place.postcode = v
          }
        }
      }

      this.$emit('input', this.place)
      this.$emit('onEdit', this.place)
    },
  },
  created() {
  },
}
</script>
