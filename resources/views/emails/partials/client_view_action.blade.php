<script type="application/ld+json">
[
@if ($entityType == ENTITY_INVOICE)
        {
          "@context": "http://schema.org",
          "@type": "Invoice",
          "paymentStatus": "{{ $invoice->present()->paymentStatus }}",
  @if ($invoice->due_at)
            "paymentDue": "{{ $invoice->due_at }}T00:00:00+00:00",

        @endif
        "provider": {
          "@type": "Organization",
          "name": "{{ $company->getDisplayName() }}"
  },
  "broker": {
    "@type": "Organization",
    "name": "Invoice Ninja",
    "url": "{{ NINJA_WEB_URL }}"
  },
  "totalPaymentDue": {
    "@type": "PriceSpecification",
    "price": "{{ $company->formatMoney(isset($payment) ? $payment->amount : $invoice->getRequestedAmount(), $client) }}"
  },
  "action": {
    "@type": "ViewAction",
    "url": "{{ $link }}"
  }
},

    @endif
    {
      "@context": "http://schema.org",
      "@type": "EmailMessage",
      "action": {
        "@type": "ViewAction",
        "url": "{{ $link }}",
    "name": {!! json_encode(trans("texts.view_{$entityType}")) !!}
    }
  }
  ]

</script>
