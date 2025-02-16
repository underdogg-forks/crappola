<?php if(Auth::user()->hasFeature(FEATURE_INVOICE_SETTINGS)): ?>
    <?php if($customLabel = $account->customLabel($entityType . '1')): ?>
        <?php echo $__env->make('partials.custom_field', [
            'field' => 'custom_value1',
            'label' => $customLabel
        ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php endif; ?>
    <?php if($customLabel = $account->customLabel($entityType . '2')): ?>
        <?php echo $__env->make('partials.custom_field', [
            'field' => 'custom_value2',
            'label' => $customLabel
        ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php endif; ?>
<?php endif; ?>
<?php /**PATH /var/www/projects/invninjl10/resources/views/partials/custom_fields.blade.php ENDPATH**/ ?>