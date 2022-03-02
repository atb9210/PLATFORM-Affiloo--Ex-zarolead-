<template>
  <div class="the-navbar__user-meta flex items-center" v-if="activeUserInfo.name">

    <div class="text-right leading-tight hidden sm:block">
      <p class="font-semibold">{{ activeUserInfo.name }}</p>
      <small>Available</small>
    </div>

    <vs-dropdown vs-custom-content vs-trigger-click class="cursor-pointer">

      <div class="con-img ml-3">
        <vs-avatar v-if="activeUserInfo.info.profile_pic_url" key="onlineImg" :src="activeUserInfo.info.profile_pic_url" alt="user-img" width="40" height="40" class="rounded-full shadow-md cursor-pointer block" />
        <vs-avatar v-else key="onlineImg" :src="activeUserInfo.info.profile_pic_url" alt="user-img" width="40" height="40" class="rounded-full shadow-md cursor-pointer block" />
      </div>

      <vs-dropdown-menu class="vx-navbar-dropdown">
        <ul style="min-width: 9rem">

          <li
            class="flex py-2 px-4 cursor-pointer hover:bg-primary hover:text-white"
            @click="$router.push('/user/settings').catch(() => {})">
            <feather-icon icon="UserIcon" svgClasses="w-4 h-4" />
            <span class="ml-2">Profile</span>
          </li>

          <li v-if="hasOneOfTheseRoles(['developer', 'admin', 'mediabuyer', 'advertiser'])"
            class="flex py-2 px-4 cursor-pointer hover:bg-primary hover:text-white"
            @click="$router.push('/dashboard/products').catch(() => {})">
            <feather-icon icon="CodesandboxIcon" svgClasses="w-4 h-4" />
            <span class="ml-2">Products</span>
          </li>

          <li v-if="hasOneOfTheseRoles(['developer', 'admin'])"
            class="flex py-2 px-4 cursor-pointer hover:bg-primary hover:text-white"
            @click="$router.push('/dashboard/users').catch(() => {})">
            <feather-icon icon="UserIcon" svgClasses="w-4 h-4" />
            <span class="ml-2">Users</span>
          </li>

          <li v-if="hasOneOfTheseRoles(['developer', 'admin'])"
            class="flex py-2 px-4 cursor-pointer hover:bg-primary hover:text-white"
            @click="$router.push('/dashboard/advertisers').catch(() => {})">
            <feather-icon icon="LayersIcon" svgClasses="w-4 h-4" />
            <span class="ml-2">Advertisers</span>
          </li>

          <li v-if="hasOneOfTheseRoles(['developer', 'admin'])"
            class="flex py-2 px-4 cursor-pointer hover:bg-primary hover:text-white"
            @click="$router.push('/dashboard/networks').catch(() => {})">
            <feather-icon icon="GlobeIcon" svgClasses="w-4 h-4" />
            <span class="ml-2">Networks</span>
          </li>

          <li v-if="hasOneOfTheseRoles(['developer', 'admin'])"
            class="flex py-2 px-4 cursor-pointer hover:bg-primary hover:text-white"
            @click="$router.push('/dashboard/tags').catch(() => {})">
            <feather-icon icon="TagIcon" svgClasses="w-4 h-4" />
            <span class="ml-2">Tags</span>
          </li>

          <li v-if="hasOneOfTheseRoles(['developer', 'admin'])"
            class="flex py-2 px-4 cursor-pointer hover:bg-primary hover:text-white"
            @click="$router.push('/dashboard/traffic-sources').catch(() => {})">
            <feather-icon icon="RadioIcon" svgClasses="w-4 h-4" />
            <span class="ml-2">Traffic Sources</span>
          </li>

          <li v-if="hasOneOfTheseRoles(['developer', 'admin'])"
            class="flex py-2 px-4 cursor-pointer hover:bg-primary hover:text-white"
            @click="$router.push('/dashboard/3rd-party').catch(() => {})">
            <feather-icon icon="CloudLightningIcon" svgClasses="w-4 h-4" />
            <span class="ml-2">3rd Party</span>
          </li>

          <!--
          <li
            class="flex py-2 px-4 cursor-pointer hover:bg-primary hover:text-white"
            @click="$router.push('/apps/chat').catch(() => {})">
            <feather-icon icon="MessageSquareIcon" svgClasses="w-4 h-4" />
            <span class="ml-2">Chat</span>
          </li>

          <li
            class="flex py-2 px-4 cursor-pointer hover:bg-primary hover:text-white"
            @click="$router.push('/apps/email').catch(() => {})">
            <feather-icon icon="MailIcon" svgClasses="w-4 h-4" />
            <span class="ml-2">Inbox</span>
          </li>

          <li
            class="flex py-2 px-4 cursor-pointer hover:bg-primary hover:text-white"
            @click="$router.push('/apps/todo').catch(() => {})">
            <feather-icon icon="CheckSquareIcon" svgClasses="w-4 h-4" />
            <span class="ml-2">Tasks</span>
          </li>

          <li
            class="flex py-2 px-4 cursor-pointer hover:bg-primary hover:text-white"
            @click="$router.push('/apps/eCommerce/wish-list').catch(() => {})">
            <feather-icon icon="HeartIcon" svgClasses="w-4 h-4" />
            <span class="ml-2">Wish List</span>
          </li>
          -->

          <vs-divider class="m-1" />

          <li
            class="flex py-2 px-4 cursor-pointer hover:bg-primary hover:text-white"
            @click="logout">
            <feather-icon icon="LogOutIcon" svgClasses="w-4 h-4" />
            <span class="ml-2">Logout</span>
          </li>
        </ul>
      </vs-dropdown-menu>
    </vs-dropdown>
  </div>
</template>

<script>
//import firebase from 'firebase/app'
//import 'firebase/auth'

export default {
  data () {
    return {

    }
  },
  computed: {
    activeUserInfo () {
      return this.$store.state.AppActiveUser
    }
  },
  methods: {
    hasOneOfTheseRoles(theseRoles) {
      let roles = this.activeUserInfo.roles
      for (let i = 0; i < roles.length; i++) {
        if (theseRoles.includes(roles[i])) {
          return true
        }
      }
      return false
    },
    logout () {
      this.$store.dispatch('auth/logoutAPI')
      this.$router.push('/pages/login').catch(() => {})
    }
  }
}
</script>
