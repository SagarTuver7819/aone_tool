<?php
require_once '../../config.php';
require_once '../../includes/functions.php';

$page_title = "Action Center";
$page_subtitle = "Data-Driven Recommendations & PPC Triggers";

include '../../includes/header.php';
include '../../includes/sidebar.php';
?>

<div class="container-fluid">
    <!-- Action Cards Grid -->
    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm" style="border-radius: 20px; background: linear-gradient(135deg, #fef2f2 0%, #fff 100%);">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div style="width: 48px; height: 48px; background: #ef4444; color: #fff; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.25rem;">
                            <i class="fas fa-fire"></i>
                        </div>
                        <h5 class="mb-0 fw-800">Critical Alerts</h5>
                    </div>
                    <div id="critical_alerts" class="action-list">
                        <div class="p-3 mb-2 bg-white rounded-3 border border-danger-subtle shadow-xs">
                            <p class="small fw-bold text-danger mb-1"><i class="fas fa-exclamation-triangle me-1"></i> HIGH ACOS ALERT</p>
                            <p class="small text-muted mb-0">3 campaigns are exceeding 80% ACoS. Immediate bid reduction recommended.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm" style="border-radius: 20px; background: linear-gradient(135deg, #f0f9ff 0%, #fff 100%);">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div style="width: 48px; height: 48px; background: #0ea5e9; color: #fff; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.25rem;">
                            <i class="fas fa-lightbulb"></i>
                        </div>
                        <h5 class="mb-0 fw-800">Growth Insights</h5>
                    </div>
                    <div id="growth_insights" class="action-list">
                        <div class="p-3 mb-2 bg-white rounded-3 border border-info-subtle shadow-xs">
                            <p class="small fw-bold text-primary mb-1"><i class="fas fa-arrow-up me-1"></i> TOP CONVERTERS</p>
                            <p class="small text-muted mb-0">5 keywords have >20% conversion rate but low impression share. Increase budget.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm" style="border-radius: 20px; background: linear-gradient(135deg, #f0fdf4 0%, #fff 100%);">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div style="width: 48px; height: 48px; background: #10b981; color: #fff; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.25rem;">
                            <i class="fas fa-check-double"></i>
                        </div>
                        <h5 class="mb-0 fw-800">Optimization Log</h5>
                    </div>
                    <div id="optimization_log" class="action-list">
                        <div class="p-3 mb-2 bg-white rounded-3 border border-success-subtle shadow-xs">
                            <p class="small fw-bold text-success mb-1"><i class="fas fa-history me-1"></i> RECENT CHANGES</p>
                            <p class="small text-muted mb-0">Automated bid rules applied to 12 campaigns successfully.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recommendations Table -->
    <div class="card border-0 shadow-sm" style="border-radius: 24px;">
        <div class="card-header bg-white py-4 px-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="fw-900 mb-1">PPC Optimization Center</h4>
                    <p class="text-muted small mb-0">Automated recommendations based on last 30 days performance</p>
                </div>
                <button class="btn btn-dark btn-sm rounded-pill px-4 fw-bold">RUN ENGINE <i class="fas fa-robot ms-2"></i></button>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr class="bg-light">
                        <th class="px-4 py-3">ASIN / Campaign</th>
                        <th>Current State</th>
                        <th>Recommendation</th>
                        <th>Impact</th>
                        <th class="text-end px-4">Action</th>
                    </tr>
                </thead>
                <tbody id="action_body">
                    <tr>
                        <td colspan="5" class="text-center py-5">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2 text-muted">Analyzing performance data...</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    loadActionCenter();

    function loadActionCenter() {
        $.ajax({
            url: '<?php echo BASE_URL; ?>api/action_center_data.php',
            method: 'GET',
            success: function(response) {
                if (response.success) {
                    renderAlerts(response);
                    renderRecommendations(response);
                }
            }
        });
    }

    function renderAlerts(data) {
        let criticalHtml = '';
        if (data.bleeding.length > 0) {
            criticalHtml += `
                <div class="p-3 mb-2 bg-white rounded-3 border border-danger-subtle shadow-sm">
                    <p class="small fw-bold text-danger mb-1"><i class="fas fa-fire me-1"></i> BLEEDING TARGETS</p>
                    <p class="small text-muted mb-0">${data.bleeding.length} targets spent > $20 with 0 orders. Immediate pause recommended.</p>
                </div>`;
        }
        if (data.high_acos.length > 0) {
            criticalHtml += `
                <div class="p-3 mb-2 bg-white rounded-3 border border-warning-subtle shadow-sm">
                    <p class="small fw-bold text-warning mb-1"><i class="fas fa-exclamation-triangle me-1"></i> HIGH ACOS</p>
                    <p class="small text-muted mb-0">${data.high_acos.length} campaigns exceeding 50% ACoS. Review bids.</p>
                </div>`;
        }
        $('#critical_alerts').html(criticalHtml || '<p class="small text-muted text-center py-3">No critical alerts</p>');

        let growthHtml = '';
        if (data.high_roas.length > 0) {
            growthHtml += `
                <div class="p-3 mb-2 bg-white rounded-3 border border-success-subtle shadow-sm">
                    <p class="small fw-bold text-success mb-1"><i class="fas fa-arrow-up me-1"></i> SCALING OPPORTUNITY</p>
                    <p class="small text-muted mb-0">${data.high_roas.length} targets with >4.0 ROAS. Increase budget.</p>
                </div>`;
        }
        if (data.low_ctr.length > 0) {
            growthHtml += `
                <div class="p-3 mb-2 bg-white rounded-3 border border-info-subtle shadow-sm">
                    <p class="small fw-bold text-primary mb-1"><i class="fas fa-search me-1"></i> TARGETING OPTIMIZATION</p>
                    <p class="small text-muted mb-0">${data.low_ctr.length} targets with high impressions but <0.2% CTR.</p>
                </div>`;
        }
        $('#growth_insights').html(growthHtml || '<p class="small text-muted text-center py-3">No growth insights</p>');
    }

    function renderRecommendations(data) {
        let html = '';
        
        // Bleeding
        data.bleeding.forEach(item => {
            html += `
            <tr>
                <td class="px-4">
                    <div class="fw-bold">${item.targeting}</div>
                    <div class="small text-muted">${item.campaign_name}</div>
                </td>
                <td><span class="badge bg-danger-subtle text-danger">$${parseFloat(item.spend).toFixed(2)} Spend / 0 Orders</span></td>
                <td><div class="fw-bold">PAUSE TARGET</div><div class="small text-muted">Bleeding budget without conversion</div></td>
                <td><span class="text-success fw-bold">High Impact</span></td>
                <td class="text-end px-4"><button class="btn btn-outline-danger btn-sm rounded-pill px-3 fw-bold">PAUSE</button></td>
            </tr>`;
        });

        // High ACoS
        data.high_acos.forEach(item => {
            html += `
            <tr>
                <td class="px-4">
                    <div class="fw-bold">${item.campaign_name}</div>
                    <div class="small text-muted">Campaign Performance</div>
                </td>
                <td><span class="badge bg-warning-subtle text-warning">${parseFloat(item.acos).toFixed(1)}% ACoS</span></td>
                <td><div class="fw-bold">LOWER BIDS BY 20%</div><div class="small text-muted">ACOS above target threshold</div></td>
                <td><span class="text-primary fw-bold">Med Impact</span></td>
                <td class="text-end px-4"><button class="btn btn-outline-warning btn-sm rounded-pill px-3 fw-bold">ADJUST</button></td>
            </tr>`;
        });

        // Low Buy Box
        data.low_bb.forEach(item => {
            html += `
            <tr>
                <td class="px-4">
                    <div class="fw-bold">${item.asin}</div>
                    <div class="small text-muted">${item.title.substring(0, 40)}...</div>
                </td>
                <td><span class="badge bg-danger-subtle text-danger">${parseFloat(item.buy_box).toFixed(1)}% Buy Box</span></td>
                <td><div class="fw-bold">CHECK PRICING</div><div class="small text-muted">Losing buy box significantly</div></td>
                <td><span class="text-danger fw-bold">Critical</span></td>
                <td class="text-end px-4"><button class="btn btn-outline-dark btn-sm rounded-pill px-3 fw-bold">CHECK</button></td>
            </tr>`;
        });

        if (html === '') {
            html = '<tr><td colspan="5" class="text-center py-4 text-muted">No specific recommendations found for the selected period.</td></tr>';
        }
        $('#action_body').html(html);
    }
});
</script>

<style>
.fw-800 { font-weight: 800; }
.fw-900 { font-weight: 900; }
.shadow-xs { box-shadow: 0 1px 2px rgba(0,0,0,0.05); }
.action-list { max-height: 400px; overflow-y: auto; }
</style>

<?php include '../../includes/footer.php'; ?>
