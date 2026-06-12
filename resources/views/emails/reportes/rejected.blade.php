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
                <table role="presentation" width="600" cellspacing="0" cellpadding="0" style="max-width:600px; width:100%; background:#ffffff; border:1px solid #e5e7eb;">
                    <tr>
                        <td style="padding:24px; border-top:4px solid #9D2449;">
                            <h1 style="margin:0 0 12px; font-size:22px; color:#404041;">Reporte rechazado</h1>
                            <p style="margin:0 0 16px; font-size:15px; line-height:1.5;">
                                {{ $rejector->name }} rechazo el reporte <strong>{{ $publication->topic }}</strong>.
                            </p>

                            <p style="margin:0 0 6px; font-size:14px; font-weight:bold;">Motivo:</p>
                            <div style="margin:0 0 20px; padding:12px; background:#f9fafb; border-left:4px solid #9D2449; font-size:14px; line-height:1.5;">
                                {{ $reason }}
                            </div>

                            <p style="margin:0 0 20px; font-size:14px; line-height:1.5;">
                                Entra al sistema para revisar el reporte y realizar los ajustes necesarios.
                            </p>

                            <a href="{{ $reportUrl }}" style="display:inline-block; padding:10px 16px; background:#611132; color:#ffffff; text-decoration:none; font-size:14px; font-weight:bold;">
                                Ver reporte
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:16px 24px; background:#f9fafb; color:#6b7280; font-size:12px;">
                            Este correo es una notificacion automatica del Sistema SEC TAM.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
