<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Reporte rechazado</title>
</head>
<body style="margin:0; padding:0; background:#f3f4f6; font-family:Arial, sans-serif; color:#404041;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#f3f4f6; padding:24px 0;">
        <tr>
            <td align="center">
                <table role="presentation" width="760" cellspacing="0" cellpadding="0" style="max-width:760px; width:100%; background:#ffffff; border:1px solid #e5e7eb;">
                    <tr>
                        <td style="padding:24px; border-top:4px solid #611132;">
                            <p style="margin:0 0 8px; color:#9D2449; font-size:11px; font-weight:bold; text-transform:uppercase;">
                                Notificación de reportes
                            </p>



                            <h1 style="margin:0 0 14px; font-size:22px; color:#404041;">Reporte rechazado</h1>
                            <p style="margin:0 0 18px; font-size:15px; line-height:1.55;">
                                <span style="font-style:italic; font-weight:600;">{{ $rejectorFullName }}</span>
                                rechazó el reporte
                                <span style="font-style:italic; font-weight:600;">“{{ $publication->topic }}”</span>.
                            </p>

                            <p style="margin:0 0 6px; color:#374151; font-size:13px; font-weight:bold;">Motivo del rechazo</p>
                            <div style="margin:0 0 18px; padding:14px 16px; background:#f9fafb; border-left:4px solid #611132; color:#374151; font-size:14px; line-height:1.55;">
                                {{ $reason }}
                            </div>

                            <p style="margin:0 0 22px; color:#4b5563; font-size:14px; line-height:1.55;">
                                Entra al sistema para revisar el reporte y realizar los ajustes necesarios.
                            </p>

                            <a href="{{ $reportUrl }}" style="display:inline-block; padding:11px 18px; background:#611132; color:#ffffff; text-decoration:none; font-size:14px; font-weight:bold;">
                                Ver reporte
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:14px 24px; background:#f9fafb; border-top:1px solid #e5e7eb; color:#6b7280; font-size:11px;">
                            Este correo es una notificación automática del Sistema SEC TAM.
                        </td>
                    </tr>
                    @include('emails.partials.privacy-notice')
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
