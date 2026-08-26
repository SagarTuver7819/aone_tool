</main>
</div> <!-- End app-container -->

<!-- Global Notification Drawer / Modal -->
<div class="notification-backdrop" id="notifBackdrop"
    style="display:none; position:fixed; inset:0; background:rgba(15,23,42,0.4); backdrop-filter:blur(3px); z-index:1060; transition:opacity 0.2s;">
</div>
<div class="notification-drawer" id="notifDrawer"
    style="display:none; position:fixed; top:0; right:0; width:360px; max-width:90vw; height:100vh; background:#ffffff; box-shadow:-8px 0 30px rgba(0,0,0,0.15); z-index:1070; flex-direction:column; border-left:1px solid #e2e8f0; font-family:'Inter', sans-serif;">
    <!-- Header -->
    <div
        style="padding:1.25rem; border-bottom:1px solid #f1f5f9; display:flex; align-items:center; justify-content:space-between; background:#ffffff;">
        <div style="display:flex; align-items:center; gap:8px;">
            <div
                style="width:32px; height:32px; border-radius:8px; background:#eff6ff; color:#4362CE; display:flex; align-items:center; justify-content:center; font-size:0.9rem;">
                <i class="fas fa-bell"></i>
            </div>
            <div>
                <h4 style="margin:0; font-size:0.95rem; font-weight:800; color:#0f172a;">Notifications</h4>
                <span style="font-size:0.72rem; color:#64748b; font-weight:600;">System &amp; Sync Alerts</span>
            </div>
        </div>
        <button type="button" id="closeNotifBtn"
            style="background:transparent; border:none; color:#94a3b8; font-size:1.1rem; cursor:pointer; width:30px; height:30px; border-radius:6px; display:flex; align-items:center; justify-content:center;">
            <i class="fas fa-times"></i>
        </button>
    </div>

    <!-- Body / List -->
    <div style="flex:1; overflow-y:auto; padding:1rem; display:flex; flex-direction:column; gap:0.75rem;">
        <!-- Notif Item 1 -->
        <div
            style="padding:0.85rem; border-radius:10px; background:#f8fafc; border:1px solid #f1f5f9; display:flex; gap:10px; align-items:flex-start;">
            <div style="width:8px; height:8px; border-radius:50%; background:#10b981; margin-top:5px; flex-shrink:0;">
            </div>
            <div style="flex:1;">
                <div style="font-size:0.82rem; font-weight:700; color:#1e293b; margin-bottom:2px;">Amazon Sync Completed
                </div>
                <div style="font-size:0.75rem; color:#64748b; line-height:1.35;">Daily orders and revenue metrics
                    successfully synchronized.</div>
                <div style="font-size:0.68rem; color:#94a3b8; margin-top:4px; font-weight:500;">Just now</div>
            </div>
        </div>

        <!-- Notif Item 2 -->
        <div
            style="padding:0.85rem; border-radius:10px; background:#f8fafc; border:1px solid #f1f5f9; display:flex; gap:10px; align-items:flex-start;">
            <div style="width:8px; height:8px; border-radius:50%; background:#4362CE; margin-top:5px; flex-shrink:0;">
            </div>
            <div style="flex:1;">
                <div style="font-size:0.82rem; font-weight:700; color:#1e293b; margin-bottom:2px;">PPC Ad Campaign
                    Update</div>
                <div style="font-size:0.75rem; color:#64748b; line-height:1.35;">Sponsored products ROAS trending at
                    4.01x this month.</div>
                <div style="font-size:0.68rem; color:#94a3b8; margin-top:4px; font-weight:500;">2 hours ago</div>
            </div>
        </div>

        <!-- Notif Item 3 -->
        <div
            style="padding:0.85rem; border-radius:10px; background:#f8fafc; border:1px solid #f1f5f9; display:flex; gap:10px; align-items:flex-start;">
            <div style="width:8px; height:8px; border-radius:50%; background:#f59e0b; margin-top:5px; flex-shrink:0;">
            </div>
            <div style="flex:1;">
                <div style="font-size:0.82rem; font-weight:700; color:#1e293b; margin-bottom:2px;">Reimbursements
                    Verified</div>
                <div style="font-size:0.75rem; color:#64748b; line-height:1.35;">Inventory audit verified $3,624.84 in
                    eligible claims.</div>
                <div style="font-size:0.68rem; color:#94a3b8; margin-top:4px; font-weight:500;">1 day ago</div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div style="padding:0.85rem 1.25rem; border-top:1px solid #f1f5f9; background:#ffffff; text-align:center;">
        <button type="button" onclick="closeNotifications()"
            style="background:#f1f5f9; color:#475569; border:none; font-weight:700; font-size:0.78rem; padding:6px 16px; border-radius:8px; cursor:pointer; width:100%;">
            Mark all as read
        </button>
    </div>
</div>

<script>
    function openNotifications() {
        $('#notifBackdrop').fadeIn(150);
        $('#notifDrawer').css('display', 'flex').hide().fadeIn(150);
    }

    function closeNotifications() {
        $('#notifBackdrop').fadeOut(150);
        $('#notifDrawer').fadeOut(150);
    }

    $(document).on('click', '[title="Notifications"], .btn-notification', function (e) {
        e.preventDefault();
        e.stopPropagation();
        openNotifications();
    });

    $(document).on('click', '#notifBackdrop, #closeNotifBtn', function () {
        closeNotifications();
    });
</script>
</body>

</html>