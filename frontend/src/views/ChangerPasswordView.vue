<template>
    <div>
      <HomeView />
    </div>
  </template>
  <script>
import { defineComponent } from "vue";
import HomeView from "./HomeView.vue";
  export default defineComponent({
    name: "ChangerPasswordView",
    components: {
        HomeView,
    },
  });
  </script>
  