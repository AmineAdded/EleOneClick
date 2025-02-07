@component('mail::message')
<style>
  /* Styles globaux */
  body {
    margin: 0;
    padding: 0;
    background-color: #f8fafc;
    font-family: 'Segoe UI', Arial, sans-serif;
    line-height: 1.6;
  }

  /* Container principal */
  .email-container {
    max-width: 600px;
    margin: 20px auto;
    background-color: #ffffff;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    padding: 40px;
  }

  /* Logo container */
  .logo-container {
    text-align: center;
    margin-bottom: 30px;
  }

  .logo {
    width: 120px;
    height: auto;
  }

  /* En-tête */
  h1 {
    color: #1a365d;
    font-size: 24px;
    font-weight: 600;
    text-align: center;
    margin-bottom: 30px;
    line-height: 1.3;
  }

  /* Texte principal */
  p {
    color: #4a5568;
    font-size: 16px;
    margin: 16px 0;
    text-align: center;
  }

  /* Panel pour le code */
  .token-panel {
    background-color: #f7fafc;
    border: 2px solid #e2e8f0;
    border-radius: 8px;
    padding: 20px;
    margin: 25px 0;
  }

  .token {
    font-family: 'Courier New', monospace;
    font-size: 28px;
    font-weight: bold;
    color: #2d3748;
    letter-spacing: 3px;
  }

  /* Message d'expiration */
  .expiration-message {
    background-color: #ebf8ff;
    color: #2c5282;
    padding: 12px;
    border-radius: 6px;
    font-weight: 500;
    margin: 20px 0;
    text-align: center;
  }

  /* Footer */
  .footer {
    margin-top: 40px;
    padding-top: 20px;
    border-top: 1px solid #e2e8f0;
    text-align: center;
    color: #718096;
    font-size: 14px;
  }

  /* Bouton optionnel */
  .action-button {
    display: inline-block;
    background-color: #4299e1;
    color: white;
    padding: 12px 24px;
    border-radius: 6px;
    text-decoration: none;
    font-weight: 500;
    margin: 20px 0;
    transition: background-color 0.2s;
  }

  .action-button:hover {
    background-color: #3182ce;
  }
</style>

<div class="email-container">

  <h1>Réinitialisation de votre mot de passe</h1>

  <p>Nous avons reçu une demande de réinitialisation de mot de passe pour votre compte. Voici votre code de vérification :</p>

  <div class="token-panel">
    <span class="token">{{ $token }}</span>
  </div>

  <div class="expiration-message">
    ⏱️ Ce code est valide pendant 1 heure à compter de la réception de cet email
  </div>

  <p>Si vous n'avez pas initié cette demande de réinitialisation, nous vous conseillons de :</p>
  <p>1. Ignorer cet email<br>
     2. Sécuriser votre compte en vérifiant vos paramètres de sécurité</p>

  <div class="footer">
    <p>Cet email a été envoyé automatiquement, merci de ne pas y répondre.<br>
    © 2025 Votre Entreprise. Tous droits réservés.</p>
  </div>
</div>
@endcomponent