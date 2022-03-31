<!-- =========================================================================================
    File Name: Table.vue
    Description: Table demo - Imports all table demos
    ----------------------------------------------------------------------------------------
    Item Name: Vuexy - Vuejs, HTML & Laravel Admin Dashboard Template
      Author: Pixinvent
    Author URL: http://www.themeforest.net/user/pixinvent
========================================================================================== -->


<template>
    <vx-card title="Customer Chat" code-toggler>
        <p class="flex items-center mb-4">
          <vx-tooltip text="Back to Customers Table" position="top">
              <vs-button class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-arrow-left" @click="goToCustomersTable"></vs-button>
          </vx-tooltip>

          <vx-tooltip text="Refresh Chat" position="top">
              <vs-button class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-refresh-cw" @click="refreshChatMessages"></vs-button>
          </vx-tooltip>
        </p>

        <div>
            <Chat v-if="customer !== null" :user="customer" :chatMessages="chatMessages" />
        </div>
    </vx-card>
</template>

<script>
import { Customer } from 'zarolead'

export default {
  data () {
    return {
      customer_id: 0,

      customerClient: null,
      customer: null,
      chatMessages: [],

      timer: null,
    }
  },
  methods: {
    async refreshChatMessages() {
      this.$vs.loading()
      this.chatMessages = await this.customerClient.fetchMessages(this.customer.id)
      this.$vs.loading.close()
    },
    async refreshChatMessagesBackground() {
      let newChatMessages = await this.customerClient.fetchMessages(this.customer.id)
      if (newChatMessages.length > 0 && newChatMessages.length != this.chatMessages.length) {
        this.$notification.show('New message from ' + this.customer.first_name + ' ' + this.customer.last_name, {
          body: this.firstNCharacters(newChatMessages[0].body),
        }, {})
      }
      this.chatMessages = newChatMessages
    },
    goToCustomersTable() {
      this.$router.push({name: 'customers'})
        .catch(error => {
          this.$router.push({name: 'customers'})
        })
    },
    firstNCharacters(s, numChars) {
      if (s === null || s == '') {
        return ''
      }
      let res = s.substr(0, numChars)
      if (s.length > numChars) {
        res += '...'
      }
      return res
    },
  },
  async created() {
    this.customer_id = parseInt(this.$route.params.customer_id)
    if (this.customer_id !== null && this.customer_id > 0) {
      this.$vs.loading()

      this.customerClient = new Customer(this.$zarolead)
      this.customer = await this.customerClient.show(this.customer_id)
      if (this.customer !== null) {
        this.chatMessages = await this.customerClient.fetchMessages(this.customer.id)

        // Refresh chat every 10 seconds
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
        text: 'Invalid customer id',
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
