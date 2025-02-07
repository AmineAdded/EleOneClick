
<template>
    <footer class="professional-footer">
      <div class="footer-content">
        <p class="copyright">
          &copy; {{ currentYear }} EleOne Technologies. 
          <span class="credits">
            Design inspiré par 
            <a href="#" target="_blank" class="credit-link">Sentra</a> | 
            Développement par 
            <a href="#" target="_blank" class="credit-link">ThemeWagon</a>
          </span>
        </p>
      </div>
    </footer>
  </template>
  
  <script>
  export default {
    name: 'ProfessionalFooter',
    computed: {
      currentYear() {
        return new Date().getFullYear();
      }
    }
  }
  </script>
  
  <style scoped>
  .professional-footer {
    background-color: #0056b3;
    color: #ffffff;
    text-align: center;
    padding: 20px;
    font-size: 14px;
    box-shadow: 0 -5px 15px rgba(0,0,0,0.1);
  }
  
  .footer-content {
    max-width: 1200px;
    margin: 0 auto;
  }
  
  .copyright {
    margin: 0;
    font-weight: 300;
  }
  
  .credits {
    margin-left: 10px;
  }
  
  .credit-link {
    color: #ffffff;
    text-decoration: none;
    font-weight: 500;
    transition: color 0.3s ease;
  }
  
  .credit-link:hover {
    color: #00c6ff;
    text-decoration: underline;
  }
  </style>

