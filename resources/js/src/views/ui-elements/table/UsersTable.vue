<!-- =========================================================================================
    File Name: TableMiscellaneous.vue
    Description: Combine filter,sorter, pagination etc.
    ----------------------------------------------------------------------------------------
    Item Name: Vuexy - Vuejs, HTML & Laravel Admin Dashboard Template
      Author: Pixinvent
    Author URL: http://www.themeforest.net/user/pixinvent
========================================================================================== -->


<template>
    <vx-card title="Miscellaneous" code-toggler>

        <p>You can add a <strong>filtered, sorted, paginated and multi selected</strong> table with combination of the <code>search</code>, <code>multiple</code> and <code>pagination</code> properties for example</p><br>
        <vs-dropdown vs-trigger-click class="cursor-pointer">
            <div class="p-4 border border-solid d-theme-border-grey-light rounded-full d-theme-dark-bg cursor-pointer flex items-center justify-between font-medium">
                <span class="mr-2">{{ currentPage * maxItems - (maxItems - 1) }} - {{ users.length - currentPage * maxItems > 0 ? currentPage * maxItems : users.length }} of {{ users.length }}</span>
                <feather-icon icon="ChevronDownIcon" svgClasses="h-4 w-4" />
            </div>
            <vs-dropdown-menu>
                <vs-dropdown-item @click="maxItems = 20">
                    <span>20</span>
                </vs-dropdown-item>
                <vs-dropdown-item @click="maxItems = 50">
                    <span>50</span>
                </vs-dropdown-item>
                <vs-dropdown-item @click="maxItems = 100">
                    <span>100</span>
                </vs-dropdown-item>
                <vs-dropdown-item @click="maxItems = 150">
                    <span>150</span>
                </vs-dropdown-item>
            </vs-dropdown-menu>
        </vs-dropdown>

        <vs-table multiple v-model="selected" :max-items="maxItems" pagination search :data="users" :currentPage="currentPage" @change-page="onPageChanged">

            <template slot="thead">
                <vs-th sort-key="email">Email</vs-th>
                <vs-th sort-key="username">Name</vs-th>
                <vs-th sort-key="roles">Roles</vs-th>
                <vs-th sort-key="website">Registration date</vs-th>
                <vs-th sort-key="id">ID</vs-th>
            </template>

            <template slot-scope="{data}">
                <vs-tr :data="tr" :key="indextr" v-for="(tr, indextr) in data">

                    <vs-td :data="data[indextr].email">
                        {{data[indextr].email}}
                    </vs-td>

                    <vs-td :data="data[indextr].username">
                        {{data[indextr].name}}
                    </vs-td>

                    <vs-td :data="data[indextr].id">
                        <ul class="demo-alignment" >
                            <li v-for="(value, role) in data[indextr].roles">
                                <label>{{ role }}</label>
                                <vs-switch v-model="data[indextr].roles[role]" @input="changeRole(data[indextr].id, role)" />
                            </li>
                        </ul>
                    </vs-td>

                    <vs-td :data="data[indextr].created_at">
                        {{data[indextr].created_at}}
                    </vs-td>

                    <vs-td :data="data[indextr].id">
                        {{data[indextr].id}}
                    </vs-td>

                </vs-tr>
            </template>
        </vs-table>

    </vx-card>
</template>

<script>
export default {
  props: {
      users: Array,
  },
  data() {
    return {
      selected: [],
      tableList: [
        'vs-th: Component',
        'vs-tr: Component',
        'vs-td: Component',
        'thread: Slot',
        'tbody: Slot',
        'header: Slot'
      ],
      maxItems: 20,
      currentPage: 1,
      totalPages: 3,
      maxPageNumbers: 100,
    }
  },
  methods: {
    changeRole(id, role) {
      var index = this.getUserById(id)
      if (index == -1) {
        return
      }
      var user = this.users[index]
      var data = {}
      data[role] = user.roles[role]

      this.$http.post('/api/v1/users/' + user.id + '/change-roles', data, {
        headers: { Authorization: 'Bearer ' + localStorage.getItem('accessToken') },
      }).then((response) => {
        this.$vs.notify({
          title: 'Success',
          text: 'Roles changed',
          iconPack: 'feather',
          icon: 'icon-alert-circle',
          color: 'success'
        })
      }).catch((error) => {
        this.users[index].roles[role] = !this.users[index].roles[role]

        this.$vs.notify({
          title: 'Error',
          text: error.response.data.message,
          iconPack: 'feather',
          icon: 'icon-alert-circle',
          color: 'danger'
        })
      });
    },
    onPageChanged(val) {
      this.currentPage = val
    },
    getUserById(id) {
      var index = -1;
      this.users.forEach((user, i) => {
        if (user.id == id) {
          index = i
          return
        }
      })
      return index
    },
  },
}
</script>
