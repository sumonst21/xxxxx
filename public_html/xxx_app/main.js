import Vue from 'vue'
import App from './App'

Vue.config.productionTip = false
import Common from './common/common.js'
import Api from './common/api.js'

App.mpType = 'app'

import faIcon from './components/kilvn-fa-icon/fa-icon.vue'
Vue.component('fa',faIcon)
const app = new Vue({
	Common,
	Api,
    ...App
})
app.$mount()
