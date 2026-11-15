<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $subject }}</title>
</head>
<body style="margin:0;padding:0;background:#f4f5f7;font-family:Arial,Helvetica,sans-serif;color:#1f2933;">
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#f4f5f7;padding:24px 0;">
    <tr>
        <td align="center">
            <table role="presentation" width="560" cellspacing="0" cellpadding="0"
                   style="max-width:560px;width:100%;background:#ffffff;border-radius:8px;overflow:hidden;">
                <tr>
                    {{-- Tenant branding. Values are sanitised (https logo, hex colour) and HTML escaped. --}}
                    <td style="padding:24px;border-bottom:4px solid {{ $branding['primary_color'] }};">
                        @if($branding['logo_url'])
                            <img src="{{ $branding['logo_url'] }}" alt="{{ $branding['display_name'] }}"
                                 style="max-height:48px;max-width:240px;display:block;">
                        @else
                            <strong style="font-size:20px;">{{ $branding['display_name'] }}</strong>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td style="padding:24px;font-size:15px;line-height:1.5;">
                        <p style="margin-top:0;">Hello {{ $recipient->name ?? '' }},</p>

                        @if($event === 'invited')
                            <p><strong>{{ $details['tenant_name'] }}</strong> invited
                                <strong>{{ $details['partner_name'] }}</strong> to join their partner program.</p>
                            @if(!empty($details['parent_name']))
                                <p>You would work under <strong>{{ $details['parent_name'] }}</strong>.</p>
                            @endif
                            @if(!empty($details['roles']))
                                <p>Roles offered:</p>
                                <ul>
                                    @foreach($details['roles'] as $role)
                                        <li>{{ $role }}</li>
                                    @endforeach
                                </ul>
                            @endif
                            <p>Until you accept, nothing changes for your account.</p>
                            @if(!empty($details['action_url']))
                                <p style="text-align:center;margin:28px 0;">
                                    <a href="{{ $details['action_url'] }}"
                                       style="background:{{ $branding['primary_color'] }};color:#ffffff;text-decoration:none;padding:12px 24px;border-radius:6px;display:inline-block;">
                                        Review invitation
                                    </a>
                                </p>
                            @endif
                        @elseif($event === 'accepted')
                            <p><strong>{{ $details['partner_name'] }}</strong> accepted your partnership invitation.
                                They are now an active partner.</p>
                        @elseif($event === 'rejected')
                            <p><strong>{{ $details['partner_name'] }}</strong> declined your partnership invitation.</p>
                        @elseif($event === 'revoked')
                            <p>The partnership between <strong>{{ $details['partner_name'] }}</strong> and
                                <strong>{{ $details['tenant_name'] }}</strong> has ended.</p>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td style="padding:16px 24px;background:#f9fafb;font-size:12px;color:#7b8794;">
                        {{ $branding['display_name'] }}
                        @if($branding['support_email'])
                            &middot; {{ $branding['support_email'] }}
                        @endif
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
