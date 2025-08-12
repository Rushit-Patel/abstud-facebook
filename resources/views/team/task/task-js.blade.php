<script>
    $(document).ready(function () {
        // Task Status Change Ajax Start
        const $status = $('#task_status');
        const $priority = $('#task_priority');

        function loadTaskSubData(statusId, selectedSubData = null) {
            if (statusId) {
                // You can add Ajax calls here if task has sub-statuses
                // Similar to how lead status works
            }
        }

        // On status change
        $(document).on('change', '#task_status', function () {
            let statusId = $(this).val();
            loadTaskSubData(statusId);
        });

        // Task Status Change Ajax End

        // Task Category and Priority handling
        const $categorySelect = $('select[name="category_id"]');
        const $prioritySelect = $('select[name="priority_id"]');

        // Branch Wise User Loading for Task Assignment Start
        function fetchUsersByBranches(branchIds) {
            $('#assigned_to').html('<option>Loading...</option>');

            if (branchIds && branchIds.length > 0) {
                $.ajax({
                    url: '{{ route("team.get.users.by.branch") }}',
                    type: 'GET',
                    data: { 'branch_ids[]': branchIds },
                    success: function (response) {
                        let options = '<option value="">Select Assignee</option>';
                        response.forEach(function (user) {
                            options += `<option value="${user.id}">${user.name}</option>`;
                        });
                        $('#assigned_to').html(options);
                    },
                    error: function () {
                        $('#assigned_to').html('<option>Error loading users</option>');
                    }
                });
            } else {
                $('#assigned_to').html('<option value="">Select Assignee</option>');
            }
        }

        // For 'task:show-all' -> Checkbox selection
        $(document).on('change', 'input[name="branch[]"]', function () {
            let selectedBranches = [];
            $('input[name="branch[]"]:checked').each(function () {
                selectedBranches.push($(this).val());
            });
            fetchUsersByBranches(selectedBranches);
        });

        // For 'task:show-branch' -> Hidden input, trigger fetch on page load
        @haspermission('task:show-branch')
            let branchId = $('input[name="branch[]"]').val();
            if (branchId) {
                fetchUsersByBranches([branchId]);
            }
        @endhaspermission

        // Branch Wise User Loading for Task Assignment End

        // Task Due Date Validation
        const $startDate = $('input[name="start_date"]');
        const $dueDate = $('input[name="due_date"]');

        if ($startDate.length && $dueDate.length) {
            $startDate.on('change', function() {
                const startDateValue = $(this).val();
                if (startDateValue) {
                    $dueDate.attr('min', startDateValue);
                    // If due date is before start date, clear it
                    if ($dueDate.val() && $dueDate.val() < startDateValue) {
                        $dueDate.val('');
                    }
                }
            });

            $dueDate.on('change', function() {
                const dueDateValue = $(this).val();
                const startDateValue = $startDate.val();
                
                if (startDateValue && dueDateValue && dueDateValue < startDateValue) {
                    alert('Due date cannot be before start date');
                    $(this).val('');
                }
            });
        }

        // Task Progress Slider
        const $progressSlider = $('input[name="progress"]');
        const $progressDisplay = $('#progress-display');
        
        if ($progressSlider.length && $progressDisplay.length) {
            $progressSlider.on('input', function() {
                $progressDisplay.text($(this).val() + '%');
            });
        }

        // Task Recurring Options
        const $isRecurring = $('input[name="is_recurring"]');
        const $recurringOptions = $('#recurring-options');

        if ($isRecurring.length && $recurringOptions.length) {
            $isRecurring.on('change', function() {
                if ($(this).is(':checked')) {
                    $recurringOptions.show();
                } else {
                    $recurringOptions.hide();
                    // Clear recurring fields
                    $recurringOptions.find('input, select').val('');
                }
            });

            // Trigger on load
            $isRecurring.trigger('change');
        }

        // Task Tags Handling (if using Tagify)
        @isset($taskTags)
            var taskTagsArray = @json($taskTags);
        @else
            var taskTagsArray = [];
        @endisset

        var taskTagsInput = document.getElementById('task_tags');
        if (taskTagsInput) {
            var taskTagify = new Tagify(taskTagsInput, {
                whitelist: taskTagsArray,
                enforceWhitelist: false,
                dropdown: {
                    enabled: 0,
                    showOnFocus: true
                }
            });

            // Auto load old or DB values for edit mode
            var oldTagValue = taskTagsInput.value;
            if (oldTagValue) {
                taskTagify.addTags(oldTagValue.split(','));
            }
        }

        // Task Time Logging
        const $estimatedHours = $('input[name="estimated_hours"]');
        const $actualHours = $('input[name="actual_hours"]');

        if ($estimatedHours.length && $actualHours.length) {
            $actualHours.on('change', function() {
                const estimated = parseFloat($estimatedHours.val()) || 0;
                const actual = parseFloat($(this).val()) || 0;
                
                if (actual > estimated && estimated > 0) {
                    const confirmation = confirm('Actual hours exceed estimated hours. Are you sure?');
                    if (!confirmation) {
                        $(this).val('');
                    }
                }
            });
        }

        // Task Assignment Multiple Users (if using Select2 or similar)
        if (typeof $ !== 'undefined' && $.fn.select2) {
            $('.task-assignee-select').select2({
                placeholder: 'Select assignees...',
                allowClear: true
            });
        }

        // Auto-save draft functionality (optional)
        let autoSaveTimer;
        const $taskForm = $('#taskForm');
        
        if ($taskForm.length) {
            $taskForm.find('input, textarea, select').on('change input', function() {
                clearTimeout(autoSaveTimer);
                autoSaveTimer = setTimeout(function() {
                    // Implement auto-save logic here if needed
                    console.log('Auto-saving task draft...');
                }, 5000); // Save after 5 seconds of inactivity
            });
        }

        // Task Dependencies handling (if implemented)
        const $dependsOn = $('select[name="depends_on[]"]');
        if ($dependsOn.length) {
            $dependsOn.on('change', function() {
                const selectedTasks = $(this).val() || [];
                // Add logic to prevent circular dependencies
            });
        }
    });
</script>

{{-- Task Attachment Handling --}}
<script>
    function addTaskAttachment() {
        const attachmentContainer = document.getElementById('task-attachments');
        if (attachmentContainer) {
            const attachmentCount = attachmentContainer.children.length;
            const attachmentHtml = `
                <div class="attachment-item flex items-center gap-2 mb-2">
                    <input type="file" name="attachments[]" class="form-control" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.xls,.xlsx">
                    <button type="button" onclick="removeAttachment(this)" class="btn btn-danger btn-sm">Remove</button>
                </div>
            `;
            attachmentContainer.insertAdjacentHTML('beforeend', attachmentHtml);
        }
    }

    function removeAttachment(button) {
        button.closest('.attachment-item').remove();
    }
</script>

{{-- Task Time Tracking --}}
<script>
    let taskTimer = {
        isRunning: false,
        startTime: null,
        elapsedTime: 0,
        intervalId: null
    };

    function startTaskTimer() {
        if (!taskTimer.isRunning) {
            taskTimer.startTime = Date.now() - taskTimer.elapsedTime;
            taskTimer.isRunning = true;
            
            taskTimer.intervalId = setInterval(updateTimerDisplay, 1000);
            
            document.getElementById('start-timer-btn').style.display = 'none';
            document.getElementById('stop-timer-btn').style.display = 'inline-block';
        }
    }

    function stopTaskTimer() {
        if (taskTimer.isRunning) {
            clearInterval(taskTimer.intervalId);
            taskTimer.isRunning = false;
            
            document.getElementById('start-timer-btn').style.display = 'inline-block';
            document.getElementById('stop-timer-btn').style.display = 'none';
            
            // Update actual hours field
            const totalHours = taskTimer.elapsedTime / (1000 * 60 * 60);
            const actualHoursField = document.querySelector('input[name="actual_hours"]');
            if (actualHoursField) {
                actualHoursField.value = totalHours.toFixed(2);
            }
        }
    }

    function updateTimerDisplay() {
        taskTimer.elapsedTime = Date.now() - taskTimer.startTime;
        const hours = Math.floor(taskTimer.elapsedTime / (1000 * 60 * 60));
        const minutes = Math.floor((taskTimer.elapsedTime % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((taskTimer.elapsedTime % (1000 * 60)) / 1000);
        
        const timerDisplay = document.getElementById('timer-display');
        if (timerDisplay) {
            timerDisplay.textContent = `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
        }
    }
</script>
