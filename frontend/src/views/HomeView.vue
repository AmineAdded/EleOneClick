<template>
    <div v-if="isLoading" class="loader-container">
        <div class="loader-content">
            <v-progress-circular
                indeterminate
                color="primary"
                size="64"
            ></v-progress-circular>
            <span class="loader-text">{{ loadingMessage }}</span>
        </div>
    </div>
  <div v-else style="display: grid; grid-template-columns: auto 1fr;">
    <section id="home">
      <NavBar :currentRoute="$route.path" @Rail="handleRail"/>
    </section>
    <section>
      <Slides />
      <hr id="grillePoly">
      <GrillePolyvalence />
      <hr id="contact">
      <Contact :triggeredRail="triggeredRail"/>
      <Footer />
    </section>
  </div>
</template>


<script>
import { AuthStore } from "@/store/auth";
import { defineComponent } from "vue";

// Components
 import NavBar from "../components/Home/NavBar.vue";
 import Slides from "../components/Home/Slides.vue";
import GrillePolyvalence from "../components/Home/GrillePolyvalence.vue";
import Contact from "../components/Home/Contact.vue";
import Footer from "../components/Home/Footer.vue";


export default defineComponent({
  name: "HomeView",
  components: {
    NavBar,
     Slides,
     GrillePolyvalence,
     Contact,
     Footer,
  },
  data(){
    return {
      triggeredRail: false,
      isLoading: true, 
    }
  },
  computed: {
        loadingMessage() {
            const auth = AuthStore();
            return auth.getStatusMessage || "Chargement en cours...";
        }
    },
    created() {
        setTimeout(() => {
            this.isLoading = false;
            const auth = AuthStore();
            auth.clearStatusMessage();  // Effacer le message après le chargement
        }, 800);
    },
  methods:{
    handleRail(){
      this.triggeredRail=!this.triggeredRail;
    }
  }
});
</script>
<style>
.loader-container {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    background: rgba(25, 118, 210, 0.03);
    z-index: 1001;
    backdrop-filter: blur(8px);
}

.loader-content {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 16px;
}

.loader-text {
    color: #1976d2;
    font-size: 1.2em;
    font-weight: 500;
    animation: pulse 2s infinite;
}
</style>
