import { createRouter, createWebHistory } from "vue-router";
import HomeView from "../views/HomeView.vue";
import UAPView from "@/views/UAPView.vue";
import FormateurView from "@/views/FormateurView.vue"
import ChangerPasswordView from "@/views/ChangerPasswordView.vue";
import AdminView from "@/views/AdminView.vue";
import ProfileAdmin from "@/components/Admin/ProfileAdmin.vue";

const routes = [
  {
    path: "/",
    name: "home",
    component: HomeView,
  },
  {
    path: "/ChangerPasswordView",
    name: "ChangerPasswordView",
    component: ChangerPasswordView,
  },
  {
    path: '/UAPView/:id',
    name: "UAPView",
    component: UAPView,
    props: true,
  },
  {
    path: "/FormateurView",
    name: "FormateurView",
    component: FormateurView,
  },
  {
    path: "/AdminView",
    name: "AdminView",
    component: AdminView,
  },
  {
    path: "/ProfileAdmin",
    name: "ProfileAdmin",
    component: ProfileAdmin,
  },

];

const router = createRouter({
  history: createWebHistory(process.env.BASE_URL),
  routes,
});


export default router;
