$(document).ready(function () {

    $('.select2').each(function () {
        const $select = $(this);
        const parentContainer = $select.closest('.select-container');

        $select.select2({
            dropdownParent: parentContainer,
            minimumResultsForSearch: $select.data('searchable') === false ? Infinity : 0
        });
    });


    $('.kt-select-display').click(function(){ 
        $(this).parent().find('.kt-select-search').find('input').focus();
    });
    $(".flatpickr").each(function () {
        const el = $(this);
        const minTime = el.data("min_time");
        const maxTime = el.data("max_time");
        const minDate = el.data("min_date");
        const maxDate = el.data("max_date");
        
        el.flatpickr({
            dateFormat: "d/m/Y",
            disableMobile: true,
            minDate: minDate || undefined,
            maxDate: maxDate || undefined,
            minTime: minTime || undefined,
            maxTime: maxTime || undefined
        });
    });
    $(".timepickr").each(function () {
        const el = $(this);
        const minTime = el.data("min_time");
        const maxTime = el.data("max_time");
        const minDate = el.data("min_date");
        const maxDate = el.data("max_date");
        
        el.flatpickr({
            dateFormat: "d/m/Y H:i",
            disableMobile: true,
            enableTime: true,
            time_24hr: true,
            minDate: minDate || undefined,
            maxDate: maxDate || undefined,
            minTime: minTime || undefined,
            maxTime: maxTime || undefined
        });
    });
    $(".range-flatpickr").flatpickr(
        {
            mode: "range",
            dateFormat: "d/m/Y",
        }
    );
    $(document).on("click", ".copy-text", function () {
        const text = $(this).text();
        navigator.clipboard.writeText(text).then(() => {
            KTToast.show({
                message: "Text copied to clipboard",
                pauseOnHover: true,
                variant: "success",
                duration: 5000,
                appearance: "light"
            });
        }).catch(err => {
            KTToast.show({
                message: "Failed to copy text",
                pauseOnHover: true,
                variant: "danger",
                duration: 5000,
                appearance: "light"
            });
        });
    });
  
});
