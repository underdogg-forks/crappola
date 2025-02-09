<?php

namespace App\Services;

use App\Libraries\Utils;
use App\Models\Account;
use App\Models\Activity;
use App\Models\Client;
use App\Models\Credit;
use App\Models\Invitation;
use App\Models\Invoice;
use App\Ninja\Datatables\PaymentDatatable;
use App\Ninja\Mailers\ContactMailer;
use App\Ninja\Mailers\UserMailer;
use App\Ninja\Repositories\AccountRepository;
use App\Ninja\Repositories\PaymentRepository;
use DateTime;
use Exception;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

class PaymentService extends BaseService
{
    /**
     * @var DatatableService
     */
    public $datatableService;

    /**
     * @var PaymentRepository
     */
    public $paymentRepo;

    /**
     * @var AccountRepository
     */
    public $accountRepo;

    /**
     * PaymentService constructor.
     */
    public function __construct(
        PaymentRepository $paymentRepo,
        AccountRepository $accountRepo,
        DatatableService $datatableService
    ) {
        $this->datatableService = $datatableService;
        $this->paymentRepo = $paymentRepo;
        $this->accountRepo = $accountRepo;
    }

    /**
     * @return bool
     */
    public function autoBillInvoice(Invoice $invoice)
    {
        if ( ! $invoice->canBePaid()) {
            return false;
        }

        /** @var Client $client */
        $client = $invoice->client;

        /** @var Account $account */
        $account = $client->account;

        /** @var Invitation $invitation */
        $invitation = $invoice->invitations->first();

        if ( ! $invitation) {
            return false;
        }

        $invoice->markSentIfUnsent();

        if ($credits = $client->credits->sum('balance')) {
            $balance = $invoice->balance;
            $amount = min($credits, $balance);
            $data = [
                'payment_type_id' => PAYMENT_TYPE_CREDIT,
                'invoice_id'      => $invoice->id,
                'client_id'       => $client->id,
                'amount'          => $amount,
            ];
            $payment = $this->paymentRepo->save($data);
            if ($amount == $balance) {
                return $payment;
            }
        }

        $paymentDriver = $account->paymentDriver($invitation, GATEWAY_TYPE_TOKEN);

        if ( ! $paymentDriver) {
            return false;
        }

        $customer = $paymentDriver->customer();

        if ( ! $customer) {
            return false;
        }

        $paymentMethod = $customer->default_payment_method;

        if ( ! $paymentMethod) {
            return false;
        }

        if ($paymentMethod->requiresDelayedAutoBill()) {
            $invoiceDate = DateTime::createFromFormat('Y-m-d', $invoice->invoice_date);
            $minDueDate = clone $invoiceDate;
            $minDueDate->modify('+10 days');

            if (date_create() < $minDueDate) {
                // Can't auto bill now
                return false;
            }

            if ($invoice->partial > 0) {
                // The amount would be different than the amount in the email
                return false;
            }

            $firstUpdate = Activity::where('invoice_id', '=', $invoice->id)
                ->where('activity_type_id', '=', ACTIVITY_TYPE_UPDATE_INVOICE)
                ->first();

            if ($firstUpdate) {
                $backup = json_decode($firstUpdate->json_backup);

                if ($backup->balance != $invoice->balance || $backup->due_date != $invoice->due_date) {
                    // It's changed since we sent the email can't bill now
                    return false;
                }
            }

            if ($invoice->payments->count()) {
                // ACH requirements are strict; don't auto bill this
                return false;
            }
        }

        try {
            return $paymentDriver->completeOnsitePurchase(false, $paymentMethod, true);
        } catch (Exception $exception) {
            $subject = trans('texts.auto_bill_failed', ['invoice_number' => $invoice->invoice_number]);
            $message = sprintf('%s: %s', ucwords($paymentDriver->providerName()), $exception->getMessage());
            //$message .= $exception->getTraceAsString();
            Utils::logError($message, 'PHP', true);
            if (App::runningInConsole()) {
                $mailer = app(UserMailer::class);
                $mailer->sendMessage($invoice->user, $subject, $message, [
                    'invoice' => $invoice,
                ]);
            }

            return false;
        }
    }

    public function save($input, $payment = null, $invoice = null)
    {
        // if the payment amount is more than the balance create a credit
        if ($invoice && Utils::parseFloat($input['amount']) > $invoice->balance) {
            $credit = Credit::createNew();
            $credit->client_id = $invoice->client_id;
            $credit->credit_date = date_create()->format('Y-m-d');
            $credit->amount = $input['amount'] - $invoice->balance;
            $credit->balance = $input['amount'] - $invoice->balance;
            $credit->private_notes = trans('texts.credit_created_by', ['transaction_reference' => $input['transaction_reference'] ?? '']);
            $credit->save();
            $input['amount'] = $invoice->balance;
        }

        return $this->paymentRepo->save($input, $payment);
    }

    public function getDatatable($clientPublicId, $search)
    {
        $datatable = new PaymentDatatable(true, $clientPublicId);
        $query = $this->paymentRepo->find($clientPublicId, $search);

        if ( ! Utils::hasPermission('view_payment')) {
            $query->where('payments.user_id', '=', Auth::user()->id);
        }

        return $this->datatableService->createDatatable($datatable, $query);
    }

    public function bulk($ids, $action, $params = []): int
    {
        if ($action == 'refund') {
            if ( ! $ids) {
                return 0;
            }

            $payments = $this->getRepo()->findByPublicIdsWithTrashed($ids);
            $successful = 0;

            foreach ($payments as $payment) {
                if (Auth::user()->can('edit', $payment) && ! $payment->is_deleted) {
                    $amount = empty($params['refund_amount']) ? null : (float) ($params['refund_amount']);
                    $sendEmail = empty($params['refund_email']) ? false : (bool) ($params['refund_email']);
                    $paymentDriver = false;
                    $refunded = false;

                    if ($accountGateway = $payment->account_gateway) {
                        $paymentDriver = $accountGateway->paymentDriver();
                    }

                    if ($paymentDriver && $paymentDriver->canRefundPayments) {
                        if ($paymentDriver->refundPayment($payment, $amount)) {
                            $successful++;
                            $refunded = true;
                        }
                    } else {
                        $payment->recordRefund($amount);
                        $successful++;
                        $refunded = true;
                    }

                    if ($refunded && $sendEmail) {
                        $mailer = app(ContactMailer::class);
                        $mailer->sendPaymentConfirmation($payment, $amount);
                    }
                }
            }

            return $successful;
        }

        return parent::bulk($ids, $action);
    }

    /**
     * @return PaymentRepository
     */
    protected function getRepo()
    {
        return $this->paymentRepo;
    }
}
