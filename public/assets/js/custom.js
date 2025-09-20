$(function () {
    //     $(".date-picker").daterangepicker({
    //         singleDatePicker: true,
    //         showDropdowns: true,
    //         minYear: 1901,
    //         maxYear: parseInt(moment().format("YYYY"),12)
    //     }, function(start, end, label) {
    //         // var years = moment().diff(start, "years");
    //     }
    // );
});

var mainDataTable;
function initDataTable(
    table,
    ajaxUrl,
    columns,
    orderColumn = [0, "desc"],
    additionalData = {}
) {
    if ($.fn.DataTable.isDataTable(table)) {
        table.DataTable().destroy(); // Destroy existing instance
        table.empty(); // Optional: Clear table content before reinitialization
    }

    mainDataTable = table.DataTable({
        processing: true,
        serverSide: true,
        searchDelay: 1000,
        scrollX: true,
        ajax: {
            url: ajaxUrl,
            type: "GET",
            data: function (d) {
                // Additional data to send to the server can be added here
                $.extend(d, additionalData); // Combine default params with custom data
            },
        },
        columns: columns,
        order: [orderColumn],
        responsive: true,
        language: {
            emptyTable: "No data available in table", // Customize your message here
        },
    });
}

var mainDataTableOffline;
function initDataTableOffline(
    table,
    ajaxUrl,
    columns,
    orderColumn = [0, "desc"],
    additionalData = {}
) {
    mainDataTableOffline = table.DataTable({
        processing: true,
        serverSide: false,
        searchDelay: 1000,
        scrollX: true,
        ajax: {
            url: ajaxUrl,
            type: "GET",
            data: function (d) {
                // Additional data to send to the server can be added here
                $.extend(d, additionalData); // Combine default params with custom data
            },
        },
        columns: columns,
        order: [orderColumn],
        responsive: true,
        language: {
            emptyTable: "No data available in table",
        },
    });
}

function initDelete(deleteUrl, name) {
    var $btn = $(this);
    $btn.addClass("disabled");
    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });

    $(this).addClass("disabled");
    Swal.fire({
        title: "Are you sure you want to delete this " + name + "?",
        text: "If you delete this, it will be gone forever.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, delete it!",
    }).then((result) => {
        console.log("enterd");
        if (result.isConfirmed) {
            $.ajax({
                type: "DELETE",
                url: deleteUrl,
                success: function (data) {
                    Swal.fire({
                        title: "Deleted!",
                        text: "Your " + name + " has been deleted.",
                        icon: "success",
                    });
                    mainDataTable.ajax.reload();
                    $btn.removeClass("disabled");
                },
                error: function (e) {
                    $btn.removeClass("disabled");
                    errorAlert();
                },
            });
        }
        $btn.removeClass("disabled");
    });
}

function errorAlert(message = "") {
    Swal.fire({
        title: "Error!",
        text:
            message !== ""
                ? message
                : "Something went wrong. Please try again later.",
        icon: "error",
        confirmButtonText: "OK",
    });
}

function displayValidationErrors(xhr) {
    // Clear previous error messages
    $("#errorMessages").empty().addClass("d-none");
    $(".text-danger").empty();

    if (xhr.status === 422) {
        var errors = xhr.responseJSON.errors;
        var errorMessages = "<ul>";
        $.each(errors, function (field, messages) {
            errorMessages += `<li>${messages[0]}</li>`;
            $(`#${field}Error`).text(messages[0]);
        });
        errorMessages += "</ul>";

        $("#errorMessages").html(errorMessages).removeClass("d-none");
    } else {
        console.error("Error:", xhr.responseText);
        $("#errorMessages")
            .html("An unexpected error occurred.")
            .removeClass("d-none");

        // Display SweetAlert message for unexpected errors
        errorAlert();
    }
}

function setDropdown(element, url, callback) {
    var defaultOption = "--Select--";
    // Show a loading message or spinner
    $(element).html(`<option>Loading...</option>`);

    // Perform the AJAX request
    $.ajax({
        url: url,
        method: "GET",
        success: function (response) {
            // Clear the dropdown
            $(element).empty();

            // Add the default option
            $(element).append(`<option value="">${defaultOption}</option>`);

            let data = response;
            $.each(data, function (index, item) {
                $(element).append(`<option value="${index}">${item}</option>`);
            });
            if (callback) callback();
        },
        error: function (xhr, status, error) {
            console.error("Error fetching data:", error);
            // Show an error message
            $(element).html(`<option>Error loading options</option>`);
        },
    });
}

var timeout;
var delay = 1000;
$("#recordSearch").keyup(function () {
    clearTimeout(timeout);
    timeout = setTimeout(function () {
        mainDataTable.search($("#recordSearch").val()).draw();
    }, delay);
});

var timeoutOffline;
var delayOffline = 1000;
$("#recordSearchOffline").keyup(function () {
    clearTimeout(timeoutOffline);
    timeoutOffline = setTimeout(function () {
        mainDataTableOffline.search($("#recordSearchOffline").val()).draw();
    }, delayOffline);
});

// Global Checkbox
$("#globalCheckbox").on("change", function () {
    var isChecked = $(this).is(":checked");
    $('input[type="checkbox"]').prop("checked", isChecked);
});

$("#statusChange").on("change", function () {
    var selectedStatus = $(this).val();

    // Custom search function
    $.fn.dataTable.ext.search.push(function () {
        return selectedStatus;
    });

    // Redraw the table
    mainDataTable.draw();
});

function errorAlert() {
    Swal.fire({
        icon: "error",
        title: "Oops...",
        text: "Oops! Something went wrong. Please try again later or contact support.",
    });
}

function openModal($title, $elementID = 'recordModal', reset = true) {
    if (reset) {
        $("#recordForm")[0].reset(); // Reset form fields
        $("#recordForm .text-danger strong").text(''); // Clear validation error messages

        // Force recordId back to 0 for create
        $("#recordId").val(0);

        // ✅ Reset description textarea
        $("textarea[name=description]").val('');

        // ✅ Hide and clear image preview
        $("#existingImageContainer").hide();
        $("#existingImage").attr("src", "");
        $("#isRemoveImage").val(0);

        // ✅ Clear file input
        $("#imageInput").val("");
    }
    $("#modelHeading").html($title);

    // Bootstrap 5 way:
    const modalEl = document.getElementById($elementID);
    const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
    modal.show();
}

function closeModal($elementID)
{
    const modalEl = document.getElementById($elementID);
    const modal = bootstrap.Modal.getInstance(modalEl);
    modal.hide();
}

$("#recordCancel").on("click", function () {
    $("#recordForm")[0].reset();
    $("#recordModal").modal("hide");
});

function submitForm(formSelector, url, onSuccess, onError) {
    $(formSelector)
        .off("submit")
        .on("submit", function (e) {
            e.preventDefault();
            var $form = $(this);
            $form.addClass("disabled");
            var $btn = $form.find("button[type=submit]");

            // show spinner
            $btn.attr("data-kt-indicator", "on");
            $btn.prop("disabled", true);

            // Reset any previous error messages
            $form.find(".text-danger").children("strong").text("");
            $form.find(".is-invalid").removeClass("is-invalid");

            $.ajax({
                url: url,
                method: "POST",
                data: new FormData(this),
                contentType: false,
                cache: false,
                processData: false,
                success: function (response) {
                    $btn.removeAttr("data-kt-indicator").prop("disabled", false);
                    $form.removeClass("disabled");
                    if (response.success) {
                        Swal.fire({
                            title: "Success!",
                            text: response.message,
                            icon: "success",
                        });
                        if (onSuccess) onSuccess(response);
                        $("#recordForm")[0].reset();
                        $("#recordModal").modal("hide");
                        mainDataTable.ajax.reload(null, false);
                    } else {
                        Swal.fire({
                            icon: "error",
                            title: "Oops...",
                            text: response.message,
                        });
                    }
                },
                error: function (xhr) {
                    $btn.removeAttr("data-kt-indicator").prop("disabled", false);
                    $form.removeClass("disabled");
                    if (xhr.status === 422) {
                        var errors = xhr.responseJSON.errors;
                        $.each(errors, function (key, value) {
                            var input = $("#" + key);
                            var errorDiv = $("#" + key + "Error");

                            if (input.length) {
                                input.addClass("is-invalid");
                            }

                            if (errorDiv.length) {
                                errorDiv.children("strong").text(value[0]);
                            }
                        });
                    } else {
                        errorAlert();
                        if (onError) onError(xhr);
                    }
                },
            });
        });
}


function formatDate(dateStr) {
    if (!dateStr) return ""; // if null/empty return blank
        let d = new Date(dateStr);
    return isNaN(d.getTime())
        ? "" // if invalid date
        : d.toLocaleString("en-IN", {
            day: "2-digit",
            month: "short",
            year: "numeric",
            hour: "2-digit",
            minute: "2-digit",
        });
}
