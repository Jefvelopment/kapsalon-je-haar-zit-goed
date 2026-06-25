<div style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto;">
    <div style="background-color: #6B8E23; color: white; padding: 20px; border-radius: 8px 8px 0 0;">
        <h1 style="margin: 0; font-size: 24px;">Nieuw Contactformulier</h1>
    </div>
    
    <div style="background-color: #f9f9f9; padding: 20px; border: 1px solid #ddd; border-radius: 0 0 8px 8px;">
        <p>Je hebt een nieuw bericht ontvangen via het contactformulier:</p>
        
        <div style="background-color: white; padding: 15px; border-left: 4px solid #6B8E23; margin: 15px 0;">
            <p><strong>Naam:</strong> {{ $name }}</p>
            <p><strong>Email:</strong> <a href="mailto:{{ $email }}">{{ $email }}</a></p>
            <p><strong>Telefoonnummer:</strong> {{ $phone }}</p>
            <p><strong>Onderwerp:</strong> {{ $subject }}</p>
            
            <hr style="border: none; border-top: 1px solid #ddd; margin: 15px 0;">
            
            <p><strong>Bericht:</strong></p>
            <p style="white-space: pre-wrap;">{{ $message }}</p>
        </div>
        
        <p style="color: #999; font-size: 12px;">
            Dit bericht is automatisch gegenereerd. Neem contact op met de klant via {{ $email }} of {{ $phone }}.
        </p>
    </div>
</div>
