<template>
    <div id="chat-app" class="border border-solid d-theme-border-grey-light rounded relative overflow-hidden">
        <div id="chatMain" class="chat__bg no-scroll-content chat-content-area border border-solid d-theme-border-grey-light border-t-0 border-r-0 border-b-0" :class="{'sidebar-spacer--wide': clickNotClose, 'flex items-center justify-center': user === null}">
            <template v-if="chatMessages.length > 0">
                <div class="chat__navbar">
                    <ChatNavbar :isSidebarCollapsed="!clickNotClose" :user="user" :isPinnedProp="isChatPinned" @openContactsSidebar="toggleChatSidebar(true)" @showProfileSidebar="showProfileSidebar" @toggleIsChatPinned="toggleIsChatPinned" />
                </div>
                <component :is="scrollbarTag" class="chat-content-scroll-area border border-solid d-theme-border-grey-light" :settings="settings" ref="chatLogPS" :key="$vs.rtl">
                    <div class="tLog">
                        <ChatLog :chatMessages="chatMessages" />
                    </div>
                </component>
                <div class="chat__input flex p-4 bg-white">
                    <vs-input class="flex-1" placeholder="Type Your Message" v-model="typedMessage" @keyup.enter="sendMsg" />
                    <vs-button class="bg-primary-gradient ml-4" type="filled" @click="sendMsg">Send</vs-button>
                </div>
            </template>
            <template v-else>
                <div class="flex flex-col items-center">
                    <feather-icon icon="MessageSquareIcon" class="mb-4 bg-white p-8 shadow-md rounded-full" svgClasses="w-16 h-16"></feather-icon>
                    <h4 class=" py-2 px-4 bg-white shadow-md rounded-full cursor-pointer" @click.stop="toggleChatSidebar(true)">Start Conversation</h4>
                </div>
            </template>
        </div>
    </div>
</template>

<script>
import VuePerfectScrollbar from 'vue-perfect-scrollbar'

import Dropin from 'dropin'

export default {
  name: 'Chat',
  components: {
    VuePerfectScrollbar,
  },
  props: {
    user: { type: Object, required: true },
    chatMessages: { type: Array, required: true },
  },
  computed: {
    scrollbarTag() {
      return this.$store.getters.scrollbarTag
    },
  },
  data () {
    return {
      dropin: {},

      clickNotClose: true,
      settings             : {
        maxScrollbarLength : 60,
        wheelSpeed         : 0.70
      },
      typedMessage: '',
      isChatPinned: false,
    }
  },
  methods: {
    async sendMsg() {
      this.$vs.loading()

      let body = this.typedMessage
      let obj = await this.dropin.sendLeadMessage(this.user.id, body)
      if (obj) {
        this.chatMessages.push({
          id: obj.id,
          fromMe: true,
          body: body,
          datetime: new Date(),
        })

        await this.setLastMessage(obj.id)
      } else {
        this.$vs.notify({
          title: 'Error',
          text: 'Cannot sent the message',
          iconPack: 'feather',
          icon: 'icon-message-circle',
          color: 'danger'
        })
      }
      this.typedMessage = ''

      this.$vs.loading.close()
    },
    async setLastMessage(messageId) {
      let info = this.user.info
      if (info === null) {
        info = {}
      }
      let whatsapp = {}
      if (info.hasOwnProperty('whatsapp')) {
        whatsapp = info.whatsapp
      }
      whatsapp.last_message = messageId
      whatsapp.last_message_read = messageId
      this.user.info = info
      await this.dropin.updateLead(this.user.id, {
        info: JSON.stringify(info),
      })
    },
    showProfileSidebar() {
      // TODO: HERE
    },
    toggleIsChatPinned() {
      // TODO: HERE
    },
  },
  async created() {
    this.dropin = new Dropin(localStorage.getItem('accessToken'))

    if (this.chatMessages.length > 0) {
      this.$vs.loading()
      let messageId = this.chatMessages[this.chatMessages.length - 1].id
      await this.setLastMessage(messageId)
      this.$vs.loading.close()
    }
  },
}
</script>

<style lang="scss" scoped>
@import "@sass/vuexy/apps/chat.scss";

#chatMain {
  width: 100%;
  margin-left: 0px !important;
  margin-right: 0px !important;
}

</style>
