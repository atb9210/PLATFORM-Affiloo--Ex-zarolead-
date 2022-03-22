/*=========================================================================================
  File Name: globalComponents.js
  Description: Here you can register components globally
  ----------------------------------------------------------------------------------------
  Item Name: Vuexy - Vuejs, HTML & Laravel Admin Dashboard Template
  Author: Pixinvent
  Author URL: http://www.themeforest.net/user/pixinvent
==========================================================================================*/


import Vue from 'vue'
import VxTooltip from './layouts/components/vx-tooltip/VxTooltip.vue'
import VxCard  from './components/vx-card/VxCard.vue'
import VxList  from './components/vx-list/VxList.vue'
import VxBreadcrumb  from './layouts/components/VxBreadcrumb.vue'
import FeatherIcon  from './components/FeatherIcon.vue'
import VxInputGroup  from './components/vx-input-group/VxInputGroup.vue'

Vue.component(VxTooltip.name, VxTooltip)
Vue.component(VxCard.name, VxCard)
Vue.component(VxList.name, VxList)
Vue.component(VxBreadcrumb.name, VxBreadcrumb)
Vue.component(FeatherIcon.name, FeatherIcon)
Vue.component(VxInputGroup.name, VxInputGroup)

// NOTE: Custom (created by driverfury)
import MySelect from './components/MySelect.vue'
Vue.component(MySelect.name, MySelect)
import EditableValue from './components/EditableValue.vue'
Vue.component(EditableValue.name, EditableValue)
import FastEditableValue from './components/FastEditableValue.vue'
Vue.component(FastEditableValue.name, FastEditableValue)
import FastEditableValueSelect from './components/FastEditableValueSelect.vue'
Vue.component(FastEditableValueSelect.name, FastEditableValueSelect)
import PhoneInput from './components/PhoneInput.vue'
Vue.component(PhoneInput.name, PhoneInput)
import StatsCard from './components/StatsCard.vue'
Vue.component(StatsCard.name, StatsCard)
import LocationInput from './components/LocationInput.vue'
Vue.component(LocationInput.name, LocationInput)
import ChatNavbar from './components/ChatNavbar.vue'
Vue.component(ChatNavbar.name, ChatNavbar)
import ChatLog from './components/ChatLog.vue'
Vue.component(ChatLog.name, ChatLog)
import Chat from './components/Chat.vue'
Vue.component(Chat.name, Chat)
import CustomerBuilderFormComponent from './components/CustomerBuilderFormComponent.vue'
Vue.component(CustomerBuilderFormComponent.name, CustomerBuilderFormComponent)
import CustomerBuilderComponent from './components/CustomerBuilderComponent.vue'
Vue.component(CustomerBuilderComponent.name, CustomerBuilderComponent)


// v-select component
import vSelect from 'vue-select'

// Set the components prop default to return our fresh components
vSelect.props.components.default = () => ({
  Deselect: {
    render: createElement => createElement('feather-icon', {
      props: {
        icon: 'XIcon',
        svgClasses: 'w-4 h-4 mt-1'
      }
    })
  },
  OpenIndicator: {
    render: createElement => createElement('feather-icon', {
      props: {
        icon: 'ChevronDownIcon',
        svgClasses: 'w-5 h-5'
      }
    })
  }
})

Vue.component(vSelect)
