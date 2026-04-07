{!! App\Mail\Templates\PlantillaBase::render('
<div style="text-align: center; font-family: sans-serif;">
    <table role="presentation" cellpadding="0" cellspacing="0" style="margin: 0 auto 20px auto; width: 80px;">
        <tr>
            <td align="center" valign="middle" style="background-color: #fee2e2; border-radius: 50%; width: 80px; height: 80px; text-align: center;">
                <span style="font-size: 40px; line-height: 80px; display: block;">🚫</span>
            </td>
        </tr>
    </table>
    <h2 style="color: #dc3545; margin: 0 0 10px 0;">Pedido Cancelado</h2>
</div>

<p style="color: #64748b; font-size: 16px; margin-bottom: 25px;">
    Hola <strong>' . htmlspecialchars($cliente_nombre) . '</strong>,
</p>

<p style="color: #334155; line-height: 1.6; margin-bottom: 20px;">
    Tu pedido ha sido <strong style="color: #dc3545;">CANCELADO</strong> exitosamente.
</p>

<div style="background-color: #f8fafc; padding: 25px; border-radius: 12px; margin: 25px 0; border-left: 4px solid #dc3545;">
    <h4 style="margin: 0 0 15px 0; color: #1e293b; font-size: 16px;">Detalles de la cancelación:</h4>
    <p style="margin: 5px 0;"><strong>📄 Folio:</strong> ' . $folio . '</p>
    <p style="margin: 5px 0;"><strong>💰 Total:</strong> $' . number_format($total, 2) . '</p>
    <p style="margin: 5px 0;"><strong>📅 Fecha de cancelación:</strong> ' . $fecha_cancelacion . '</p>
</div>

<p style="color: #555; line-height: 1.6;">
    Si no solicitaste esta cancelación o tienes alguna duda, por favor contáctanos inmediatamente.
</p>

<div style="text-align: center; margin: 30px 0;">
    <a href="' . route('cliente.pedidos') . '" 
       style="display: inline-block; background-color: #7fad39; color: white; padding: 14px 28px; 
              text-decoration: none; border-radius: 8px; font-weight: 600;">
        Ver mis pedidos
    </a>
</div>

<p style="color: #64748b; font-size: 12px; margin-top: 30px; text-align: center;">
    ¿Tienes dudas? Contáctanos al <strong>55 4017 5803</strong>
</p>
') !!}