
<template>
  <div class="contact-form-container" :class="{ 'rail-triggered': triggeredRail }">
    <div class="form-wrapper">
      <h2 class="form-title">Contactez-nous</h2>
      
      <div class="form-group">
        <div class="icon-wrapper">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="input-icon">
            <path d="M12 6a3.5 3.5 0 1 1-3.5 3.5A3.5 3.5 0 0 1 12 6m0-2a5.5 5.5 0 1 0 5.5 5.5A5.5 5.5 0 0 0 12 4Zm7.5 13.5c0 1.38-2.46 2.5-5.5 2.5s-5.5-1.12-5.5-2.5 2.46-2.5 5.5-2.5 5.5 1.12 5.5 2.5ZM3 17.5c0 1.38 2.46 2.5 5.5 2.5v-2c-1.84 0-3.5-.62-3.5-1.5h-2Zm18 0c0 1.38-2.46 2.5-5.5 2.5v-2c1.84 0 3.5-.62 3.5-1.5h2Z"/>
          </svg>
        </div>
        <input 
          type="text" 
          placeholder="Votre nom" 
          class="form-input" 
          v-model="name"
        />
      </div>

      <div class="form-group">
        <div class="icon-wrapper">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="input-icon">
            <path d="M22 6c0-1.1-.9-2-2-2H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6zm-2 0-8 5-8-5h16zm0 12H4V8l8 5 8-5v10z"/>
          </svg>
        </div>
        <input 
          type="email" 
          placeholder="Votre email" 
          class="form-input" 
          v-model="email"
        />
      </div>

      <div class="form-group">
        <div class="icon-wrapper">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="input-icon">
            <path d="M13 7h-2v4H7v2h4v4h2v-2h4v-2h-4V7zm-1-5C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z"/>
          </svg>
        </div>
        <textarea 
          placeholder="Suggestion" 
          class="form-textarea" 
          v-model="message"
        ></textarea>
      </div>

      <div class="button-group">
        <button 
          class="btn btn-primary" 
          @click="sendMessage"
        >
          Envoyer
        </button>
        <button 
          class="btn btn-reset" 
          @click="resetForm"
        >
          Réinitialiser
        </button>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "ContactForm",
  props: {
    triggeredRail: {
      type: Boolean,
      required: true
    }
  },
  data() {
    return {
      name: '',
      email: '',
      message: ''
    }
  },
  methods: {
    validateEmail(email) {
      const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
      return re.test(email)
    },
    sendMessage() {
      // Added basic validation
      if (this.name.trim() && this.validateEmail(this.email) && this.message.trim()) {
        console.log('Sending message:', {
          name: this.name,
          email: this.email,
          message: this.message
        })
        this.$emit('message-sent', {
          name: this.name,
          email: this.email,
          message: this.message
        })
        this.resetForm()
      } else {
        // Optional: Add error handling or validation feedback
        alert('Veuillez remplir tous les champs correctement')
      }
    },
    resetForm() {
      this.name = ''
      this.email = ''
      this.message = ''
    }
  }
}
</script>

<style scoped>
.contact-form-container {
  --primary-color: #0056b3;
  --secondary-color: #f4f6ff;
  --text-color: #333;
  --border-color: #e0e0e0;
  --hover-color: #003d82;

  background: linear-gradient(145deg, var(--secondary-color), #ffffff);
  border-radius: 16px;
  box-shadow: 
    10px 10px 20px rgba(0, 0, 0, 0.05), 
    -10px -10px 20px rgba(255, 255, 255, 0.9);
  max-width: 520px;
  margin: 2rem auto;
  padding: 2.5rem;
  transition: all 0.4s ease;
}

.contact-form-container:hover {
  transform: translateY(-5px);
  box-shadow: 
    12px 12px 24px rgba(0, 0, 0, 0.08), 
    -12px -12px 24px rgba(255, 255, 255, 0.95);
}

.contact-form-container.rail-triggered {
  transform: translateX(-90px);
}

.form-title {
  color: var(--primary-color);
  text-align: center;
  margin-bottom: 2.5rem;
  font-size: 2rem;
  font-weight: 800;
  letter-spacing: -0.5px;
}

.form-group {
  position: relative;
  margin-bottom: 1.8rem;
}

.icon-wrapper {
  position: absolute;
  left: 15px;
  top: 50%;
  transform: translateY(-50%);
}

.input-icon {
  width: 26px;
  height: 26px;
  fill: var(--primary-color);
  opacity: 0.6;
  transition: opacity 0.3s ease;
}

.form-input:focus + .icon-wrapper .input-icon,
.form-textarea:focus + .icon-wrapper .input-icon {
  opacity: 1;
}

.form-input, 
.form-textarea {
  width: 100%;
  padding: 14px 14px 14px 50px;
  border: 2px solid var(--border-color);
  border-radius: 10px;
  font-size: 1rem;
  transition: all 0.3s ease;
  background-color: #ffffff;
}

.form-input:focus, 
.form-textarea:focus {
  border-color: var(--primary-color);
  outline: none;
  box-shadow: 0 0 0 4px rgba(106, 74, 255, 0.1);
}

.form-textarea {
  min-height: 180px;
  resize: vertical;
  line-height: 1.6;
}

.button-group {
  display: flex;
  gap: 1.2rem;
  margin-top: 1rem;
}

.btn {
  flex: 1;
  padding: 14px;
  border-radius: 10px;
  font-weight: 700;
  transition: all 0.3s ease;
  border: none;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.btn-primary {
  background-color: var(--primary-color);
  color: white;
  box-shadow: 0 4px 15px rgba(106, 74, 255, 0.3);
}

.btn-primary:hover {
  background-color: var(--hover-color);
  transform: translateY(-2px);
  box-shadow: 0 6px 18px rgba(106, 74, 255, 0.4);
}

.btn-reset {
  background-color: transparent;
  border: 2px solid var(--primary-color);
  color: var(--primary-color);
}

.btn-reset:hover {
  background-color: var(--primary-color);
  color: white;
}
</style>
