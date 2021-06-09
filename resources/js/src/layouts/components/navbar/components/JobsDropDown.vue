<template>
  <!-- JOBS -->
  <vs-dropdown vs-custom-content vs-trigger-click class="cursor-pointer">
    <feather-icon icon="SettingsIcon" class="cursor-pointer mt-1 sm:mr-6 mr-2" :badge="jobs.length" />

    <vs-dropdown-menu class="notification-dropdown dropdown-custom vx-navbar-dropdown">

      <div class="notification-top text-center p-5 bg-primary text-white">
        <h3 class="text-white">{{ jobs.length }} Jobs</h3>
        <p class="opacity-75">Background Jobs</p>
      </div>

      <component :is="scrollbarTag" ref="mainSidebarPs" class="scroll-area--nofications-dropdown p-0 mb-10" :settings="settings" :key="$vs.rtl">
        <ul class="bordered-items">
          <li v-for="(job, index) in jobs" :key="index" class="flex justify-between px-4 py-4 notification cursor-pointer">
            <div class="flex items-start">
              <feather-icon :icon="jobIcons[job.status]" :svgClasses="[`text-${jobColors[job.status]}`, 'stroke-current mr-1 h-6 w-6']"></feather-icon>
              <div class="mx-2">
                <span class="font-medium block notification-title" :class="[`text-${jobColors[job.status]}`]">{{ job.name }}</span>
                <small>{{ jobMessages[job.status] }}</small>
              </div>
            </div>
            <small class="mt-1 whitespace-no-wrap">{{ elapsedTime(job.created_at) }}</small>
          </li>
        </ul>
      </component>

    </vs-dropdown-menu>
  </vs-dropdown>
</template>

<script>
import VuePerfectScrollbar from 'vue-perfect-scrollbar'

export default {
  components: {
    VuePerfectScrollbar
  },
  data () {
    return {
      jobIcons: {
        'running': 'SettingsIcon',
        'completed': 'CheckIcon',
        'failed': 'XIcon',
      },
      jobColors: {
        'running': 'primary',
        'completed': 'success',
        'failed': 'danger',
      },
      jobMessages: {
        'running': 'This job is currently running in background...',
        'completed': 'This job has been successfully completed.',
        'failed': 'This job terminated with an error!',
      },
      settings: {
        maxScrollbarLength: 60,
        wheelSpeed: .60
      },
    }
  },
  computed: {
    scrollbarTag () { return this.$store.getters.scrollbarTag },
    jobs() { return this.$store.getters.allJobs },
  },
  methods: {
    elapsedTime(startTime) {
      const x        = new Date(startTime)
      const now      = new Date()
      let timeDiff = now - x
      timeDiff    /= 1000

      const seconds = Math.round(timeDiff)
      timeDiff    = Math.floor(timeDiff / 60)

      const minutes = Math.round(timeDiff % 60)
      timeDiff    = Math.floor(timeDiff / 60)

      const hours   = Math.round(timeDiff % 24)
      timeDiff    = Math.floor(timeDiff / 24)

      const days    = Math.round(timeDiff % 365)
      timeDiff    = Math.floor(timeDiff / 365)

      const years   = timeDiff

      if (years > 0) {
        return `${years + (years > 1 ? ' Years ' : ' Year ')}ago`
      } else if (days > 0) {
        return `${days + (days > 1 ? ' Days ' : ' Day ')}ago`
      } else if (hours > 0) {
        return `${hours + (hours > 1 ? ' Hrs ' : ' Hour ')}ago`
      } else if (minutes > 0) {
        return `${minutes + (minutes > 1 ? ' Mins ' : ' Min ')}ago`
      } else if (seconds > 0) {
        return seconds + (seconds > 1 ? ' sec ago' : 'just now')
      }

      return 'Just Now'
    },
    // Method for creating dummy notification time
    randomDate ({ hr, min, sec }) {
      const date = new Date()

      if (hr) date.setHours(date.getHours() - hr)
      if (min) date.setMinutes(date.getMinutes() - min)
      if (sec) date.setSeconds(date.getSeconds() - sec)

      return date
    },
  },
  async created() {
    // Initialize jobs
    await this.$store.dispatch('initializeJobs')
  },
}

</script>

