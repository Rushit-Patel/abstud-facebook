<script>
    $(document).ready(function () {
        //Status Sub Status Ajax Call Start

       const $status = $('#lead_status');
        const $subStatus = $('#lead_sub_status');

        function loadSubStatuses(statusId, selectedSubStatus = null) {
            $subStatus.html('<option value="">Loading...</option>');

            if (statusId) {
                $.ajax({
                    url: '{{ route("team.ajax.lead.sub.status") }}',
                    type: 'GET',
                    data: { status_id: statusId },
                    success: function (response) {
                        if ($.isEmptyObject(response)) {
                            $subStatus.html('<option value="">Select lead sub status</option>');
                            $subStatus.parent().hide();
                            $subStatus.prop('required', false); // Remove required
                        } else {
                            let options = '<option value="">Select lead sub status</option>';
                            $.each(response, function (key, value) {
                                const selected = (key == selectedSubStatus) ? 'selected' : '';
                                options += `<option value="${key}" ${selected}>${value}</option>`;
                            });
                            $subStatus.html(options);
                            $subStatus.parent().show();
                            $subStatus.prop('required', true); // Add required
                        }
                    }
                });
            } else {
                $subStatus.html('<option value="">Select lead sub status</option>');
                $subStatus.parent().hide();
                $subStatus.prop('required', false); // Remove required
            }
        }

        // On status change
        $(document).on('change', '#lead_status', function () {
            let statusId = $(this).val();
            loadSubStatuses(statusId);
        });

        // Trigger on load if editing
        let initialStatusId = $('#lead_status').val();
        let selectedSubStatus = '{{ old("lead_sub_status", isset($clientLead->sub_status) ? $clientLead->sub_status : null) }}';

        if (initialStatusId) {
            loadSubStatuses(initialStatusId, selectedSubStatus);
        } else {
            $subStatus.html('<option value="">Select lead sub status</option>');
            $subStatus.parent().hide();
            $subStatus.prop('required', false); // Remove required if no initial status
        }


        //Status Sub Status Ajax Call End

        //Purpose Wise Country Coaching Hide Show Start
        const $purposeSelect = $('select[name="purpose"]');
        const $countryDiv = $('#countryDiv');
        const $SecondcountryDiv = $('#SecondcountryDiv');
        const $coachingDiv = $('#coachingDiv');

        const $countrySelect = $('select[name="country"]');
        const $coachingSelect = $('select[name="coaching"]');

        if ($purposeSelect.length === 0 || $countrySelect.length === 0 || $coachingSelect.length === 0) {
            console.error('Required elements not found. Check your form element names.');
            return;
        }
        function toggleFields(value) {
            if (value === '2') {
                // Show coaching
                $coachingDiv.show();
                $coachingSelect.prop('required', true);

                // Hide country
                $countryDiv.hide();
                $SecondcountryDiv.hide();
                $countrySelect.prop('required', false).val('').trigger('change');
            } else {
                // Show country
                $countryDiv.show();
                $SecondcountryDiv.show();
                $countrySelect.prop('required', true);

                // Hide coaching
                $coachingDiv.hide();
                $coachingSelect.prop('required', false).val('').trigger('change');
            }
        }
        $purposeSelect.on('change', function () {
            toggleFields($(this).val());
        });
        // Trigger once on page load
        toggleFields($purposeSelect.val());

        //Purpose Wise Country Coaching Hide Show End

        //Branch Wise Country State City Start

        function loadLocationData(branchId) {
            if (branchId) {
                $.ajax({
                    url: '{{ route("team.ajax.branch.country.state.city") }}',
                    type: 'GET',
                    data: { branch_id: branchId },
                    success: function (response) {
                        // Set country dropdown
                        let countrySelect = $('select[name="country_id"]');
                        countrySelect.empty().append(`<option value="">Select Country</option>`);
                        $.each(response.countries, function (id, name) {
                            let selected = (id == response.country_id) ? 'selected' : '';
                            countrySelect.append(`<option value="${id}" ${selected}>${name}</option>`);
                        });

                        // Set state dropdown
                        let stateSelect = $('select[name="state_id"]');
                        stateSelect.empty().append(`<option value="">Select State</option>`);
                        $.each(response.states, function (id, name) {
                            let selected = (id == response.state_id) ? 'selected' : '';
                            stateSelect.append(`<option value="${id}" ${selected}>${name}</option>`);
                        });

                        // Set city dropdown
                        let citySelect = $('select[name="city_id"]');
                        citySelect.empty().append(`<option value="">Select City</option>`);
                        $.each(response.cities, function (id, name) {
                            let selected = (id == response.city_id) ? 'selected' : '';
                            citySelect.append(`<option value="${id}" ${selected}>${name}</option>`);
                        });
                    }
                });
            }
        }


        function counselorBranchwise(branchId) {
        const selectedCounselorId = "{{ old('assign_owner', isset($clientLead) ? $clientLead->assign_owner : '') }}";
            if (branchId) {
                $.ajax({
                    url: '{{ route("team.ajax.branch.user") }}',
                    type: 'GET',
                    data: { branch_id: branchId },
                    success: function (response) {
                        // Set country dropdown

                        let counselorSelect = $('select[name="assign_owner"]');
                        counselorSelect.empty().append(`<option value="">Select counsellor</option>`);
                        $.each(response.counselors, function (id, name) {
                            let selected = (id == selectedCounselorId) ? 'selected' : '';
                            counselorSelect.append(`<option value="${id}">${name}</option>`);
                        });
                        counselorSelect.val(selectedCounselorId).trigger('change');
                    }
                });
            }
        }

    let branchSelect = $('select[name="branch"]');

    // If user has permission and dropdown is visible
    branchSelect.on('change', function () {
        let branchId = $(this).val();
        loadLocationData(branchId);
        counselorBranchwise(branchId);
    });

    // If branch dropdown is hidden (auto-filled for restricted users), trigger manually on page load
    if (branchSelect.is(':hidden')) {
        let branchId = branchSelect.val();
        loadLocationData(branchId);
        counselorBranchwise(branchId);
    }


        //Branch Wise Country State City End

        // Tagify Tags Start
        @isset($tags)
            var tagsArray = @json($tags);
        @else
            var tagsArray = [];
        @endisset

        var input = document.getElementById('tags');
        if (input) {
            var tagify = new Tagify(input, {
                whitelist: tagsArray,
                enforceWhitelist: true,
                dropdown: {
                    enabled: 0,
                    showOnFocus: true
                }
            });

            // Auto load old or DB values for edit mode
            var oldValue = input.value;
            if (oldValue) {
                tagify.addTags(oldValue.split(','));
            }
        }

        // Tagify Tags End

          // Repeater configuration Education Start
        $('#education-repeater').repeater({
            show: function () {
                $(this).slideDown();

                // Update IDs based on name attributes
                $(this).find('input, select, textarea').each(function () {
                    var name = $(this).attr('name');
                    if (name) {
                        var id = name.replace(/\[/g, '_').replace(/\]/g, '');
                        $(this).attr('id', id);
                    }
                });

                // Initialize Select2 for new selects
                $(this).find('select').each(function () {
                    var $select = $(this);
                    $select.parent().find('.select2-container--default').remove();

                    $select.select2({
                        width: '100%'
                    });
                });
            },
            hide: function (deleteElement) {
                // Destroy Select2 before removing
                $(this).find('select').each(function () {
                    if ($(this).hasClass('select2-hidden-accessible')) {
                        $(this).select2('destroy');
                    }
                });
                $(this).slideUp(deleteElement);
            }
        });
        // Repeater configuration Education End

        // On change of education level
        $(document).on('change', '.education_level', function () {
            var selectedLevel = $(this).val();
            var $parent = $(this).closest('[data-repeater-item]');
            handleEducationLevelChange($parent, selectedLevel);
        });

        // Function to load streams and toggle fields
        function handleEducationLevelChange($parent, selectedLevel, selectedStream = null) {
            if (!selectedLevel) return;

            var $streamSelect = $parent.find('.education_stream');

            $.ajax({
                url: '/team/get-education-streams/' + selectedLevel,
                type: 'GET',
                success: function (response) {
                    $streamSelect.empty().append('<option value="">Select Stream</option>');

                    $.each(response.streams || {}, function (key, value) {
                        const isSelected = selectedStream && selectedStream == key ? 'selected' : '';
                        $streamSelect.append(`<option value="${key}" ${isSelected}>${value}</option>`);
                    });

                    var requiredDetails = response.required_details || [];
                    var allFields = ['board', 'language', 'stream', 'passing_year', 'result', 'no_of_backlog', 'institute'];

                    allFields.forEach(function (field) {
                        var $fieldWrapper = $parent.find('.field-' + field);
                        if (requiredDetails.includes(field)) {
                            $fieldWrapper.show();
                            $fieldWrapper.find('input, select').attr('required', true);
                        } else {
                            $fieldWrapper.hide();
                            $fieldWrapper.find('input, select').removeAttr('required');
                        }
                    });
                }
            });
        }

        // Initialize existing data on page load (edit mode)
        $('[data-repeater-item]').each(function () {
            var $parent = $(this);
            var selectedLevel = $parent.find('.education_level').val();
            var selectedStream = $parent.find('.education_stream').val(); // already selected value from backend
            handleEducationLevelChange($parent, selectedLevel, selectedStream);
        });

        // Education Leval Wise Stream Ajax Call End
    });
</script>

{{-- Relative In Foreign Country? Jquery --}}
<script>
    $(document).ready(function () {
        var $checkbox = $('#is_relativeCheckbox');
        var $relativeFields = $('#relative-fields');
        var $requiredInputs = $('#relative_relationship, #relative_country, #visa_type');

        function toggleRelativeFields() {
            if (!$checkbox.length || !$relativeFields.length) return;

            if ($checkbox.is(':checked')) {
                $relativeFields.removeClass('hidden');
                $requiredInputs.each(function () {
                    $(this).attr('required', 'required');
                });
            } else {
                $relativeFields.addClass('hidden');
                $requiredInputs.each(function () {
                    $(this).removeAttr('required');
                });
            }
        }

        // Initial load check
        toggleRelativeFields();

        // On checkbox change
        $checkbox.on('change', toggleRelativeFields);
    });
</script>

{{-- Any Visa Rejection Country --}}
<script>
    $(document).ready(function () {
        const $isRejectionCheckbox = $('#is_visa_rejectionCheckbox');
        const $rejectionSection = $('#visa-rejection-section');

        function toggleRejectionSection() {
            if ($isRejectionCheckbox.is(':checked')) {
                $rejectionSection.removeClass('hidden');
                $rejectionSection.find('select, input').attr('required', 'required');
            } else {
                $rejectionSection.addClass('hidden');
                $rejectionSection.find('select, input').removeAttr('required');
            }
        }
        $isRejectionCheckbox.on('change', toggleRejectionSection);
        toggleRejectionSection();


        $('#visa-rejection-repeater').repeater({
            initEmpty: false,
            defaultValues: {
                'rejection_country': '',
                'rejection_month_year': '',
                'rejection_visa_type': ''
            },
            show: function () {
                $(this).slideDown();

                // Assign dynamic IDs
                $(this).find('input, select, textarea').each(function () {
                    var name = $(this).attr('name');
                    if (name) {
                        var id = name.replace(/\[/g, '_').replace(/\]/g, '');
                        $(this).attr('id', id);
                    }
                });

                // Re-initialize Select2
                $(this).find('select').each(function () {
                    var $select = $(this);
                    $select.parent().find('.select2-container').remove();
                    $select.select2({ width: '100%' });
                });
            },
            hide: function (deleteElement) {
                $(this).find('select').each(function () {
                    if ($(this).hasClass('select2-hidden-accessible')) {
                        $(this).select2('destroy');
                    }
                });
                $(this).slideUp(deleteElement);
            }
        });
    });
</script>

{{-- Any Visited Country Repeater--}}
<script>
    $(document).ready(function () {
        const $isVisitedCheckbox = $('#is_visitedCheckbox');
        const $visitedSection = $('#visited-section');

        function toggleVisitedSection() {
            if ($isVisitedCheckbox.is(':checked')) {
                $visitedSection.removeClass('hidden');
                $visitedSection.find('select, input').attr('required', 'required');
            } else {
                $visitedSection.addClass('hidden');
                $visitedSection.find('select, input').removeAttr('required');
            }
        }

        $isVisitedCheckbox.on('change', toggleVisitedSection);
        toggleVisitedSection(); // On load

        function initFlatpickr($container) {
            $container.find('.flatpickr').each(function () {
                flatpickr(this, {
                    dateFormat: "d/m/Y",
                });
            });
        }

        // Init repeater

        $('#visited-repeater').repeater({
            initEmpty: false,
            defaultValues: {
                'visited_country': '',
                'visited_visa_type': '',
                'start_date': '',
                'end_date': '',
            },
            show: function () {
                $(this).slideDown();

                // Assign dynamic IDs
                $(this).find('input, select, textarea').each(function () {
                    var name = $(this).attr('name');
                    if (name) {
                        var id = name.replace(/\[/g, '_').replace(/\]/g, '');
                        $(this).attr('id', id);
                    }
                });

                // Re-initialize Select2
                $(this).find('select').each(function () {
                    var $select = $(this);
                    $select.parent().find('.select2-container').remove();
                    $select.select2({ width: '100%' });
                });
                initFlatpickr($(this));
            },
            hide: function (deleteElement) {
                $(this).find('select').each(function () {
                    if ($(this).hasClass('select2-hidden-accessible')) {
                        $(this).select2('destroy');
                    }
                });
                $(this).slideUp(deleteElement);
            }
        });
    });
</script>

{{-- Employment Information Form Repeater --}}
<script>
    $(document).ready(function () {
        const $isEmploymentCheckbox = $('#is_employmentCheckbox');
        const $employmentSection = $('#employment-section');
        const $employmentRepeater = $('#employment-repeater');

        // Toggle section based on main checkbox
        function toggleEmploymentSection() {
            const isChecked = $isEmploymentCheckbox.is(':checked');
            $employmentSection.toggleClass('hidden', !isChecked);

            $employmentRepeater.find('.employment-item').each(function () {
                const $item = $(this);
                const $isWorking = $item.find('[name*="[is_working]"], [name="is_working"]');

                $item.find('[name*="[company_name]"], [name="company_name"]').prop('required', isChecked);
                $item.find('[name*="[designation]"], [name="designation"]').prop('required', isChecked);
                $item.find('[name*="[start_date]"], [name="start_date"]').prop('required', isChecked);

                toggleEndDateVisibility($item);
            });
        }

        // Show/hide end date & years based on employment + working status
        function toggleEndDateVisibility($item) {
            const isEmploymentChecked = $isEmploymentCheckbox.is(':checked');
            const isWorkingChecked = $item.find('[name*="[is_working]"], [name="is_working"]').is(':checked');

            const $endDateWrapper = $item.find('.field-end-date');
            const $noOfYearWrapper = $item.find('.field-no-of-year');
            const $endDate = $item.find('[name*="[end_date]"], [name="end_date"]');
            const $noOfYear = $item.find('[name*="[no_of_year]"], [name="no_of_year"]');

            if (isEmploymentChecked && !isWorkingChecked) {
                $endDateWrapper.removeClass('hidden');
                $noOfYearWrapper.removeClass('hidden');
                $endDate.prop('required', true);
                $noOfYear.prop('required', true);
            } else {
                $endDateWrapper.addClass('hidden');
                $noOfYearWrapper.addClass('hidden');
                $endDate.prop('required', false).val('');
                $noOfYear.prop('required', false).val('');
            }
        }

        // Flatpickr init
        function initializeFlatpickr($container) {
            $container.find('.flatpickr').each(function () {
                flatpickr(this, {
                    dateFormat: 'd/m/Y'
                });
            });
        }

        toggleEmploymentSection();

        // Main checkbox change
        $isEmploymentCheckbox.on('change', toggleEmploymentSection);

        // Existing working checkboxes
        $employmentRepeater.find('.employment-item').each(function () {
            const $item = $(this);
            $item.find('[name*="[is_working]"], [name="is_working"]').on('change', function () {
                toggleEndDateVisibility($item);
            });
            toggleEndDateVisibility($item);
        });

        // Repeater
        $('#employment-repeater').repeater({
            initEmpty: false,
            defaultValues: {
                'company_name': '',
                'designation': '',
                'start_date': ''
            },
            show: function () {
                $(this).slideDown();

                const $item = $(this);
                const index = $employmentRepeater.find('.employment-item').length - 1;

                // Rename inputs
                $item.find('input, select, textarea').each(function () {
                    let name = $(this).attr('name');
                    if (name && name.indexOf('[') === -1) {
                        name = `employment[${index}][${name}]`;
                        $(this).attr('name', name);
                        $(this).attr('id', name.replace(/\[/g, '_').replace(/\]/g, ''));
                    }
                });

                $item.addClass('employment-item');

                initializeFlatpickr($item);
                toggleEndDateVisibility($item);

                $item.find('[name*="[is_working]"], [name="is_working"]').on('change', function () {
                    toggleEndDateVisibility($item);
                });

                $item.find('select').each(function () {
                    const $select = $(this);
                    $select.parent().find('.select2-container').remove();
                    $select.select2({ width: '100%' });
                });

                $employmentRepeater.find('.employment-item').each(function (i) {
                    $(this).find('.remove-employment').toggleClass('hidden', i === 0);
                });
            },
            hide: function (deleteElement) {
                const $item = $(this);
                $item.find('select').each(function () {
                    if ($(this).hasClass('select2-hidden-accessible')) {
                        $(this).select2('destroy');
                    }
                });
                $item.slideUp(deleteElement);
            }
        });
    });
</script>

{{-- English Proficiency Test Js --}}

<script>
    $(document).ready(function () {
        function toggleModules() {
            $('.test-checkbox').each(function () {
                var $checkbox = $(this);
                var $target = $($checkbox.data('target'));

                if ($checkbox.is(':checked')) {
                    $target.show();
                    $target.find('input[type="text"]').prop('required', true);
                } else {
                    $target.hide();
                    $target.find('input[type="text"]').prop('required', false).val('');
                }
            });
        }

        toggleModules();
        $('.test-checkbox').on('change', toggleModules);
        function validateScore(input) {
            const $input = $(input);
            const $errorMsg = $input.closest('.relative').find('.error-message');
            const value = parseFloat($input.val());
            const min = parseFloat($input.data('min'));
            const max = parseFloat($input.data('max'));
            const step = parseFloat($input.data('step'));

            // Clear previous error
            $errorMsg.addClass('hidden').text('');
            $input.removeClass('border-red-500');

            if (!$input.val()) {
                return true; // Let required validation handle empty values
            }

            if (isNaN(value)) {
                $errorMsg.removeClass('hidden').text('Please enter a valid number');
                $input.addClass('border-red-500');
                return false;
            }

            if (value < min || value > max) {
                $errorMsg.removeClass('hidden').text(`Score must be between ${min} and ${max}`);
                $input.addClass('border-red-500');
                return false;
            }

            // Check if value follows the step increment
            const remainder = (value - min) % step;
            console.log(`Value: ${value}, Min: ${min}, Step: ${step}, Remainder: ${remainder}`);

            if (Math.abs(remainder) > 0.001 && Math.abs(remainder - step) > 0.001) {
                $errorMsg.removeClass('hidden').text(`Score must be in increments of ${step}`);
                $input.addClass('border-red-500');
                return false;
            }

            return true;
        }

        // Attach validation to score inputs
        $(document).on('input blur', '.score-input', function() {
            validateScore(this);
        });
    });
</script>
{{-- Passport Information --}}
<script>
    $(document).ready(function () {
        const $checkbox = $('#passportCheckbox');
        const $passportFields = $('#passportFields');
        const $requiredInputs = [
            $('#passport_number'),
            $('#passport_expiry_date'),
            $('#passport_copy')
        ].filter(function ($el) {
            return $el.length > 0; // Ensure element exists
        });

        function togglePassportFields() {
            if ($checkbox.length === 0 || $passportFields.length === 0) return;

            if ($checkbox.is(':checked')) {
                $passportFields.removeClass('hidden');
                $requiredInputs.forEach(function ($input) {
                    if ($input.attr('type') !== 'file') {
                        $input.attr('required', 'required');
                    }
                });
            } else {
                $passportFields.addClass('hidden');
                $requiredInputs.forEach(function ($input) {
                    $input.removeAttr('required');
                    if ($input.attr('type') !== 'file') {
                        $input.val('');
                    }
                });
            }
        }

        // Initial check
        togglePassportFields();

        // Bind change event
        $checkbox.on('change', togglePassportFields);
    });
</script>

{{-- Assign Owner Dropdown --}}
@if(isset($userCounselors))
    <script>
        $(document).on('click', '.assignOwner', function (e) {
            e.preventDefault();
            e.stopPropagation();

            var leadId = $(this).data('id');
            var $button = $(this);

            // Close any existing dropdowns
            $('.custom-assign-dropdown').remove();

            // Get button position
            var buttonOffset = $button.offset();
            var buttonHeight = $button.outerHeight();
            var buttonWidth = $button.outerWidth();

            // Create custom dropdown HTML with high z-index
            var dropdownHtml = `
                <div class="custom-assign-dropdown" id="assignDropdown_${leadId}" style="
                    position: fixed;
                    top: ${buttonOffset.top + buttonHeight + 5}px;
                    left: ${buttonOffset.left}px;
                    z-index: 99999;
                    min-width: 280px;
                    background: white;
                    border: 1px solid #e5e7eb;
                    border-radius: 8px;
                    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
                    padding: 16px;
                    font-size: 14px;
                    animation: fadeInDown 0.2s ease-out;
                ">
                    <x-team.forms.select name="counselorSelect_${leadId}" id="counselorSelect_${leadId}" label="Assign Counselor" :options="$userCounselors" />

                    <div style="display: flex; gap: 8px; margin-top: 16px;">
                        <button class="kt-btn kt-btn-sm kt-btn-primary assign-confirm" data-lead-id="${leadId}" >
                            <i class="ki-filled ki-check" style="font-size: 12px;"></i> Assign
                        </button>
                        <button class="kt-btn kt-btn-sm kt-btn-secondary assign-cancel">
                            <i class="ki-filled ki-cross" style="font-size: 12px;"></i> Cancel
                        </button>
                    </div>
                </div>
            `;

            // Add CSS animation if not already present
            if (!$('#assignDropdownCSS').length) {
                $('head').append(`
                    <style id="assignDropdownCSS">
                        @keyframes fadeInDown {
                            0% {
                                opacity: 0;
                                transform: translateY(-10px);
                            }
                            100% {
                                opacity: 1;
                                transform: translateY(0);
                            }
                        }
                    </style>
                `);
            }

            // Append dropdown to body for proper positioning
            $('body').append(dropdownHtml);

            // Adjust position if dropdown goes off screen
            var $dropdown = $('#assignDropdown_' + leadId);
            var dropdownWidth = $dropdown.outerWidth();
            var dropdownHeight = $dropdown.outerHeight();
            var windowWidth = $(window).width();
            var windowHeight = $(window).height();
            var scrollTop = $(window).scrollTop();

            // Adjust horizontal position if off-screen
            if (buttonOffset.left + dropdownWidth > windowWidth) {
                $dropdown.css('left', windowWidth - dropdownWidth - 10);
            }

            // Adjust vertical position if off-screen
            if (buttonOffset.top + buttonHeight + dropdownHeight > windowHeight + scrollTop) {
                $dropdown.css('top', buttonOffset.top - dropdownHeight - 5);
            }

            // Focus on select
            setTimeout(function() {
                $('#counselorSelect_' + leadId).focus();
            }, 100);
        });

        // Handle assign confirmation
        $(document).on('click', '.assign-confirm', function (e) {
            e.preventDefault();
            e.stopPropagation();

            var leadId = $(this).data('lead-id');
            var selectedCounselor = $('#counselorSelect_' + leadId).val();
            var selectedCounselorName = $('#counselorSelect_' + leadId + ' option:selected').text();

            if (!selectedCounselor) {
                // Highlight the select field
                $('#counselorSelect_' + leadId).css({
                    'border-color': '#ef4444',
                    'box-shadow': '0 0 0 3px rgba(239, 68, 68, 0.1)'
                });
                alert('Please select a counselor first.');
                return;
            }

            // Show loading state
            $(this).html('<i class="ki-filled ki-loading" style="animation: spin 1s linear infinite;"></i> Assigning...').prop('disabled', true);

            // AJAX call to assign the owner
            $.ajax({
                url: '{{ route("team.lead.assign.owner") }}',
                type: 'POST',
                data: {
                    lead_id: leadId,
                    counselor_id: selectedCounselor,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        // Hide the dropdown
                        $('.custom-assign-dropdown').remove();
                        KTToast.show({
                            message: response.success,
                            icon: '<i class="ki-filled ki-check text-success text-xl"></i>',
                            pauseOnHover: true,
                            variant: "success",
                        });

                        // Optionally reload the datatable or update the row

                        var table = $('#lead-table').DataTable();
                        table.ajax.reload();
                    } else {
                        KTToast.show({
                            message: response.error,
                            pauseOnHover: true,
                            variant: "error",
                        });
                    }
                },
                error: function(xhr, status, error) {
                    KTToast.show({
                        message: 'Failed to assign owner. Please try again.',
                        pauseOnHover: true,
                        variant: "error",
                    });
                },
                complete: function() {
                    // Reset button state
                    $('.assign-confirm').html('<i class="ki-filled ki-check"></i> Assign').prop('disabled', false);
                }
            });
        });

        // Handle cancel
        $(document).on('click', '.assign-cancel', function (e) {
            e.preventDefault();
            e.stopPropagation();
            $('.custom-assign-dropdown').remove();
        });

        // Hide dropdown when clicking outside
        $(document).on('click', function(e) {
            if (!$(e.target).closest('.custom-assign-dropdown, .assignOwner').length) {
                $('.custom-assign-dropdown').remove();
            }
        });

        // Hide dropdown on escape key
        $(document).on('keydown', function(e) {
            if (e.key === 'Escape') {
                $('.custom-assign-dropdown').remove();
            }
        });

        // Handle window resize and scroll
        $(window).on('resize scroll', function() {
            $('.custom-assign-dropdown').remove();
        });
    </script>
@endif

<script>
    $(document).ready(function () {
        function fetchUsersByBranches(branchIds) {
            $('#owner').html('<option>Loading...</option>');

            if (branchIds.length > 0) {
                $.ajax({
                    url: '{{ route("team.get.users.by.branch") }}',
                    method: 'GET',
                    data: { branch_ids: branchIds },
                    success: function (response) {
                        let options = '<option value="">Select User</option>';
                        response.forEach(function (user) {
                            options += `<option value="${user.id}">${user.name}</option>`;
                        });
                        $('#owner').html(options);
                    },
                    error: function () {
                        $('#owner').html('<option>Error loading users</option>');
                    }
                });
            } else {
                $('#owner').html('<option value="">Select User</option>');
            }
        }

        // For 'lead:show-all' -> Checkbox selection
        $(document).on('change', 'input[name="branch[]"]', function () {
            let selectedBranches = [];
            $('input[name="branch[]"]:checked').each(function () {
                selectedBranches.push($(this).val());
            });
            fetchUsersByBranches(selectedBranches);
        });

        // For 'lead:show-branch' -> Hidden input, trigger fetch on page load
        @haspermission('lead:show-branch')
            let branchId = $('input[name="branch[]"]').val();
            if (branchId) {
                fetchUsersByBranches([branchId]);
            }
        @endhaspermission
    });
</script>

{{-- Whatsapp Copy --}}
<script>
    function copyMobileToWhatsapp() {
        const mobileInput = document.querySelector('input[name="mobile_no"]');
        const whatsappInput = document.querySelector('input[name="whatsapp_no"]');
        if (mobileInput && whatsappInput) {
            whatsappInput.value = mobileInput.value;
        }
    }
</script>

{{-- Ajax Call Coaching Wise Batch Single --}}
<script>
    $(document).ready(function () {
        // Hidden input se batch id lena
        let selectedBatchId = $('#selected_batch').val();

        $('#coaching_select').on('change', function () {
            var coachingId = $(this).val();
            $('#batch_select').empty().append('<option value="">Loading...</option>');

            if (coachingId) {
                $.ajax({
                    url: '{{ route('team.get.coaching.batch') }}',
                    type: 'GET',
                    data: { coaching_id: coachingId },
                    success: function (response) {
                        $('#batch_select').empty().append('<option value="">Select batch</option>');

                        $.each(response, function (key, batch) {
                            $('#batch_select').append(
                                $('<option>', {
                                    value: batch.id,
                                    //text: batch.name,
                                    text: batch.name + ' (' + batch.time + ')',
                                    selected: (batch.id == selectedBatchId) // ✅ Preselect
                                })
                            );
                        });
                    },
                    error: function () {
                        $('#batch_select').empty().append('<option value="">No batches found</option>');
                    }
                });
            } else {
                $('#batch_select').empty().append('<option value="">Select batch</option>');
            }
        });

        // ✅ Edit mode: Agar coaching already selected hai to trigger karein
        let selectedCoachingId = $('#coaching_select').val();
        if (selectedCoachingId) {
            $('#coaching_select').trigger('change');
        }
    });
</script>

{{-- Ajax Call Coaching Wise Batch multiple --}}

<script>
    $(document).ready(function () {
        //let selectedBatchIds = $('#selected_batch_multiple').val().split(',');
        let selectedBatchIds = ($('#selected_batch_multiple').val() || '').split(',');

        $('#coaching_select_multiple').on('change', function () {
            var coachingIds = $(this).val(); // Multiple values array
            $('#batch_select_multiple').empty().append('<option value="">Loading...</option>');

            if (coachingIds && coachingIds.length > 0) {
                $.ajax({
                    url: '{{ route('team.get.coaching.batch-multiple') }}',
                    type: 'GET',
                    data: { coaching_id: coachingIds }, // send array
                    success: function (response) {
                        $('#batch_select_multiple').empty();

                        $.each(response, function (key, batch) {
                            $('#batch_select_multiple').append(
                                $('<option>', {
                                    value: batch.id,
                                    text: batch.name + ' (' + batch.time + ')',
                                    selected: selectedBatchIds.includes(String(batch.id))
                                })
                            );
                        });
                    },
                    error: function () {
                        $('#batch_select_multiple').empty().append('<option value="">No batches found</option>');
                    }
                });
            } else {
                $('#batch_select_multiple').empty().append('<option value="">Select batch</option>');
            }
        });

        // ✅ Edit mode: load batches if coaching already selected
        let selectedCoachingIds = $('#coaching_select_multiple').val();
        if (selectedCoachingIds && selectedCoachingIds.length > 0) {
            $('#coaching_select_multiple').trigger('change');
        }
    });
</script>

{{-- Coaching Material Ajax call --}}
<script>
    $(document).ready(function () {
        // Parse selected materials from hidden input
        let selectedMaterials = ($('#selected_coaching_materials').val() || '').split(',').map(Number);

        // Function to toggle material checkbox section visibility
        function toggleMaterialSection() {
            if ($('#is_material').is(':checked')) {
                $('#material-checkboxes').show();
            } else {
                $('#material-checkboxes').hide();
            }
        }

        // Trigger toggle on page load
        toggleMaterialSection();

        // Toggle material section on is_material checkbox change
        $('#is_material').on('change', function () {
            toggleMaterialSection();
        });

        // Load coaching materials via AJAX when coaching is selected
        $('#coaching_select').on('change', function () {
            const coachingId = $(this).val();

            if (coachingId) {
                $.ajax({
                    url: '{{ route("team.get.coaching.material") }}',
                    method: 'GET',
                    data: {
                        coaching_id: coachingId,
                        selected_materials: selectedMaterials
                    },
                    success: function (response) {
                        $('#material-checkboxes').html(response.html);
                    },
                    error: function (xhr) {
                        console.error("AJAX Error:", xhr.responseText);
                    }
                });
            }
        });

        // Trigger coaching material fetch on page load
        $('#coaching_select').trigger('change');
    });
</script>


{{-- Attendence Search Ajax Call --}}

<script>
$(document).ready(function () {
    $('#searchBtn').on('click', function () {
        let date = $('#joining_date').val();
        let coachingIds = $('#coaching_select_multiple').val();
        let batchIds = $('#batch_select_multiple').val();

        if (!date && (!coachingIds || coachingIds.length === 0) && (!batchIds || batchIds.length === 0)) {
            alert('Please select at least one filter.');
            return;
        }

        $.ajax({
            url: "{{ route('team.search.attendance.coaching.batch') }}",
            type: "GET",
            data: {
                joining_date: date,
                coaching_id: coachingIds,
                batch_id: batchIds
            },
            beforeSend: function () {
                $('#searchBtn').text('Searching...').prop('disabled', true);
            },
            success: function (response) {
                if (response.status) {
                    $('#searchResults').html(response.html); // Blade view HTML
                }
            },
            complete: function () {
                $('#searchBtn').text('Search').prop('disabled', false);
            },
            error: function () {
                alert('Something went wrong. Please try again.');
            }
        });
    });
});

</script>

{{-- Exam Date Booking --}}
<script>
    $(document).on('change', '#english_proficiency_test_id', function () {
        let testId = $(this).val();
        let examModeSelect = $('#exam_mode_id');
        let resultDaysWrapper = $('#result_days_wrapper');
        let resultDaysInput = $('#result_days');

        // Get selected value from Blade (edit mode or old input)
        let selectedExamModeId = $('#exam_mode_id').data('selected');

        // Reset values
        examModeSelect.empty().append('<option value="">Select exam mode</option>');
        resultDaysWrapper.hide();
        resultDaysInput.val('');

        if (testId) {
            $.ajax({
                url: "{{ route('team.exam.modes', ':id') }}".replace(':id', testId),
                type: 'GET',
                success: function (data) {
                    $.each(data.modes, function (id, name) {
                        let selectedAttr = (id == selectedExamModeId) ? 'selected' : '';
                        examModeSelect.append(`<option value="${id}" ${selectedAttr}>${name}</option>`);
                    });

                    if (data.result_days) {
                        resultDaysInput.val(data.result_days);
                        resultDaysWrapper.show();
                    }
                }
            });
        }
    });

    // Trigger change on page load if edit mode
    $(document).ready(function () {
        if ($('#english_proficiency_test_id').val()) {
            $('#english_proficiency_test_id').trigger('change');
        }
    });

</script>

