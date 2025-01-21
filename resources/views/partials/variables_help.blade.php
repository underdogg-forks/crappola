<div class="col-md-6">
    <ul>
        @if ($entityType === ENTITY_QUOTE)
            <li>$quote.quoteNumber</li>
            <li>$quote.quoteDate</li>
            <li>$quote.validUntil</li>
        @elseif ($entityType === ENTITY_INVOICE)
            <li>$invoice.invoiceNumber</li>
            <li>$invoice.invoiceDate</li>
            <li>$invoice.dueDate</li>
        @endif
        <li>${{ $entityType }}.discount</li>
        <li>${{ $entityType }}.poNumber</li>
        <li>${{ $entityType }}.publicNotes</li>
        <li>${{ $entityType }}.amount</li>
        <li>${{ $entityType }}.terms</li>
        <li>${{ $entityType }}.footer</li>
        <li>${{ $entityType }}.partial</li>
        <li>${{ $entityType }}.partialDueDate</li>
        @if ($company->customLabel('invoice1'))
            <li>${{ $entityType }}.customValue1</li>
        @endif
        @if ($company->customLabel('invoice2'))
            <li>${{ $entityType }}.customValue2</li>
        @endif
        @if ($company->customLabel('invoice_text1'))
            <li>${{ $entityType }}.customTextValue1</li>
        @endif
        @if ($company->customLabel('invoice_text2'))
            <li>${{ $entityType }}.customTextValue2</li>
        @endif
    </ul>
    <ul>
        <li>$company.name</li>
        <li>$company.idNumber</li>
        <li>$company.vatNumber</li>
        <li>$company.address1</li>
        <li>$company.address2</li>
        <li>$company.city</li>
        <li>$company.state</li>
        <li>$company.postalCode</li>
        <li>$company.country.name</li>
        <li>$company.phone</li>
        @if ($company->custom_label1)
            <li>$company.customValue1</li>
        @endif
        @if ($company->custom_label2)
            <li>$company.customValue2</li>
        @endif
    </ul>
    </ul>
</div>
<div class="col-md-6">
    <ul>
        <li>$client.name</li>
        <li>$client.idNumber</li>
        <li>$client.vatNumber</li>
        <li>$client.address1</li>
        <li>$client.address2</li>
        <li>$client.city</li>
        <li>$client.state</li>
        <li>$client.postalCode</li>
        <li>$client.country.name</li>
        <li>$client.phone</li>
        <li>$client.balance</li>
        @if ($company->customLabel('client1'))
            <li>$client.customValue1</li>
        @endif
        @if ($company->customLabel('client2'))
            <li>$client.customValue2</li>
        @endif
    </ul>
    <ul>
        <li>$contact.firstName</li>
        <li>$contact.lastName</li>
        <li>$contact.email</li>
        <li>$contact.phone</li>
        @if ($company->customLabel('contact1'))
            <li>$contact.customValue1</li>
        @endif
        @if ($company->customLabel('contact2'))
            <li>$contact.customValue2</li>
        @endif
    </ul>
</div>
