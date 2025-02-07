import './plugins/axios'
import { createApp } from "vue";
import App from "./App.vue";
import router from "./router";
import store from "./store";
import vuetify from "./plugins/vuetify";
import { loadFonts } from "./plugins/webfontloader";
import "bootstrap/dist/css/bootstrap.min.css";
import "bootstrap/dist/js/bootstrap.bundle.min.js";
import { createPinia } from 'pinia'; 

loadFonts();

createApp(App).use(router).use(store).use(vuetify).use(createPinia()).mount("#app");
