/**
 * Main JavaScript for Asset Management System
 */

$(document).ready(function() {
    // Auto-dismiss alerts after 5 seconds
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);

    // Confirmation dialogs for delete actions
    $('.confirm-delete').on('click', function(e) {
        if (!confirm('Are you sure you want to delete this item? This action cannot be undone.')) {
            e.preventDefault();
            return false;
        }
    });

    // Confirmation dialogs for status changes
    $('.confirm-action').on('click', function(e) {
        var message = $(this).data('message') || 'Are you sure you want to perform this action?';
        if (!confirm(message)) {
            e.preventDefault();
            return false;
        }
    });

    // Show loading spinner on form submit
    $('form').on('submit', function() {
        showLoader();
    });

    // Add asset row (for multiple assets submission)
    $('#addAssetRow').on('click', function() {
        var assetRow = $('.asset-row:first').clone();
        assetRow.find('input, select').val('');
        assetRow.find('.remove-asset').show();
        $('.asset-container').append(assetRow);
    });

    // Remove asset row
    $(document).on('click', '.remove-asset', function() {
        $(this).closest('.asset-row').remove();
    });

    // Form validation
    $('form[data-validate="true"]').on('submit', function(e) {
        var isValid = true;
        
        $(this).find('input[required], select[required], textarea[required]').each(function() {
            if ($(this).val() === '') {
                isValid = false;
                $(this).addClass('is-invalid');
            } else {
                $(this).removeClass('is-invalid');
            }
        });

        // Email validation
        $(this).find('input[type="email"]').each(function() {
            var email = $(this).val();
            var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (email && !emailPattern.test(email)) {
                isValid = false;
                $(this).addClass('is-invalid');
                if ($(this).next('.invalid-feedback').length === 0) {
                    $(this).after('<div class="invalid-feedback">Please enter a valid email address.</div>');
                }
            }
        });

        // Phone validation
        $(this).find('input[type="tel"]').each(function() {
            var phone = $(this).val();
            var phonePattern = /^[0-9]{10}$/;
            if (phone && !phonePattern.test(phone)) {
                isValid = false;
                $(this).addClass('is-invalid');
                if ($(this).next('.invalid-feedback').length === 0) {
                    $(this).after('<div class="invalid-feedback">Please enter a valid 10-digit phone number.</div>');
                }
            }
        });

        if (!isValid) {
            e.preventDefault();
            hideLoader();
            alert('Please fill in all required fields correctly.');
            return false;
        }
    });

    // Remove validation error on input
    $('input, select, textarea').on('input change', function() {
        $(this).removeClass('is-invalid');
    });

    // DataTable initialization (if jQuery DataTables is loaded)
    if ($.fn.DataTable) {
        $('.data-table').DataTable({
            "pageLength": 25,
            "ordering": true,
            "searching": true,
            "responsive": true
        });
    }

    // Tooltip initialization
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Toggle sidebar on mobile
    $('#menu-toggle').on('click', function() {
        $('#sidebar-wrapper').toggleClass('toggled');
    });
});

/**
 * Show loading spinner
 */
function showLoader() {
    var spinner = '<div class="spinner-overlay show"><div class="spinner-border text-light" role="status"><span class="visually-hidden">Loading...</span></div></div>';
    if ($('.spinner-overlay').length === 0) {
        $('body').append(spinner);
    } else {
        $('.spinner-overlay').addClass('show');
    }
}

/**
 * Hide loading spinner
 */
function hideLoader() {
    $('.spinner-overlay').removeClass('show');
}

/**
 * Format date to dd-mm-yyyy
 */
function formatDate(dateString) {
    var date = new Date(dateString);
    var day = String(date.getDate()).padStart(2, '0');
    var month = String(date.getMonth() + 1).padStart(2, '0');
    var year = date.getFullYear();
    return day + '-' + month + '-' + year;
}

/**
 * Validate password strength
 */
function validatePassword(password) {
    var minLength = 8;
    var hasUpperCase = /[A-Z]/.test(password);
    var hasLowerCase = /[a-z]/.test(password);
    var hasNumber = /[0-9]/.test(password);
    var hasSpecialChar = /[!@#$%^&*(),.?":{}|<>]/.test(password);

    if (password.length < minLength) {
        return 'Password must be at least ' + minLength + ' characters long.';
    }
    if (!hasUpperCase) {
        return 'Password must contain at least one uppercase letter.';
    }
    if (!hasLowerCase) {
        return 'Password must contain at least one lowercase letter.';
    }
    if (!hasNumber) {
        return 'Password must contain at least one number.';
    }
    if (!hasSpecialChar) {
        return 'Password must contain at least one special character.';
    }
    
    return true;
}

/**
 * Print page content
 */
function printPage() {
    window.print();
}

/**
 * Export table to CSV
 */
function exportTableToCSV(filename) {
    var csv = [];
    var rows = document.querySelectorAll("table tr");
    
    for (var i = 0; i < rows.length; i++) {
        var row = [], cols = rows[i].querySelectorAll("td, th");
        
        for (var j = 0; j < cols.length; j++) {
            row.push(cols[j].innerText);
        }
        
        csv.push(row.join(","));
    }
    
    downloadCSV(csv.join("\n"), filename);
}

/**
 * Download CSV file
 */
function downloadCSV(csv, filename) {
    var csvFile;
    var downloadLink;
    
    csvFile = new Blob([csv], {type: "text/csv"});
    downloadLink = document.createElement("a");
    downloadLink.download = filename;
    downloadLink.href = window.URL.createObjectURL(csvFile);
    downloadLink.style.display = "none";
    document.body.appendChild(downloadLink);
    downloadLink.click();
}
