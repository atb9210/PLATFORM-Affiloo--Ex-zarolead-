<template>
  <vx-card no-shadow>

    <div class="vx-col md:w-full w-full mt-base">
        <vs-input type="password" class="w-full" label-placeholder="Old Password" v-model="old_password"></vs-input>
        <span class="text-danger">{{ errors.first('old_password') }}</span>
    </div>
    <div class="vx-col md:w-full w-full mt-base">
        <vs-input type="password" class="w-full" label-placeholder="New Password" v-model="password"></vs-input>
        <span class="text-danger">{{ errors.first('password') }}</span>
    </div>
    <div class="vx-col md:w-full w-full mt-base">
        <vs-input type="password" class="w-full" label-placeholder="Confirm Password" v-model="password_confirmation"></vs-input>
        <span class="text-danger">{{ errors.first('password_confirmation') }}</span>
    </div>

    <!-- Save & Reset Button -->
    <div class="flex flex-wrap items-center justify-end">
      <vs-button class="ml-auto mt-2" @click="changePassword">Save Changes</vs-button>
      <vs-button class="ml-4 mt-2" type="border" color="warning">Reset</vs-button>
    </div>
  </vx-card>
</template>

<script>
export default {
  data () {
    return {
      id: this.$store.state.AppActiveUser.id,
      old_password: '',
      password: '',
      password_confirmation: '',
    }
  },
  methods: {
    changePassword() {
      if (this.password != this.password_confirmation) {
        this.errors.add({
          field: 'password_confirmation',
          msg: 'Passwords should match',
        })
      }

      this.$vs.loading()
      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }
      let data = {
        old_password: this.old_password,
        password: this.password,
        password_confirmation: this.password_confirmation,
      }

      this.$http.post('/api/v1/users/' + this.id + '/change-password', data, { headers: headers })
        .then((response) => {
          this.$vs.loading.close()

          this.errors.clear()
          this.old_password = ''
          this.password = ''
          this.password_confirmation = ''

          this.$vs.notify({
            title: 'Success',
            text: 'Password changed successfully',
            iconPack: 'feather',
            icon: 'icon-alert-circle',
            color: 'success'
          })
        })
        .catch((error) => {
          this.$vs.loading.close()
          this.errors.clear()
          for (const [key, value] of Object.entries(error.response.data.errors)) {
            for (let i = 0; i < value.length; i++) {
              this.errors.add({
                field: key,
                msg: value[i]
              })
            }
          }
        })
    }
  },
  computed: {
    activeUserInfo () {
      return this.$store.state.AppActiveUser
    }
  }
}
</script>
