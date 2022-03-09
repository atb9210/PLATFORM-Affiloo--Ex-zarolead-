<template>
    <div v-if="user != null" class="chat__header">
        <vs-navbar class="p-4 flex navbar-custom" color="white" type="flat">
            <div class="relative flex mr-4">
                <feather-icon icon="MenuIcon" class="mr-4 cursor-pointer" v-if="isSidebarCollapsed" @click.stop="$emit('openContactsSidebar')" />
                <vs-avatar class="m-0 border-2 border-solid border-white" size="40px" src="https://iupac.org/wp-content/uploads/2018/05/default-avatar.png" @click.stop="$emit('showProfileSidebar', user)" />
                <div class="h-3 w-3 border-white border border-solid rounded-full absolute right-0 bottom-0" :class="'bg-' + getStatusColor(false)"></div>
            </div>
            <h6>{{ user.full_name }}</h6>
            <vs-spacer></vs-spacer>
            <feather-icon icon="StarIcon" class="cursor-pointer" :svgClasses="[{'text-warning stroke-current': isPinnedLocal}, 'w-6', 'h-6']" @click.stop="isPinnedLocal = !isPinnedLocal"></feather-icon>
        </vs-navbar>
    </div>
</template>

<script>
export default {
  name: 'ChatNavbar',
  props: {
    user: {
      type: Object,
      required: true
    },
    isPinnedProp: {
      type: Boolean,
      required: true
    },
    isSidebarCollapsed: {
      type: Boolean,
      required: true
    }
  },
  computed: {
    isPinnedLocal: {
      get () {
        return this.isPinnedProp
      },
      set (val) {
        const chatData = this.$store.getters['chat/chatDataOfUser'](this.userId)
        if (chatData) {
          const payload = { id: this.userId, value: val }
          this.$store.dispatch('chat/toggleIsPinned', payload)
            .then(() => { this.$emit('toggleIsChatPinned', val) })
            .catch((err) => { console.error(err) })
        } else {
          this.$emit('toggleIsChatPinned', val)
        }
      }
    },
  },
  data () {
    return {
    }
  },
  methods: {
    getStatusColor() {
      // TODO: HERE
      return 'danger'
    },
  },
  updated () {
  },
  mounted () {
  }
}
</script>
