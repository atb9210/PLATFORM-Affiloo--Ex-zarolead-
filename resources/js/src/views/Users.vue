<!-- =========================================================================================
    File Name: Table.vue
    Description: Table demo - Imports all table demos
    ----------------------------------------------------------------------------------------
    Item Name: Vuexy - Vuejs, HTML & Laravel Admin Dashboard Template
      Author: Pixinvent
    Author URL: http://www.themeforest.net/user/pixinvent
========================================================================================== -->


<template>
    <div id="table-demo">
        <users-table :users="usersAdjusted"></users-table>
    </div>
</template>

<script>
import UsersTable from './ui-elements/table/UsersTable.vue'

export default{
  components: {
    UsersTable
  },
  data() {
      return {
          users: [],
          usersAdjusted: [],
      }
  },
  methods: {
    adjustUsersArray() {
      var roles = [ 'admin', 'mediabuyer', 'affiliate', 'advertiser', 'accountant' ]
      this.users.forEach((user) => {
        var u = {
            id: 0,
            name: '',
            email: '',
            roles: {
            },
            created_at: '',
            updated_at: '',
        }
        u.id = user.id
        u.name = user.name
        u.email = user.email
        roles.forEach((role) => {
          if (user.roles.includes(role)) {
            u.roles[role] = true
          } else {
            u.roles[role] = false
          }
        })
        u.created_at = user.created_at
        u.updated_at = user.updated_at
        this.usersAdjusted.push(u)
      })
      console.log(this.usersAdjusted)
    },
  },
  created() {
      this.$http.get('/api/v1/users', { headers: { Authorization: 'Bearer ' + localStorage.getItem('accessToken') } })
        .then((response) => {
          this.users = response.data.data
          this.adjustUsersArray()
        })
        .catch()
  }
}
</script>
