<?php
require_once '../../config.php';
require_once '../../includes/functions.php';

$page_title = "Pro Analytics Configuration";
$page_subtitle = "Manage Historical COGS, Shipping Rules, and Expenses";

include '../../includes/header.php';
include '../../includes/sidebar.php';

$customer_id = $_SESSION['customer_id'] ?? 0;
?>

<div style="display: grid; grid-template-columns: 1fr; gap: 2rem;">
    <!-- Left Column: Product Configurations & COGS History -->
    <div>
        <div class="card" style="border-radius: 16px; border: none; box-shadow: 0 4px 20px rgba(0,0,0,0.04); margin-bottom: 2rem;">
            <div style="background: #f8fafc; padding: 1rem 1.5rem; border-bottom: 1px solid #e2e8f0; font-weight: 700; color: #334155; display: flex; justify-content: space-between; align-items: center;">
                <span><i class="fas fa-barcode" style="color: #3b82f6; margin-right: 0.5rem;"></i> Product Costs & Shipping</span>
                <button class="btn btn-sm btn-primary" onclick="openProductModal()">+ Add Product</button>
            </div>
            <div style="padding: 1rem;">
                <table class="table" style="width: 100%;">
                    <thead>
                        <tr>
                            <th>SKU</th>
                            <th>Current COGS</th>
                            <th>Shipping/Unit</th>
                            <th>Fixed/Unit</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="product_list"></tbody>
                </table>
            </div>
        </div>

        <div class="card" style="border-radius: 16px; border: none; box-shadow: 0 4px 20px rgba(0,0,0,0.04);">
            <div style="background: #f8fafc; padding: 1rem 1.5rem; border-bottom: 1px solid #e2e8f0; font-weight: 700; color: #334155;">
                <i class="fas fa-history" style="color: #3b82f6; margin-right: 0.5rem;"></i> COGS History (Historical Pricing)
            </div>
            <div style="padding: 1rem;">
                <table class="table" style="width: 100%;">
                    <thead>
                        <tr>
                            <th>SKU</th>
                            <th>COGS</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="cogs_history_list"></tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Right Column: Global Expense Rules -->
    <div>
        <div class="card" style="border-radius: 16px; border: none; box-shadow: 0 4px 20px rgba(0,0,0,0.04);">
            <div style="background: #f8fafc; padding: 1rem 1.5rem; border-bottom: 1px solid #e2e8f0; font-weight: 700; color: #334155; display: flex; justify-content: space-between; align-items: center;">
                <span><i class="fas fa-calculator" style="color: #3b82f6; margin-right: 0.5rem;"></i> Expense Rules</span>
                <button class="btn btn-sm btn-primary" onclick="openRuleModal()">+ Add Rule</button>
            </div>
            <div style="padding: 1rem;">
                <div id="rules_list"></div>
            </div>
        </div>
    </div>
</div>

<!-- Modals for adding/editing would go here -->
<!-- (Simplified for brevity, but functional for UI demo) -->

<script>
$(document).ready(function() {
    function loadConfigs() {
        $.get('../../api/pro_config_process.php', { action: 'list_all' }, function(res) {
            let p_html = '';
            res.products.forEach(p => {
                p_html += `<tr>
                    <td><b>${p.sku}</b></td>
                    <td>$${p.cogs || '0.00'}</td>
                    <td>$${p.shipping_cost_per_unit}</td>
                    <td>$${p.other_fixed_cost_per_unit}</td>
                    <td><button class="btn btn-sm btn-light"><i class="fas fa-edit"></i></button></td>
                </tr>`;
            });
            $('#product_list').html(p_html || '<tr><td colspan="5" class="text-center">No products configured</td></tr>');

            let c_html = '';
            res.history.forEach(h => {
                c_html += `<tr>
                    <td>${h.sku}</td>
                    <td>$${h.cogs}</td>
                    <td>${h.start_date}</td>
                    <td>${h.end_date || 'Present'}</td>
                    <td><button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button></td>
                </tr>`;
            });
            $('#cogs_history_list').html(c_html || '<tr><td colspan="5" class="text-center">No history recorded</td></tr>');

            let r_html = '';
            res.rules.forEach(r => {
                r_html += `<div style="padding: 1rem; border: 1px solid #e2e8f0; border-radius: 12px; margin-bottom: 1rem;">
                    <div style="display: flex; justify-content: space-between;">
                        <span style="font-weight: 700;">${r.rule_name}</span>
                        <span class="badge bg-primary">${r.rule_type.replace(/_/g, ' ')}</span>
                    </div>
                    <div style="font-size: 1.25rem; font-weight: 800; margin-top: 0.5rem;">
                        ${r.rule_type === 'percent_of_sales' ? r.value + '%' : '$' + r.value}
                    </div>
                </div>`;
            });
            $('#rules_list').html(r_html || '<p class="text-center">No active rules</p>');
        });
    }

    loadConfigs();
});

function openProductModal() { alert("Product management logic can be added here."); }
function openRuleModal() { alert("Rule management logic can be added here."); }
</script>

<?php include '../../includes/footer.php'; ?>
