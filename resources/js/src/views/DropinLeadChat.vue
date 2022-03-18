<!-- =========================================================================================
    File Name: Table.vue
    Description: Table demo - Imports all table demos
    ----------------------------------------------------------------------------------------
    Item Name: Vuexy - Vuejs, HTML & Laravel Admin Dashboard Template
      Author: Pixinvent
    Author URL: http://www.themeforest.net/user/pixinvent
========================================================================================== -->


<template>
    <vx-card title="Leads" code-toggler>
        <p class="flex items-center mb-4">
          <vx-tooltip text="Back to Leads Table" position="top">
              <vs-button class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-arrow-left" @click="goToLeadsTable"></vs-button>
          </vx-tooltip>

          <vx-tooltip text="Refresh Chat" position="top">
              <vs-button class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-refresh-cw" @click="refreshChatMessages"></vs-button>
          </vx-tooltip>
        </p>

        <div>
            <Chat v-if="lead !== null" :user="lead" :chatMessages="chatMessages" />
        </div>
    </vx-card>
</template>

<script>
import Dropin from 'dropin'

export default {
  data () {
    return {
      lead_id: 0,

      dropin: null,
      lead: null,
      chatMessages: [],

      timer: null,
    }
  },
  methods: {
    async refreshChatMessages() {
      this.$vs.loading()
      this.chatMessages = await this.dropin.fetchLeadMessages(this.lead.id)
      this.$vs.loading.close()
    },
    async refreshChatMessagesBackground() {
      this.chatMessages = await this.dropin.fetchLeadMessages(this.lead.id)
    },
    goToLeadsTable() {
      this.$router.push({name: 'dropin-leads'})
        .catch(error => {
          this.$router.push({name: 'dropin-leads'})
        })
    },
  },
  async created() {
    this.lead_id = parseInt(this.$route.params.lead_id)
    if (this.lead_id !== null && this.lead_id > 0) {
      this.$vs.loading()

      this.dropin = new Dropin(localStorage.getItem('accessToken'))
      this.lead = await this.dropin.fetchLead(this.lead_id)
      if (this.lead !== null) {
        this.chatMessages = await this.dropin.fetchLeadMessages(this.lead.id)

        // Refresh Chat every 10 seconds
        this.timer = setInterval(() => {
          this.refreshChatMessagesBackground()
        }, 10 * 1000)
      } else {
        this.$vs.notify({
          title: 'Error',
          text: 'Lead not found',
          iconPack: 'feather',
          icon: 'icon-alert-circle',
          color: 'danger'
        })
      }

      this.$vs.loading.close()
    } else {
      this.$vs.notify({
        title: 'Error',
        text: 'Invalid lead id',
        iconPack: 'feather',
        icon: 'icon-alert-circle',
        color: 'danger'
      })
    }
  },
  beforeDestroyed() {
    if (this.timer !== null) {
      clearInterval(this.timer)
      this.timer = null
    }
  },
}
</script>
