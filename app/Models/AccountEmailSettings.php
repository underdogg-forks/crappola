<?php

namespace App\Models;

/**
 * Class Account.
 *
 * @property int                             $id
 * @property int                             $account_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null                     $reply_to_email
 * @property string|null                     $bcc_email
 * @property string                          $email_subject_invoice
 * @property string                          $email_subject_quote
 * @property string                          $email_subject_payment
 * @property string                          $email_template_invoice
 * @property string                          $email_template_quote
 * @property string                          $email_template_payment
 * @property string                          $email_subject_reminder1
 * @property string                          $email_subject_reminder2
 * @property string                          $email_subject_reminder3
 * @property string                          $email_template_reminder1
 * @property string                          $email_template_reminder2
 * @property string                          $email_template_reminder3
 * @property string|null                     $late_fee1_amount
 * @property string|null                     $late_fee1_percent
 * @property string|null                     $late_fee2_amount
 * @property string|null                     $late_fee2_percent
 * @property string|null                     $late_fee3_amount
 * @property string|null                     $late_fee3_percent
 * @property string|null                     $email_subject_reminder4
 * @property string|null                     $email_template_reminder4
 * @property int|null                        $frequency_id_reminder4
 * @property string|null                     $email_subject_proposal
 * @property string|null                     $email_template_proposal
 *
 * @method static \Illuminate\Database\Eloquent\Builder|AccountEmailSettings newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AccountEmailSettings newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AccountEmailSettings query()
 * @method static \Illuminate\Database\Eloquent\Builder|AccountEmailSettings whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountEmailSettings whereBccEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountEmailSettings whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountEmailSettings whereEmailSubjectInvoice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountEmailSettings whereEmailSubjectPayment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountEmailSettings whereEmailSubjectProposal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountEmailSettings whereEmailSubjectQuote($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountEmailSettings whereEmailSubjectReminder1($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountEmailSettings whereEmailSubjectReminder2($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountEmailSettings whereEmailSubjectReminder3($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountEmailSettings whereEmailSubjectReminder4($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountEmailSettings whereEmailTemplateInvoice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountEmailSettings whereEmailTemplatePayment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountEmailSettings whereEmailTemplateProposal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountEmailSettings whereEmailTemplateQuote($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountEmailSettings whereEmailTemplateReminder1($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountEmailSettings whereEmailTemplateReminder2($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountEmailSettings whereEmailTemplateReminder3($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountEmailSettings whereEmailTemplateReminder4($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountEmailSettings whereFrequencyIdReminder4($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountEmailSettings whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountEmailSettings whereLateFee1Amount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountEmailSettings whereLateFee1Percent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountEmailSettings whereLateFee2Amount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountEmailSettings whereLateFee2Percent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountEmailSettings whereLateFee3Amount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountEmailSettings whereLateFee3Percent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountEmailSettings whereReplyToEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountEmailSettings whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class AccountEmailSettings extends \Illuminate\Database\Eloquent\Model
{
    public static $templates = [
        TEMPLATE_INVOICE,
        TEMPLATE_QUOTE,
        TEMPLATE_PROPOSAL,
        //TEMPLATE_PARTIAL,
        TEMPLATE_PAYMENT,
        TEMPLATE_REMINDER1,
        TEMPLATE_REMINDER2,
        TEMPLATE_REMINDER3,
        TEMPLATE_REMINDER4,
    ];

    /**
     * @var array
     */
    protected $fillable = [
        'bcc_email',
        'reply_to_email',
        'email_subject_invoice',
        'email_subject_quote',
        'email_subject_payment',
        'email_template_invoice',
        'email_template_quote',
        'email_template_payment',
        'email_subject_reminder1',
        'email_subject_reminder2',
        'email_subject_reminder3',
        'email_template_reminder1',
        'email_template_reminder2',
        'email_template_reminder3',
        'late_fee1_amount',
        'late_fee1_percent',
        'late_fee2_amount',
        'late_fee2_percent',
        'late_fee3_amount',
        'late_fee3_percent',
    ];
}
