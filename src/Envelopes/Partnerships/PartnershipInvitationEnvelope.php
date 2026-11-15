<?php

namespace NextDeveloper\Accounting\Envelopes\Partnerships;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

/**
 * Email about the lifecycle of a partnership invitation (invited, accepted, rejected, revoked).
 *
 * The email is branded with the TENANT (the program owner): its logo, colour and name, so the invited company
 * receives it from the company that invited them. All branding values are sanitised by the job before they get here.
 */
class PartnershipInvitationEnvelope extends Mailable
{
    public string $subjectLine;

    /**
     * @param string $event   invited | accepted | rejected | revoked
     * @param array  $branding display_name, logo_url (https only or null), primary_color, support_email
     * @param array  $details  tenant_name, partner_name, parent_name (nullable), roles (names), action_url (nullable)
     */
    public function __construct(
        public string $event,
        public array  $branding,
        public array  $details,
        public        $recipient
    ) {
        $tenant = $details['tenant_name'];
        $partner = $details['partner_name'];

        $this->subjectLine = match ($event) {
            'invited'  => $tenant . ' invited you to become a partner',
            'accepted' => $partner . ' accepted your partnership invitation',
            'rejected' => $partner . ' declined your partnership invitation',
            'revoked'  => 'Your partnership with ' . $tenant . ' has ended',
            default    => 'Partnership update',
        };
    }

    public function content(): Content
    {
        return new Content(
            html: 'Accounting::partnership.invitation',
            with: [
                'event'     => $this->event,
                'branding'  => $this->branding,
                'details'   => $this->details,
                'recipient' => $this->recipient,
                'subject'   => $this->subjectLine,
            ]
        );
    }

    public function envelope(): Envelope
    {
        $replyTo = $this->branding['support_email'] ?: config('leo.mail.reply_to');

        return new Envelope(
            //  The sending address stays the platform's (SPF / DKIM), only the display name is the tenant's.
            from: new Address(config('leo.mail.from'), $this->branding['display_name']),
            replyTo: $replyTo ? [new Address($replyTo)] : [],
            subject: $this->subjectLine,
        );
    }
}
