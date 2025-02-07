@component('mail::message')
<h1 style="font-size: 24px; font-weight: bold; color: #0348e8; text-align: center; margin-bottom: 20px;">
    Demande de réinitialisation de mot de passe
</h1>
<p style="font-size: 16px; color: #333; line-height: 1.5; text-align: center;">
    Nous avons bien reçu votre demande de réinitialisation du mot de passe pour votre compte.
</p>
<p style="font-size: 16px; color: #333; line-height: 1.5; text-align: center; margin-bottom: 20px;">
    Voici le code que vous pouvez utiliser pour récupérer l'accès à votre compte :
</p>

@component('mail::panel')
<p style="font-size: 18px; font-weight: bold; color: #0348e8; text-align: center;">
    {{ $token }}
</p>
@endcomponent

<p style="text-align: center; margin-top: 20px;">
    <a href="{{ env('FRONTEND_URL') }}/ChangerPasswordView"
       style="display: inline-block;
              background-color: #0348e8;
              color: #fff;
              font-size: 16px;
              font-weight: bold;
              padding: 12px 24px;
              text-decoration: none;
              border-radius: 5px;
              box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);">
        Réinitialiser le mot de passe
    </a>
</p>

<p style="font-size: 14px; color: #666; text-align: center; margin-top: 20px;">
    Ce code est valable pendant une durée de 1 heure à partir de l'envoi de ce message.
</p>
@endcomponent
