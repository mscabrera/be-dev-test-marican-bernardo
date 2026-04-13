import { createApp } from 'vue';
import App from './components/App.vue';
import vuetify from './vuetify'
import '@mdi/font/css/materialdesignicons.css'

const app = createApp(App)

app.use(vuetify)

app.mount('#app')