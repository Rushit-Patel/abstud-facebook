<script>
$(document).ready(function() {
    var STORAGE_KEY = 'invoice_filters';

    // Handle filter form submission
    $('#invoiceFilterForm').on('submit', function(e) {
        e.preventDefault();
        applyLeadFilters();
        updateInvoiceFiltersFromStorage();
    });

    function updateFilterBadges(filters) {
        var $badgeContainer = $('#leadFilterBadge');
        if (!$badgeContainer.length) return;
        $badgeContainer.empty();

        var badgeCount = 0;

        // Date
        if (filters.date) {
            var dateText = filters.date;
            $badgeContainer.append(
                '<span class="kt-badge">Date: ' + dateText +
                ' <button type="button" data-filter-type="date">×</button></span> '
            );
            badgeCount++;
        }

        // Status
        if (filters.status && filters.status.length > 0) {
            $.each(filters.status, function(index, status) {
                var statusText = getFilterDisplayName('status', status);
                $badgeContainer.append(
                    '<span class="kt-badge">Status: ' + statusText +
                    ' <button type="button" data-filter-type="status" data-filter-value="' + status + '">×</button></span> '
                );
                badgeCount++;
            });
        }

        // Branch
        if (filters.branch && filters.branch.length > 0) {
            $.each(filters.branch, function(index, branch) {
                var branchText = getFilterDisplayName('branch', branch);
                $badgeContainer.append(
                    '<span class="kt-badge">Branch: ' + branchText +
                    ' <button type="button" data-filter-type="branch" data-filter-value="' + branch + '">×</button></span> '
                );
                badgeCount++;
            });
        }

        // Owner
        if (filters.owner && filters.owner.length > 0) {
            $.each(filters.owner, function(index, owner) {
                var ownerText = getFilterDisplayName('owner', owner);
                $badgeContainer.append(
                    '<span class="kt-badge">Owner: ' + ownerText +
                    ' <button type="button" data-filter-type="owner" data-filter-value="' + owner + '">×</button></span> '
                );
                badgeCount++;
            });
        }

        // Source
        if (filters.source && filters.source.length > 0) {
            $.each(filters.source, function(index, source) {
                var sourceText = getFilterDisplayName('source', source);
                $badgeContainer.append(
                    '<span class="kt-badge">Source: ' + sourceText +
                    ' <button type="button" data-filter-type="source" data-filter-value="' + source + '">×</button></span> '
                );
                badgeCount++;
            });
        }

        // Lead Type
        if (filters.lead_type && filters.lead_type.length > 0) {
            $.each(filters.lead_type, function(index, type) {
                var typeText = getFilterDisplayName('lead_type', type);
                $badgeContainer.append(
                    '<span class="kt-badge">Type: ' + typeText +
                    ' <button type="button" data-filter-type="lead_type" data-filter-value="' + type + '">×</button></span> '
                );
                badgeCount++;
            });
        }

        if (badgeCount > 0) {
            $badgeContainer.append(
                '<span class="kt-badge" id="clearAllFilters">Clear All (' + badgeCount + ')</span>'
            ).show();
        } else {
            $badgeContainer.hide();
        }
    }

    function getFilterDisplayName(filterType, value) {
        var $form = $('#invoiceFilterForm');
        var $input = $form.find('input[name="' + filterType + '[]"][value="' + value + '"], input[name="' + filterType + '"][value="' + value + '"], select[name="' + filterType + '[]"] option[value="' + value + '"]');

        if ($input.is('option')) {
            return $input.text().trim() || value;
        }

        if ($input.length > 0) {
            var $label = $input.closest('label');
            if ($label.length > 0) {
                return $label.text().trim() || value;
            }
            var inputId = $input.attr('id');
            if (inputId) {
                var $associatedLabel = $('label[for="' + inputId + '"]');
                if ($associatedLabel.length > 0) {
                    return $associatedLabel.text().trim() || value;
                }
            }
        }

        return value;
    }

    $(document).on('click', '[data-filter-type]', function(e) {
        e.preventDefault();
        e.stopPropagation();

        var filterType = $(this).data('filter-type');
        var filterValue = $(this).data('filter-value');
        var $form = $('#invoiceFilterForm');

        if (filterType === 'date') {
            $form.find('input[name="date"]').val('');
            var $dateInput = $form.find('input[name="date"]');
            if ($dateInput.length && $dateInput[0]._flatpickr) {
                $dateInput[0]._flatpickr.clear();
            }
        } else {
            var $select = $form.find('select[name="' + filterType + '[]"]');
            if ($select.length > 0) {
                var values = $select.val() || [];
                values = values.filter(function(v) { return v != filterValue; });
                $select.val(values).trigger('change');
            } else {
                $form.find('input[name="' + filterType + '[]"][value="' + filterValue + '"]').prop('checked', false);
            }
        }

        applyLeadFilters();
    });

    $(document).on('click', '#clearAllFilters', function(e) {
        e.preventDefault();
        window.resetInvoiceFilters();
    });

    function applyLeadFilters() {
        var $form = $('#invoiceFilterForm');
        var $applyBtn = $('button[type="submit"][form="invoiceFilterForm"]');
        var originalText = $applyBtn.html();

        $applyBtn.prop('disabled', true).html('<i class="ki-filled ki-loading animate-spin me-2"></i>Applying...');

        var filters = {
            date: $form.find('input[name="date"]').val(),
            status: $form.find('input[name="status[]"]:checked').map(function() { return $(this).val(); }).get(),
            branch: $form.find('input[name="branch[]"]:checked').map(function() { return $(this).val(); }).get(),
            owner: $form.find('select[name="owner[]"]').val() || [],
            source: $form.find('input[name="source[]"]:checked').map(function() { return $(this).val(); }).get(),
            lead_type: $form.find('input[name="lead_type[]"]:checked').map(function() { return $(this).val(); }).get()
        };

        localStorage.setItem(STORAGE_KEY, JSON.stringify(filters));

        var queryParams = [];
        if (filters.date) queryParams.push('date=' + encodeURIComponent(filters.date));
        $.each(filters.status, (i, v) => queryParams.push('status[]=' + encodeURIComponent(v)));
        $.each(filters.branch, (i, v) => queryParams.push('branch[]=' + encodeURIComponent(v)));
        $.each(filters.owner, (i, v) => queryParams.push('owner[]=' + encodeURIComponent(v)));
        $.each(filters.source, (i, v) => queryParams.push('source[]=' + encodeURIComponent(v)));
        $.each(filters.lead_type, (i, v) => queryParams.push('lead_type[]=' + encodeURIComponent(v)));

        var queryString = queryParams.length ? '?' + queryParams.join('&') : '';
        var table = $('#invoice-table').DataTable();
        table.ajax.url(table.ajax.url().split('?')[0] + queryString);
        table.ajax.reload(function() {
            $applyBtn.prop('disabled', false).html(originalText);

            var $drawer = $('#invoice_filter_drawer');
            if ($drawer.length && typeof KTDrawer !== 'undefined') {
                var drawerInstance = KTDrawer.getInstance($drawer[0]);
                if (drawerInstance) drawerInstance.hide();
            }

            if (typeof toastr !== 'undefined') {
                toastr.success('Filters applied successfully!');
            }

            updateFilterBadges(filters);
            $(document).trigger('filtersApplied', [filters]);
        }, function() {
            $applyBtn.prop('disabled', false).html(originalText);
            if (typeof toastr !== 'undefined') {
                toastr.error('Error applying filters. Please try again.');
            }
        });
    }

    window.resetInvoiceFilters = function() {
        var $form = $('#invoiceFilterForm');

        $form.find('input[type="text"], input[type="hidden"]').val('');
        $form.find('input[type="checkbox"]').prop('checked', false);
        $form.find('select[multiple]').val([]).trigger('change');

        var $dateInput = $form.find('input[name="date"]');
        if ($dateInput.length && $dateInput[0]._flatpickr) {
            $dateInput[0]._flatpickr.clear();
        }

        localStorage.removeItem(STORAGE_KEY);

        var table = $('#invoice-table').DataTable();
        table.ajax.url(table.ajax.url().split('?')[0]);
        table.ajax.reload(function() {
            if (typeof toastr !== 'undefined') {
                toastr.info('Filters cleared successfully!');
            }

            updateFilterBadges({});
            var $drawer = $('#invoice_filter_drawer');
            if ($drawer.length && typeof KTDrawer !== 'undefined') {
                var drawerInstance = KTDrawer.getInstance($drawer[0]);
                if (drawerInstance) drawerInstance.hide();
            }
        });

        $(document).trigger('filtersReset');
    };

    // ✅ Clear filters on page load
    localStorage.removeItem(STORAGE_KEY);
    if (typeof updateInvoiceFiltersFromStorage === "function") {
        updateInvoiceFiltersFromStorage();
    } else {
        window.resetInvoiceFilters(); // Only one reload at start
    }
});
</script>
