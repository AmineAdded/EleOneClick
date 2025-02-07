@component('mail::message')
<h1 style="font-size: 24px; font-weight: bold; color: #0348e8; text-align: center; margin-bottom: 20px;">
    🎉 Bienvenue sur notre plateforme, {{ $name }} !
</h1>

<p style="font-size: 16px; color: #333; line-height: 1.5; text-align: center;">
    Nous sommes ravis de vous accueillir parmi nous ! 🚀  
    Votre compte a bien été créé avec succès et vous pouvez maintenant profiter de tous nos services.
</p>

<p style="font-size: 16px; color: #333; line-height: 1.5; text-align: center; margin-bottom: 20px;">
    Cliquez sur le bouton ci-dessous pour accéder à votre espace personnel :
</p>

<p style="text-align: center; margin-top: 20px;">
    <a href="{{ env('FRONTEND_URL') }}"
       style="display: inline-block;
              background-color: #0348e8;
              color: #fff;
              font-size: 16px;
              font-weight: bold;
              padding: 12px 24px;
              text-decoration: none;
              border-radius: 5px;
              box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);">
        Accéder à mon compte
    </a>
</p>

<p style="font-size: 14px; color: #666; text-align: center; margin-top: 20px;">
    Si vous avez des questions, n'hésitez pas à nous contacter.  
    Nous vous souhaitons une excellente expérience sur notre plateforme ! 😊
</p>

<p style="font-size: 14px; color: #666; text-align: center; margin-top: 10px;">
    À bientôt,  
    L'équipe {{ config('app.name') }}
</p>
@endcomponent
