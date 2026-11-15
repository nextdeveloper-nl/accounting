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
     * @param string $fromName Display name of the sender. Built by the job from the VERIFIED IAM account name in the
     *                         fixed format "<account name> via <platform>", never from tenant branding.
     * @param string|null $replyTo Verified email (the tenant account owner), never a tenant typed address.
     */
    public function __construct(
        public string  $event,
        public array   $branding,
        public array   $details,
        public         $recipient,
        public string  $fromName,
        public ?string $replyTo
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
        return new Envelope(
            //  The sending address stays the platform's (SPF / DKIM). The display name is a FIXED format built from
            //  the verified account name (see the job), so a tenant cannot present itself as the platform or as someone
            //  else, and the reply goes to the verified account owner.
            from: new Address(config('leo.mail.from'), $this->fromName),
            replyTo: $this->replyTo ? [new Address($this->replyTo)] : [],
            subject: $this->subjectLine,
        );
    }
}
