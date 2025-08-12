<script>
$(document).ready(function() {
    var STORAGE_KEY = 'task_filters';

    // Handle filter form submission
    $('#taskFilterForm').on('submit', function(e) {
        e.preventDefault();
        applyTaskFilters();
    });

    function updateFilterBadges(filters) {
        var $badgeContainer = $('#taskFilterBadge');
        if (!$badgeContainer.length) return;
        $badgeContainer.empty();

        var badgeCount = 0;

        // Date
        if (filters.date) {
            var dateText = filters.date;
            $badgeContainer.append(
                '<span class="kt-badge">Date: ' + dateText +
                ' <span class="kt-btn kt-btn-sm kt-btn-icon kt-btn-dim shrink-0" data-filter-type="date">×</span></span> '
            );
            badgeCount++;
        }

        // Due Date
        if (filters.due_date) {
            var dueDateText = filters.due_date;
            $badgeContainer.append(
                '<span class="kt-badge ">Due Date: ' + dueDateText +
                ' <span class="kt-btn kt-btn-sm kt-btn-icon kt-btn-dim shrink-0" data-filter-type="due_date">×</span></span> '
            );
            badgeCount++;
        }

        // Status
        if (filters.status && filters.status.length > 0) {
            $.each(filters.status, function(index, status) {
                var statusText = getFilterDisplayName('status', status);
                $badgeContainer.append(
                    '<span class="kt-badge ">Status: ' + statusText +
                    ' <span class="kt-btn kt-btn-sm kt-btn-icon kt-btn-dim shrink-0" data-filter-type="status" data-filter-value="' + status + '">×</span></span> '
                );
                badgeCount++;
            });
        }

        // Priority
        if (filters.priority && filters.priority.length > 0) {
            $.each(filters.priority, function(index, priority) {
                var priorityText = getFilterDisplayName('priority', priority);
                $badgeContainer.append(
                    '<span class="kt-badge ">Priority: ' + priorityText +
                    ' <span class="kt-btn kt-btn-sm kt-btn-icon kt-btn-dim shrink-0" data-filter-type="priority" data-filter-value="' + priority + '">×</span></span> '
                );
                badgeCount++;
            });
        }

        // Category
        if (filters.category && filters.category.length > 0) {
            $.each(filters.category, function(index, category) {
                var categoryText = getFilterDisplayName('category', category);
                $badgeContainer.append(
                    '<span class="kt-badge ">Category: ' + categoryText +
                    ' <span class="kt-btn kt-btn-sm kt-btn-icon kt-btn-dim shrink-0" data-filter-type="category" data-filter-value="' + category + '">×</span></span> '
                );
                badgeCount++;
            });
        }

        // Assigned To
        if (filters.assigned_to && filters.assigned_to.length > 0) {
            $.each(filters.assigned_to, function(index, assignee) {
                var assigneeText = getFilterDisplayName('assigned_to', assignee);
                $badgeContainer.append(
                    '<span class="kt-badge ">Assigned To: ' + assigneeText +
                    ' <span class="kt-btn kt-btn-sm kt-btn-icon kt-btn-dim shrink-0" data-filter-type="assigned_to" data-filter-value="' + assignee + '">×</span></span> '
                );
                badgeCount++;
            });
        }

        // Owner (Creator)
        if (filters.owner && filters.owner.length > 0) {
            $.each(filters.owner, function(index, owner) {
                var ownerText = getFilterDisplayName('owner', owner);
                $badgeContainer.append(
                    '<span class="kt-badge ">Owner: ' + ownerText +
                    ' <span class="" data-filter-type="owner" data-filter-value="' + owner + '">×</span></span> '
                );
                badgeCount++;
            });
        }

        if (badgeCount > 0) {
            $badgeContainer.append(
                '<span class="kt-btn kt-btn-sm kt-btn-outline" id="clearAllTaskFilters">Clear All (' + badgeCount + ')</span>'
            ).show();
        } else {
            $badgeContainer.hide();
        }
    }

    function getFilterDisplayName(filterType, value) {
        var $form = $('#taskFilterForm');
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
        var $form = $('#taskFilterForm');

        if (filterType === 'date' || filterType === 'due_date') {
            $form.find('input[name="' + filterType + '"]').val('');
            var $dateInput = $form.find('input[name="' + filterType + '"]');
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

        applyTaskFilters();
    });

    $(document).on('click', '#clearAllTaskFilters', function(e) {
        e.preventDefault();
        window.resetTaskFilters();
    });

    function applyTaskFilters() {
        var $form = $('#taskFilterForm');
        var $applyBtn = $('button[type="submit"][form="taskFilterForm"]');
        var originalText = $applyBtn.html();

        $applyBtn.prop('disabled', true).html('<i class="ki-filled ki-loading animate-spin me-2"></i>Applying...');

        var filters = {
            date: $form.find('input[name="date"]').val(),
            due_date: $form.find('input[name="due_date"]').val(),
            status: $form.find('input[name="status[]"]:checked').map(function() { return $(this).val(); }).get(),
            priority: $form.find('input[name="priority[]"]:checked').map(function() { return $(this).val(); }).get(),
            category: $form.find('input[name="category[]"]:checked').map(function() { return $(this).val(); }).get(),
            branch: $form.find('input[name="branch[]"]:checked').map(function() { return $(this).val(); }).get(),
            assigned_to: $form.find('select[name="assigned_to[]"]').val() || [],
            owner: $form.find('select[name="owner[]"]').val() || []
        };

        localStorage.setItem(STORAGE_KEY, JSON.stringify(filters));

        var queryParams = [];
        if (filters.date) queryParams.push('date=' + encodeURIComponent(filters.date));
        if (filters.due_date) queryParams.push('due_date=' + encodeURIComponent(filters.due_date));
        $.each(filters.status, (i, v) => queryParams.push('status[]=' + encodeURIComponent(v)));
        $.each(filters.priority, (i, v) => queryParams.push('priority[]=' + encodeURIComponent(v)));
        $.each(filters.category, (i, v) => queryParams.push('category[]=' + encodeURIComponent(v)));
        $.each(filters.branch, (i, v) => queryParams.push('branch[]=' + encodeURIComponent(v)));
        $.each(filters.assigned_to, (i, v) => queryParams.push('assigned_to[]=' + encodeURIComponent(v)));
        $.each(filters.owner, (i, v) => queryParams.push('owner[]=' + encodeURIComponent(v)));

        var queryString = queryParams.length ? '?' + queryParams.join('&') : '';
        
        // Check if DataTable exists and is properly initialized
        var $table = $('#task-table');
        if (!$table.length) {
            console.error('Task table element not found');
            $applyBtn.prop('disabled', false).html(originalText);
            return;
        }

        try {
            var table = $table.DataTable();
            if (!table || !table.ajax || typeof table.ajax.url !== 'function') {
                console.error('DataTable AJAX not properly initialized');
                $applyBtn.prop('disabled', false).html(originalText);
                return;
            }

            var currentUrl = table.ajax.url();
            if (!currentUrl || typeof currentUrl !== 'string') {
                console.error('DataTable AJAX URL is undefined or invalid');
                $applyBtn.prop('disabled', false).html(originalText);
                return;
            }

            var baseUrl = currentUrl.split('?')[0];
            var newUrl = baseUrl + queryString;
            
            table.ajax.url(newUrl);
            table.ajax.reload(function() {
                $applyBtn.prop('disabled', false).html(originalText);

                var $drawer = $('#task_filter_drawer');
                if ($drawer.length && typeof KTDrawer !== 'undefined') {
                    var drawerInstance = KTDrawer.getInstance($drawer[0]);
                    if (drawerInstance) drawerInstance.hide();
                }

                if (typeof toastr !== 'undefined') {
                    toastr.success('Task filters applied successfully!');
                }

                updateFilterBadges(filters);
                $(document).trigger('taskFiltersApplied', [filters]);
            }, function() {
                $applyBtn.prop('disabled', false).html(originalText);
                if (typeof toastr !== 'undefined') {
                    toastr.error('Error applying task filters. Please try again.');
                }
            });
        } catch (error) {
            console.error('Error in applyTaskFilters:', error);
            $applyBtn.prop('disabled', false).html(originalText);
            if (typeof toastr !== 'undefined') {
                toastr.error('Error applying task filters. Please try again.');
            }
        }
    }

    window.resetTaskFilters = function() {
        var $form = $('#taskFilterForm');

        $form.find('input[type="text"], input[type="hidden"]').val('');
        $form.find('input[type="checkbox"]').prop('checked', false);
        $form.find('select[multiple]').val([]).trigger('change');

        // Clear date inputs with Flatpickr
        var $dateInputs = $form.find('input[name="date"], input[name="due_date"]');
        $dateInputs.each(function() {
            if (this._flatpickr) {
                this._flatpickr.clear();
            }
        });

        localStorage.removeItem(STORAGE_KEY);

        // Check if DataTable exists and is properly initialized
        var $table = $('#task-table');
        if (!$table.length) {
            console.error('Task table element not found');
            return;
        }

        try {
            var table = $table.DataTable();
            if (!table || !table.ajax || typeof table.ajax.url !== 'function') {
                console.error('DataTable AJAX not properly initialized');
                return;
            }

            var currentUrl = table.ajax.url();
            if (!currentUrl || typeof currentUrl !== 'string') {
                console.error('DataTable AJAX URL is undefined or invalid');
                return;
            }

            var baseUrl = currentUrl.split('?')[0];
            table.ajax.url(baseUrl);
            table.ajax.reload(function() {
                if (typeof toastr !== 'undefined') {
                    toastr.info('Task filters cleared successfully!');
                }

                updateFilterBadges({});
                var $drawer = $('#task_filter_drawer');
                if ($drawer.length && typeof KTDrawer !== 'undefined') {
                    var drawerInstance = KTDrawer.getInstance($drawer[0]);
                    if (drawerInstance) drawerInstance.hide();
                }
            });
        } catch (error) {
            console.error('Error in resetTaskFilters:', error);
            if (typeof toastr !== 'undefined') {
                toastr.error('Error resetting task filters. Please try again.');
            }
        }

        $(document).trigger('taskFiltersReset');
    };

    function initializeFiltersFromStorage() {
        try {
            var savedFilters = localStorage.getItem(STORAGE_KEY);
            if (!savedFilters) return;

            var filters = JSON.parse(savedFilters);
            var $form = $('#taskFilterForm');

            // Set date filters
            if (filters.date) {
                $form.find('input[name="date"]').val(filters.date);
                var $dateInput = $form.find('input[name="date"]');
                if ($dateInput.length && $dateInput[0]._flatpickr) {
                    $dateInput[0]._flatpickr.setDate(filters.date);
                }
            }

            if (filters.due_date) {
                $form.find('input[name="due_date"]').val(filters.due_date);
                var $dueDateInput = $form.find('input[name="due_date"]');
                if ($dueDateInput.length && $dueDateInput[0]._flatpickr) {
                    $dueDateInput[0]._flatpickr.setDate(filters.due_date);
                }
            }

            // Set checkbox filters
            if (filters.status) {
                $.each(filters.status, function(index, status) {
                    $form.find('input[name="status[]"][value="' + status + '"]').prop('checked', true);
                });
            }

            if (filters.priority) {
                $.each(filters.priority, function(index, priority) {
                    $form.find('input[name="priority[]"][value="' + priority + '"]').prop('checked', true);
                });
            }

            if (filters.category) {
                $.each(filters.category, function(index, category) {
                    $form.find('input[name="category[]"][value="' + category + '"]').prop('checked', true);
                });
            }

            if (filters.branch) {
                $.each(filters.branch, function(index, branch) {
                    $form.find('input[name="branch[]"][value="' + branch + '"]').prop('checked', true);
                });
            }

            // Set select filters
            if (filters.assigned_to) {
                var $assignedToSelect = $form.find('select[name="assigned_to[]"]');
                if ($assignedToSelect.length) {
                    $assignedToSelect.val(filters.assigned_to).trigger('change');
                }
            }

            if (filters.owner) {
                var $ownerSelect = $form.find('select[name="owner[]"]');
                if ($ownerSelect.length) {
                    $ownerSelect.val(filters.owner).trigger('change');
                }
            }

            if (hasAnyTaskFilters(filters)) {
                applySavedFiltersToDataTable(filters);
            }

            updateFilterBadges(filters);
        } catch (e) {
            console.error('Error loading saved task filters:', e);
            localStorage.removeItem(STORAGE_KEY);
        }
    }

    function hasAnyTaskFilters(filters) {
        return filters.date ||
            filters.due_date ||
            (filters.status && filters.status.length) ||
            (filters.priority && filters.priority.length) ||
            (filters.category && filters.category.length) ||
            (filters.branch && filters.branch.length) ||
            (filters.assigned_to && filters.assigned_to.length) ||
            (filters.owner && filters.owner.length);
    }

    function applySavedFiltersToDataTable(filters) {
        var queryParams = [];

        if (filters.date) queryParams.push('date=' + encodeURIComponent(filters.date));
        if (filters.due_date) queryParams.push('due_date=' + encodeURIComponent(filters.due_date));
        $.each(filters.status, (i, v) => queryParams.push('status[]=' + encodeURIComponent(v)));
        $.each(filters.priority, (i, v) => queryParams.push('priority[]=' + encodeURIComponent(v)));
        $.each(filters.category, (i, v) => queryParams.push('category[]=' + encodeURIComponent(v)));
        $.each(filters.branch, (i, v) => queryParams.push('branch[]=' + encodeURIComponent(v)));
        $.each(filters.assigned_to, (i, v) => queryParams.push('assigned_to[]=' + encodeURIComponent(v)));
        $.each(filters.owner, (i, v) => queryParams.push('owner[]=' + encodeURIComponent(v)));

        var queryString = queryParams.length ? '?' + queryParams.join('&') : '';
        
        // Check if DataTable exists and is properly initialized
        var $table = $('#task-table');
        if (!$table.length) {
            console.error('Task table element not found');
            return;
        }

        try {
            var table = $table.DataTable();
            if (!table || !table.ajax || typeof table.ajax.url !== 'function') {
                console.error('DataTable AJAX not properly initialized');
                return;
            }

            var currentUrl = table.ajax.url();
            if (!currentUrl || typeof currentUrl !== 'string') {
                console.error('DataTable AJAX URL is undefined or invalid');
                return;
            }

            var baseUrl = currentUrl.split('?')[0];
            var newUrl = baseUrl + queryString;
            table.ajax.url(newUrl);
            table.ajax.reload();
        } catch (error) {
            console.error('Error in applySavedFiltersToDataTable:', error);
        }
    }

    function updateFiltersFromStorage() {
        try {
            var savedFilters = localStorage.getItem(STORAGE_KEY);
            if (savedFilters) {
                var filters = JSON.parse(savedFilters);
                updateFilterBadges(filters);
            }
        } catch (e) {
            console.error('Error updating filters from storage:', e);
        }
    }

    initializeFiltersFromStorage();
});
</script>
