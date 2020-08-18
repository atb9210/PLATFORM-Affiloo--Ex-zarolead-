<template>
  <vx-card no-shadow>

    <!-- Img Row -->
    <div class="flex flex-wrap items-center mb-base">
      <vs-avatar :src="activeUserInfo.info.profile_pic_url" size="70px" class="mr-4 mb-4" />
      <div>
        <vs-input class="w-full mt-1 mb-1" label-placeholder="Profile Picture URL" v-model="profilePicUrl"></vs-input>
        <!--
        <vs-button class="mr-4 sm:mb-0 mb-2">Set picture</vs-button>
        <vs-button type="border" color="danger">Remove</vs-button>
        <p class="text-sm mt-2">Allowed JPG, GIF or PNG. Max size of 800kB</p>
        -->
      </div>
    </div>

    <!-- Info -->
    <vs-input class="w-full mb-base" label-placeholder="Name" v-model="name"></vs-input>
    <vs-input class="w-full" label-placeholder="Email" v-model="email"></vs-input>

    <vs-alert v-if="accountConfirmed" icon-pack="feather" icon="icon-info" class="h-full my-4" color="success">
      <span>Your account is verified.</span>
    </vs-alert>
    <vs-alert v-else icon-pack="feather" icon="icon-check-circle" class="h-full my-4" color="warning">
      <span>Your account is not verified. <a href="#" class="hover:underline">Resend Confirmation</a></span>
    </vs-alert>

    <!-- Save & Reset Button -->
    <div class="flex flex-wrap items-center justify-end">
      <vs-button class="ml-auto mt-2" @click="updateUserInfo">Save Changes</vs-button>
      <vs-button class="ml-4 mt-2" type="border" color="warning">Reset</vs-button>
    </div>
  </vx-card>
</template>

<script>
export default {
  data () {
    return {
      id: this.$store.state.AppActiveUser.id,
      name: '',
      email: '',
      profilePicUrl: '',
      accountConfirmed: false,
    }
  },
  methods: {
    updateUserInfo() {
      this.$vs.loading()

      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }

      let data = {
        name: this.name,
        email: this.email,
      }
      let info = {}
      if (this.$store.state.AppActiveUser.info != '') {
        info = this.$store.state.AppActiveUser.info
        info.profile_pic_url = this.profilePicUrl
      }
      data.info = JSON.stringify(info)

      this.$http.put('/api/v1/users/' + this.id, data, { headers })
        .then((response) => {
          this.$vs.loading.close()
          this.$vs.notify({
            title: 'Success',
            text: 'User\'s info updated',
            iconPack: 'feather',
            icon: 'icon-alert-circle',
            color: 'success'
          })
          this.refreshUserInfo()
        })
        .catch((error) => {
          this.$vs.loading.close()
          this.$vs.notify({
            title: 'Error',
            text: error.message,
            iconPack: 'feather',
            icon: 'icon-alert-circle',
            color: 'danger'
          })
        })
    },
    refreshUserInfo() {
      this.$vs.loading()

      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }

      this.$http.get('/api/v1/users/' + this.id, { headers })
        .then((response) => {
          let res = response.data.data
          this.$store.commit('UPDATE_USER_INFO', res)

          if (res.hasOwnProperty('name')) {
            this.name = res.name
          }
          if (res.hasOwnProperty('email')) {
            this.email = res.email
          }
          if (res.hasOwnProperty('info') && res.info.hasOwnProperty('profile_pic_url')) {
            this.profilePicUrl = res.info.profile_pic_url
          }
          this.accountConfirmed = (res.hasOwnProperty('email_verified_at') && res.email_verified_at != null && res.email_verified_at != '')

          this.$vs.loading.close()
        })
        .catch((error) => {
          this.$vs.loading.close()
        })
    },
  },
  created() {
    this.refreshUserInfo()

    // TODO: to remove
    console.log(this.$store.state.AppActiveUser)
  },
  computed: {
    activeUserInfo () {
      return this.$store.state.AppActiveUser
    }
  }
}
</script>
