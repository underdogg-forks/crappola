<?php

namespace App\Models\Traits;

use App\Constants\Domain;
use App\Libraries\Utils;
use HTMLUtils;

/**
 * Class SendsEmails.
 */
trait SendsEmails
{
    /**
     * @return mixed
     */
    public function getEmailSubject($entityType)
    {
        if ($this->hasFeature(FEATURE_CUSTOM_EMAILS)) {
            $field = "email_subject_{$entityType}";
            $value = $this->account_email_settings->$field;

            if ($value) {
                $value = preg_replace("/\r\n|\r|\n/", ' ', $value);

                return HTMLUtils::sanitizeHTML($value);
            }
        }

        return $this->getDefaultEmailSubject($entityType);
    }

    /**
     * @return mixed
     */
    public function getDefaultEmailSubject($entityType)
    {
        if (strpos($entityType, 'reminder') !== false) {
            $entityType = 'reminder';
        }

        return trans("texts.{$entityType}_subject", [
            'invoice' => '$invoice',
            'company' => '$company',
            'quote'   => '$quote',
            'number'  => '$number',
        ]);
    }

    /**
     * @param bool $message
     *
     * @return mixed
     */
    public function getEmailTemplate($entityType, $message = false)
    {
        $template = false;

        if ($this->hasFeature(FEATURE_CUSTOM_EMAILS)) {
            $field = "email_template_{$entityType}";
            $template = $this->account_email_settings->$field;
        }

        if (! $template) {
            $template = $this->getDefaultEmailTemplate($entityType, $message);
        }

        $template = preg_replace("/\r\n|\r|\n/", ' ', $template);

        // <br/> is causing page breaks with the email designs
        $template = str_replace('/>', ' />', $template);

        return HTMLUtils::sanitizeHTML($template);
    }

    /**
     * @param bool $message
     */
    public function getDefaultEmailTemplate($entityType, $message = false): string
    {
        if (strpos($entityType, 'reminder') !== false) {
            $entityType = ENTITY_INVOICE;
        }

        $template = '<div>$client,</div><br />';

        if ($this->hasFeature(FEATURE_CUSTOM_EMAILS) && $this->account_email_settings->email_design_id != EMAIL_DESIGN_PLAIN) {
            $template .= '<div>' . trans("texts.{$entityType}_message_button", ['amount' => '$amount']) . '</div><br />' .
                '<div style="text-align:center;">$viewButton</div><br />';
        } else {
            $template .= '<div>' . trans("texts.{$entityType}_message", ['amount' => '$amount']) . '</div><br />' .
                '<div>$viewLink</div><br />';
        }

        if ($message) {
            $template .= "$message<p/>";
        }

        return $template . '$emailSignature';
    }

    /**
     * @param string $view
     *
     * @return string
     */
    public function getTemplateView($view = '')
    {
        return $this->getEmailDesignId() == EMAIL_DESIGN_PLAIN ? $view : 'design' . $this->getEmailDesignId();
    }

    /**
     * @return mixed|string
     */
    public function getEmailFooter()
    {
        if (! $this->isPro()) {
            return '<p><div>' . trans('texts.email_signature') . "\n<br>\$company</div></p>";
        }
        if (! $this->account_email_settings->email_footer) {
            return '<p><div>' . trans('texts.email_signature') . "\n<br>\$company</div></p>";
        }

        // Add line breaks if HTML isn't already being used
        return strip_tags($this->account_email_settings->email_footer) == $this->account_email_settings->email_footer ? nl2br($this->account_email_settings->email_footer) : $this->account_email_settings->email_footer;
    }

    /**
     * @param Invoice $invoice
     */
    public function getInvoiceReminder($invoice, $filterEnabled = true): string|bool
    {
        $reminder = $invoice->isQuote() ? 'quote_reminder' : 'reminder';

        for ($i = 1; $i <= 3; $i++) {
            if ($date = $this->getReminderDate($reminder . $i, $filterEnabled)) {
                if ($this->account_email_settings->{'field_' . $reminder . $i} == REMINDER_FIELD_DUE_DATE) {
                    if ($invoice->partial && $invoice->partial_due_date == $date) {
                        return $reminder . $i;
                    }
                    if ($invoice->due_at == $date) {
                        return $reminder . $i;
                    }
                } else {
                    if ($invoice->invoice_date == $date) {
                        return $reminder . $i;
                    }
                }
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    public function getReminderDate($reminder, $filterEnabled = true): bool|string
    {
        if ($filterEnabled && ! $this->account_email_settings->{"enable_{$reminder}"}) {
            return false;
        }

        $numDays = $this->account_email_settings->{"num_days_{$reminder}"};
        $plusMinus = $this->account_email_settings->{"direction_{$reminder}"} == REMINDER_DIRECTION_AFTER ? '-' : '+';

        return date('Y-m-d', strtotime("$plusMinus $numDays days"));
    }

    public function setTemplateDefaults($type, $subject, $body): void
    {
        $settings = $this->account_email_settings;

        if ($subject) {
            $settings->{'email_subject_' . $type} = $subject;
        }

        if ($body) {
            $settings->{'email_template_' . $type} = $body;
        }

        $settings->save();
    }

    public function getBccEmail()
    {
        return $this->isPro() ? $this->account_email_settings->bcc_email : false;
    }

    public function getReplyToEmail()
    {
        return $this->isPro() ? $this->account_email_settings->reply_to_email : false;
    }

    public function getFromEmail()
    {
        if (! $this->isPro()) {
            return false;
        }
        if (! Utils::isNinja()) {
            return false;
        }
        if (Utils::isReseller()) {
            return false;
        }

        return Domain::getEmailFromId($this->domain_id);
    }

    public function getDailyEmailLimit(): int|float
    {
        $limit = MAX_EMAILS_SENT_PER_DAY;

        $limit += $this->created_at->diffInMonths() * 100;

        return min($limit, 5000);
    }
}
