<li class="nav-item {{ Request::is('clients*') ? 'active' : '' }}">
    <a class="nav-link" href="{{ route('clients.index') }}">
        <i class="nav-icon icon-cursor"></i>
        <span>Clients</span>
    </a>
</li>
<li class="nav-item {{ Request::is('quotes*') ? 'active' : '' }}">
    <a class="nav-link" href="{{ route('quotes.index') }}">
        <i class="nav-icon icon-cursor"></i>
        <span>Quotes</span>
    </a>
</li>
<li class="nav-item {{ Request::is('invoices*') ? 'active' : '' }}">
    <a class="nav-link" href="{{ route('invoices.index') }}">
        <i class="nav-icon icon-cursor"></i>
        <span>Invoices</span>
    </a>
</li>
<li class="nav-item {{ Request::is('payments*') ? 'active' : '' }}">
    <a class="nav-link" href="{{ route('payments.index') }}">
        <i class="nav-icon icon-cursor"></i>
        <span>Payments</span>
    </a>
</li>
<li class="nav-item {{ Request::is('products*') ? 'active' : '' }}">
    <a class="nav-link" href="{{ route('products.index') }}">
        <i class="nav-icon icon-cursor"></i>
        <span>Products</span>
    </a>
</li>
<li class="nav-item {{ Request::is('tasks*') ? 'active' : '' }}">
    <a class="nav-link" href="{{ route('tasks.index') }}">
        <i class="nav-icon icon-cursor"></i>
        <span>Tasks</span>
    </a>
</li>
<li class="nav-item {{ Request::is('settings*') ? 'active' : '' }}">
    <a class="nav-link" href="{{ route('settings.index') }}">
        <i class="nav-icon icon-cursor"></i>
        <span>Settings</span>
    </a>
</li>






















<li class="nav-item {{ Request::is('invoiceGroups*') ? 'active' : '' }}">
    <a class="nav-link" href="{{ route('invoiceGroups.index') }}">
        <i class="nav-icon icon-cursor"></i>
        <span>Invoice Groups</span>
    </a>
</li>
<li class="nav-item {{ Request::is('recurringInvoices*') ? 'active' : '' }}">
    <a class="nav-link" href="{{ route('recurringInvoices.index') }}">
        <i class="nav-icon icon-cursor"></i>
        <span>Invoice Recurrings</span>
    </a>
</li>
<li class="nav-item {{ Request::is('emailTemplates*') ? 'active' : '' }}">
    <a class="nav-link" href="{{ route('emailTemplates.index') }}">
        <i class="nav-icon icon-cursor"></i>
        <span>Email Templates</span>
    </a>
</li>
<li class="nav-item {{ Request::is('productFamilies*') ? 'active' : '' }}">
    <a class="nav-link" href="{{ route('productFamilies.index') }}">
        <i class="nav-icon icon-cursor"></i>
        <span>Product Families</span>
    </a>
</li>
<li class="nav-item {{ Request::is('invoiceItems*') ? 'active' : '' }}">
    <a class="nav-link" href="{{ route('invoiceItems.index') }}">
        <i class="nav-icon icon-cursor"></i>
        <span>Invoice Items</span>
    </a>
</li>
<li class="nav-item {{ Request::is('paymentMethods*') ? 'active' : '' }}">
    <a class="nav-link" href="{{ route('paymentMethods.index') }}">
        <i class="nav-icon icon-cursor"></i>
        <span>Payment Methods</span>
    </a>
</li>

<li class="nav-item {{ Request::is('projects*') ? 'active' : '' }}">
    <a class="nav-link" href="{{ route('projects.index') }}">
        <i class="nav-icon icon-cursor"></i>
        <span>Projects</span>
    </a>
</li>
<li class="nav-item {{ Request::is('quoteItems*') ? 'active' : '' }}">
    <a class="nav-link" href="{{ route('quoteItems.index') }}">
        <i class="nav-icon icon-cursor"></i>
        <span>Quote Items</span>
    </a>
</li>



<li class="nav-item {{ Request::is('taxRates*') ? 'active' : '' }}">
    <a class="nav-link" href="{{ route('taxRates.index') }}">
        <i class="nav-icon icon-cursor"></i>
        <span>Tax Rates</span>
    </a>
</li>
<li class="nav-item {{ Request::is('units*') ? 'active' : '' }}">
    <a class="nav-link" href="{{ route('units.index') }}">
        <i class="nav-icon icon-cursor"></i>
        <span>Units</span>
    </a>
</li>

<li class="nav-item {{ Request::is('users*') ? 'active' : '' }}">
    <a class="nav-link" href="{{ route('users.index') }}">
        <i class="nav-icon icon-cursor"></i>
        <span>Users</span>
    </a>
</li>
