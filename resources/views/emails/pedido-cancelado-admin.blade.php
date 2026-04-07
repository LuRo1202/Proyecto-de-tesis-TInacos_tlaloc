{!! App\Mail\Templates\PlantillaBase::render('
<div style="text-align: center; font-family: sans-serif;">
    <table role="presentation" cellpadding="0" cellspacing="0" style="margin: 0 auto 20px auto; width: 80px;">
        <tr>
            <td align="center" valign="middle" style="background-color: #fee2e2; border-radius: 50%; width: 80px; height: 80px; text-align: center;">
                <span style="font-size: 40px; line-height: 80px; display: block;">⚠️</span>
            </td>
        </tr>
    </table>
    <h2 style="color: #dc3545; margin: 0 0 10px 0;">Pedido Cancelado</h2>
</div>

<p style="color: #64748b; font-size: 16px; margin-bottom: 25px;">
    Hola,
</p>

<p style="color: #334155; line-height: 1.6; margin-bottom: 20px;">
    El cliente <strong>' . htmlspecialchars($cliente_nombre) . '</strong> ha cancelado su pedido.
</p>

<div style="background-color: #f8fafc; padding: 25px; border-radius: 12px; margin: 25px 0; border-left: 4px solid #dc3545;">
    <h4 style="margin: 0 0 15px 0; color: #1e293b; font-size: 16px;">📋 Datos de la cancelación:</h4>
    <p style="margin: 5px 0;"><strong>📄 Folio:</strong> ' . $folio . '</p>
    <p style="margin: 5px 0;"><strong>💰 Total:</strong> $' . number_format($total, 2) . '</p>
    <p style="margin: 5px 0;"><strong>📅 Fecha de cancelación:</strong> ' . $fecha_cancelacion . '</p>
    <p style="margin: 5px 0;"><strong>👤 Cliente:</strong> ' . htmlspecialchars($cliente_nombre) . '</p>
    <p style="margin: 5px 0;"><strong>📧 Email:</strong> ' . $cliente_email . '</p>
    <p style="margin: 5px 0;"><strong>📞 Teléfono:</strong> ' . $cliente_telefono . '</p>
</div>



<div style="text-align: center; margin: 30px 0;">
    <a href="' . url('/gerente/pedidos') . '" 
       style="display: inline-block; background-color: #dc3545; color: white; padding: 14px 28px; 
              text-decoration: none; border-radius: 8px; font-weight: 600;">
        Ver pedidos cancelados
    </a>
</div>
') !!}