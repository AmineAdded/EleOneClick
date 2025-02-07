
 <template>
  <v-card 
    rounded="xl" 
    elevation="12" 
    class="professional-slide-container"
  >
    <v-window v-model="onboarding" class="position-relative">
      <v-window-item
        v-for="n in length"
        :key="`card-${n}`"
        :value="n"
      >
        <v-card
          class="slide-card d-flex justify-center align-center"
          :style="{
            backgroundImage: `linear-gradient(to bottom, rgba(0,86,179,0.7), rgba(0,86,179,0.9)), url(${require(`@/assets/images/EleOneTech${onboarding}.png`)})`,
            backgroundSize: 'cover',
            backgroundPosition: 'center'
          }"
        >
          <div class="slide-content text-center">
            <h2 class="slide-title mb-6 text-white">{{ getSlideTitle(n) }}</h2>
            <p class="slide-description text-subtitle-1 text-white font-weight-light">
              {{ getSlideDescription(n) }}
            </p>
          </div>
        </v-card>
      </v-window-item>
    </v-window>

    <v-card-actions class="slide-navigation px-6 pb-6">
      <v-btn 
        icon 
        variant="outlined" 
        color="white"
        @click="prev" 
        class="nav-btn mr-4"
      >
        <v-icon>mdi-chevron-left</v-icon>
      </v-btn>
      
      <v-item-group
        v-model="onboarding"
        class="slide-dots"
        mandatory
      >
        <v-item
          v-for="n in length"
          :key="`btn-${n}`"
          v-slot="{ isSelected, toggle }"
          :value="n"
        >
          <v-btn
            :variant="isSelected ? 'elevated' : 'text'"
            icon
            size="small"
            @click="toggle"
            class="dot-btn"
            :color="isSelected ? 'primary' : 'white'"
          ></v-btn>
        </v-item>
      </v-item-group>
      
      <v-btn 
        icon 
        variant="outlined" 
        color="white"
        @click="next" 
        class="nav-btn ml-4"
      >
        <v-icon>mdi-chevron-right</v-icon>
      </v-btn>
    </v-card-actions>
  </v-card>
</template>

<script>
export default {
  name: "ProfessionalSlides",
  data: () => ({
    length: 3,
    onboarding: 1,
    intervalId: null,
  }),
  methods: {
    next() {
      this.onboarding = this.onboarding + 1 > this.length ? 1 : this.onboarding + 1;
    },
    prev() {
      this.onboarding = this.onboarding - 1 <= 0 ? this.length : this.onboarding - 1;
    },
    getSlideTitle(n) {
      const titles = [
        'Transformation Digitale',
        'Innovation Technologique',
        'Solutions Intelligentes'
      ];
      return titles[n - 1];
    },
    getSlideDescription(n) {
      const descriptions = [
        'Optimisez votre entreprise avec nos solutions digitales avancées.',
        'Restez à la pointe avec nos technologies de dernière génération.',
        'Des solutions intelligentes pour propulser votre entreprise vers l\'avenir.'
      ];
      return descriptions[n - 1];
    },
    startAutoSlide() {
      this.intervalId = setInterval(this.next, 5000);
    },
    stopAutoSlide() {
      if (this.intervalId) {
        clearInterval(this.intervalId);
        this.intervalId = null;
      }
    },
  },
  mounted() {
    this.startAutoSlide();
  },
  beforeUnmount() {
    this.stopAutoSlide();
  },
}
</script>

<style scoped>
.professional-slide-container {
  position: relative;
  border-radius: 24px;
  overflow: hidden;
  box-shadow: 0 15px 35px rgba(0,0,0,0.15);
}

.slide-card {
  height: 675px;
  position: relative;
  transition: all 0.5s ease;
}

.slide-content {
  max-width: 800px;
  padding: 3rem;
  background: rgba(0,86,179,0.8);
  border-radius: 24px;
  backdrop-filter: blur(10px);
  transform: scale(0.95);
  opacity: 0.9;
  transition: all 0.4s ease;
}

.slide-title {
  font-size: 3rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 2px;
  color: white;
}

.slide-description {
  font-size: 1.3rem;
  line-height: 1.7;
  color: rgba(255,255,255,0.9);
}

.slide-navigation {
  position: absolute;
  bottom: 30px;
  left: 0;
  right: 0;
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 10;
}

.nav-btn {
  transition: all 0.3s ease;
}

.nav-btn:hover {
  transform: scale(1.1);
}

.slide-dots {
  display: flex;
  justify-content: center;
  align-items: center;
}

.dot-btn {
  margin: 0 8px;
  transition: all 0.3s ease;
}
</style>