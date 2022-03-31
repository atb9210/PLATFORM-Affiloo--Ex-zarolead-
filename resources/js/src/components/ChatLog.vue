<template>
    <div id="component-chat-log" class="m-8" v-if="chatMessages">
        <div v-for="(msg, index) in chatMessages" class="msg-grp-container" :key="index">

            <!-- If previous msg is older than current time -->
            <template v-if="chatMessages[index-1]">
                <vs-divider v-if="!isSameDay(msg.datetime, chatMessages[index-1].datetime)" class="msg-time">
                    <span>{{ toDate(msg.datetime) }}</span>
                </vs-divider>
                <div class="spacer mt-8" v-if="!hasSentPreviousMsg(chatMessages[index-1].fromMe, msg.fromMe)"></div>
            </template>

            <div class="flex items-start" :class="[{'flex-row-reverse' : msg.fromMe}]">

                <template v-if="chatMessages[index-1]">
                    <template v-if="(!hasSentPreviousMsg(chatMessages[index-1].fromMe, msg.fromMe) || !isSameDay(msg.datetime, chatMessages[index-1].datetime))">
                        <vs-avatar size="40px" class="border-2 shadow border-solid border-white m-0 flex-shrink-0" :class="msg.fromMe ? 'sm:ml-5 ml-3' : 'sm:mr-5 mr-3'" :src="senderImg(msg.fromMe)"></vs-avatar>
                    </template>
                </template>

                <template v-if="index == 0">
                    <vs-avatar size="40px" class="border-2 shadow border-solid border-white m-0 flex-shrink-0" :class="msg.fromMe ? 'sm:ml-5 ml-3' : 'sm:mr-5 mr-3'" :src="senderImg(msg.fromMe)"></vs-avatar>
                </template>

                <template v-if="chatMessages[index-1]">
                    <div class="mr-16" v-if="!(!hasSentPreviousMsg(chatMessages[index-1].fromMe, msg.fromMe) || !isSameDay(msg.datetime, chatMessages[index-1].datetime))"></div>
                </template>

                <div class="msg break-words relative shadow-md rounded py-3 px-4 mb-2 rounded-lg max-w-sm" :class="{'bg-primary-gradient text-white': msg.fromMe, 'border border-solid border-transparent bg-white': !msg.fromMe}">
                    <span class="text-lg">{{ msg.body }}</span>
                    <span class="text-xs italic">{{ msg.date_obj.getHours() + ':' + msg.date_obj.getMinutes().toString().padStart(2, '0') }}</span>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
  name: 'ChatLog',
  props: {
    chatMessages: { type: Array, required: true },
  },
  computed: {
    activeUserImg () {
      //return this.$store.state.AppActiveUser.photoURL
      return 'https://iupac.org/wp-content/uploads/2018/05/default-avatar.png'
    },
    senderImg () {
      /*
      return (isSentByActiveUser) => {
        if (isSentByActiveUser) return this.$store.state.AppActiveUser.photoURL
        else return this.$store.getters['chat/contact'](this.userId).photoURL
      }
      */
      return (_) => 'https://iupac.org/wp-content/uploads/2018/05/default-avatar.png'
    },
    hasSentPreviousMsg () {
      return (last_sender, current_sender) => last_sender === current_sender
    }
  },
  data () {
    return {
    }
  },
  methods: {
    isSameDay (time_to, time_from) {
      const date_time_to = new Date(Date.parse(time_to))
      const date_time_from = new Date(Date.parse(time_from))
      return date_time_to.getFullYear() === date_time_from.getFullYear() &&
                date_time_to.getMonth() === date_time_from.getMonth() &&
                date_time_to.getDate() === date_time_from.getDate()
    },
    toDate (time) {
      const locale = 'it-it'
      const date_obj = new Date(Date.parse(time))
      const monthName = date_obj.toLocaleString(locale, {
        month: 'short'
      })
      return `${date_obj.getDate()  } ${   monthName}`
    },
    scrollToBottom () {
      this.$nextTick(() => {
        this.$parent.$el.scrollTop = this.$parent.$el.scrollHeight
      })
    }
  },
  created () {
  },
  updated () {
    this.scrollToBottom()

    // DEBUG TODO: to delete
    console.log(this.chatMessages)
  },
  mounted () {
    this.scrollToBottom()
  }
}
</script>
