/**
 * Reusable Location AJAX Handler for Country/State/City Dropdowns
 * 
 * Usage:
 * LocationAjax.init({
 *     countrySelector: '#country_id',
 *     stateSelector: '#state_id', 
 *     citySelector: '#city_id',
 *     statesRoute: '/team/settings/company/states/',
 *     citiesRoute: '/team/settings/company/cities/'
 * });
 */

window.LocationAjax = (function($) {
    'use strict';

    let config = {
        countrySelector: '#country_id',
        stateSelector: '#state_id',
        citySelector: '#city_id',
        statesRoute: '/team/settings/company/states/',
        citiesRoute: '/team/settings/company/cities/',
        loadingText: 'Loading...',
        selectText: 'Select',
        noDataText: 'No data available'
    };

    /**
     * Initialize the location AJAX functionality
     * @param {Object} options - Configuration options
     */
    function init(options = {}) {
        // Merge options with default config
        config = $.extend({}, config, options);
        
        // Bind event handlers
        bindEvents();
        
        console.log('LocationAjax initialized with config:', config);
    }

    /**
     * Bind event handlers for dropdowns
     */
    function bindEvents() {
        // Country change handler
        $(document).on('change', config.countrySelector, function() {
            const countryId = $(this).val();
            handleCountryChange(countryId);
        });

        // State change handler  
        $(document).on('change', config.stateSelector, function() {
            const stateId = $(this).val();
            handleStateChange(stateId);
        });
    }

    /**
     * Handle country selection change
     * @param {string|number} countryId - Selected country ID
     */
    function handleCountryChange(countryId) {
        const $stateSelect = $(config.stateSelector);
        const $citySelect = $(config.citySelector);

        // Reset dependent dropdowns
        resetDropdown($stateSelect, config.selectText + ' State');
        resetDropdown($citySelect, config.selectText + ' City');

        if (!countryId) {
            return;
        }

        // Show loading state
        setLoadingState($stateSelect, true);

        // Make AJAX request for states
        const statesUrl = config.statesRoute + countryId;
        
        $.ajax({
            url: statesUrl,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                setLoadingState($stateSelect, false);
                populateDropdown($stateSelect, response, 'Select State');
            },
            error: function(xhr, status, error) {
                setLoadingState($stateSelect, false);
                handleAjaxError($stateSelect, 'Error loading states', xhr, status, error);
            }
        });
    }

    /**
     * Handle state selection change
     * @param {string|number} stateId - Selected state ID
     */
    function handleStateChange(stateId) {
        const $citySelect = $(config.citySelector);

        // Reset city dropdown
        resetDropdown($citySelect, config.selectText + ' City');

        if (!stateId) {
            return;
        }

        // Show loading state
        setLoadingState($citySelect, true);

        // Make AJAX request for cities
        const citiesUrl = config.citiesRoute + stateId;
        
        $.ajax({
            url: citiesUrl,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                setLoadingState($citySelect, false);
                populateDropdown($citySelect, response, 'Select City');
            },
            error: function(xhr, status, error) {
                setLoadingState($citySelect, false);
                handleAjaxError($citySelect, 'Error loading cities', xhr, status, error);
            }
        });
    }

    /**
     * Reset dropdown to empty state
     * @param {jQuery} $dropdown - Dropdown element
     * @param {string} placeholder - Placeholder text
     */
    function resetDropdown($dropdown, placeholder) {
        $dropdown.empty().append(`<option value="">${placeholder}</option>`);
        $dropdown.prop('disabled', false);
    }

    /**
     * Set loading state for dropdown
     * @param {jQuery} $dropdown - Dropdown element
     * @param {boolean} loading - Loading state
     */
    function setLoadingState($dropdown, loading) {
        if (loading) {
            $dropdown.prop('disabled', true);
            $dropdown.empty().append(`<option value="">${config.loadingText}</option>`);
        } else {
            $dropdown.prop('disabled', false);
        }
    }

    /**
     * Populate dropdown with data
     * @param {jQuery} $dropdown - Dropdown element
     * @param {Array} data - Array of objects with id and name properties
     * @param {string} placeholder - Placeholder text
     */
    function populateDropdown($dropdown, data, placeholder) {
        $dropdown.empty();
        $dropdown.append(`<option value="">${placeholder}</option>`);

        if (data && data.length > 0) {
            $.each(data, function(index, item) {
                $dropdown.append(`<option value="${item.id}">${item.name}</option>`);
            });
        } else {
            $dropdown.append(`<option value="" disabled>${config.noDataText}</option>`);
        }
    }

    /**
     * Handle AJAX errors
     * @param {jQuery} $dropdown - Dropdown element
     * @param {string} message - Error message
     * @param {Object} xhr - XMLHttpRequest object
     * @param {string} status - Error status
     * @param {string} error - Error details
     */
    function handleAjaxError($dropdown, message, xhr, status, error) {
        console.error('LocationAjax Error:', {
            message: message,
            status: status,
            error: error,
            response: xhr.responseText
        });

        // Reset dropdown with error state
        $dropdown.empty().append(`<option value="" disabled>Error loading data</option>`);
        
        // Show toast notification if available
        if (window.showToast && typeof window.showToast.error === 'function') {
            window.showToast.error(message + '. Please try again.');
        }
    }

    /**
     * Get selected values from all location dropdowns
     * @returns {Object} Object with country_id, state_id, city_id
     */
    function getSelectedValues() {
        return {
            country_id: $(config.countrySelector).val(),
            state_id: $(config.stateSelector).val(),
            city_id: $(config.citySelector).val()
        };
    }

    /**
     * Set selected values for dropdowns (useful for editing forms)
     * @param {Object} values - Object with country_id, state_id, city_id
     */
    function setSelectedValues(values) {
        if (values.country_id) {
            $(config.countrySelector).val(values.country_id).trigger('change');
            
            // Wait for states to load, then set state value
            setTimeout(function() {
                if (values.state_id) {
                    $(config.stateSelector).val(values.state_id).trigger('change');
                    
                    // Wait for cities to load, then set city value
                    setTimeout(function() {
                        if (values.city_id) {
                            $(config.citySelector).val(values.city_id);
                        }
                    }, 500);
                }
            }, 500);
        }
    }

    // Public API
    return {
        init: init,
        getSelectedValues: getSelectedValues,
        setSelectedValues: setSelectedValues,
        handleCountryChange: handleCountryChange,
        handleStateChange: handleStateChange
    };

})(jQuery);
