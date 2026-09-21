/**
 * Shared DB-driven date range helper.
 * Auto-selects filter_from / filter_to from get_data_range.php overall span.
 * No UI/design changes — only sets values + optional flatpickr default.
 */
(function (window) {
    'use strict';

    function ymd(value) {
        if (!value) return '';
        return String(value).substring(0, 10);
    }

    function fallbackSpan() {
        var now = new Date();
        var to = now.toISOString().slice(0, 10);
        var from = new Date(now.getFullYear(), now.getMonth(), 1).toISOString().slice(0, 10);
        return { from: from, to: to };
    }

    /**
     * Prefer overall DB span so every module opens on the same available months.
     * Optional preferredKeys e.g. ['ads','trans'] used only if overall missing.
     */
    function resolve(ranges, preferredKeys) {
        preferredKeys = preferredKeys || [];
        var src = null;
        if (ranges && ranges.overall && ranges.overall.min_date && ranges.overall.max_date) {
            src = ranges.overall;
        } else if (ranges) {
            for (var i = 0; i < preferredKeys.length; i++) {
                var key = preferredKeys[i];
                if (ranges[key] && ranges[key].min_date && ranges[key].max_date) {
                    src = ranges[key];
                    break;
                }
            }
        }
        var from = src ? ymd(src.min_date) : '';
        var to = src ? ymd(src.max_date) : '';
        if (!from || !to) return fallbackSpan();
        return { from: from, to: to };
    }

    function applyHidden(from, to) {
        if (window.jQuery) {
            window.jQuery('#filter_from, .filter-from-input').val(from);
            window.jQuery('#filter_to, .filter-to-input').val(to);
        } else {
            var fromEl = document.getElementById('filter_from');
            var toEl = document.getElementById('filter_to');
            if (fromEl) fromEl.value = from;
            if (toEl) toEl.value = to;
        }
    }

    /**
     * Fetch get_data_range.php and apply overall months.
     * options: { url, customerId, preferredKeys, onDone(span, ranges) }
     */
    function boot(options) {
        options = options || {};
        var url = options.url || '../../api/get_data_range.php';
        var customerId = options.customerId != null ? options.customerId : 0;
        var preferredKeys = options.preferredKeys || [];
        var onDone = typeof options.onDone === 'function' ? options.onDone : function () {};

        function finish(ranges) {
            var span = resolve(ranges, preferredKeys);
            applyHidden(span.from, span.to);
            onDone(span, ranges || {});
        }

        if (!window.jQuery) {
            finish(null);
            return;
        }

        window.jQuery.get(url, { customer_id: customerId || 0 })
            .done(function (ranges) { finish(ranges); })
            .fail(function () { finish(null); });
    }

    window.AOneDateRange = {
        resolve: resolve,
        applyHidden: applyHidden,
        boot: boot,
        fallbackSpan: fallbackSpan
    };
})(window);
