<?php
$path = __DIR__ . '/../modules/customer/manage.php';
$c = file_get_contents($path);

if (strpos($c, 'function onAccountTypeChange') !== false) {
    echo "SCRIPT_OK\n";
} else {
    $old = <<<'JS'
<script>
function setAllPerms(on) {
    document.querySelectorAll('.perm-check').forEach(function (el) { el.checked = !!on; });
}
function setActionPerms(action, on) {
    document.querySelectorAll('.perm-' + action).forEach(function (el) { el.checked = !!on; });
}
function onPermChange(el) {
    if (!el.checked) return;
    if (el.dataset.action === 'add' || el.dataset.action === 'edit' || el.dataset.action === 'delete') {
        var view = document.querySelector('.perm-check[data-module="' + el.dataset.module + '"][data-action="view"]');
        if (view) view.checked = true;
    }
}
</script>
JS;
    $new = <<<'JS'
<script>
function setAllPerms(on) {
    document.querySelectorAll('.perm-check').forEach(function (el) { el.checked = !!on; });
}
function setActionPerms(action, on) {
    document.querySelectorAll('.perm-' + action).forEach(function (el) { el.checked = !!on; });
}
function onPermChange(el) {
    if (!el.checked) return;
    if (el.dataset.action === 'add' || el.dataset.action === 'edit' || el.dataset.action === 'delete') {
        var view = document.querySelector('.perm-check[data-module="' + el.dataset.module + '"][data-action="view"]');
        if (view) view.checked = true;
    }
}
function onAccountTypeChange(type) {
    var hint = document.getElementById('staffHint');
    if (hint) hint.style.display = (type === 'staff') ? 'block' : 'none';
    var label = document.getElementById('nameLabel');
    var input = document.querySelector('input[name="customer_name"]');
    if (label && input) {
        if (type === 'staff') {
            label.textContent = 'Staff Name';
            input.placeholder = 'Enter Staff Name';
            ['view','add','edit'].forEach(function (a) {
                var el = document.querySelector('.perm-check[data-module="client_management"][data-action="' + a + '"]');
                if (el) el.checked = true;
            });
        } else {
            label.textContent = 'Seller Profile Name';
            input.placeholder = 'Enter Seller Profile Name';
            document.querySelectorAll('.perm-check[data-module="client_management"], .perm-check[data-module="report_upload"]').forEach(function (el) {
                el.checked = false;
            });
        }
    }
}
</script>
JS;
    // normalize line endings for replace
    $c2 = str_replace("\r\n", "\n", $c);
    $oldN = str_replace("\r\n", "\n", $old);
    if (strpos($c2, $oldN) === false) {
        echo "SCRIPT_BLOCK_NOT_FOUND\n";
    } else {
        $c2 = str_replace($oldN, $new, $c2);
        if (strpos($c, "\r\n") !== false) {
            $c2 = str_replace("\n", "\r\n", $c2);
        }
        file_put_contents($path, $c2);
        echo "SCRIPT_PATCHED\n";
    }
}

// Fix save button label if still hardcoded
$c = file_get_contents($path);
$c = str_replace('Save Client &amp; Permissions', 'Save <?php echo $account_type === \'staff\' ? \'Staff\' : \'Client\'; ?> &amp; Permissions', $c);
file_put_contents($path, $c);
echo "BTN_DONE\n";
