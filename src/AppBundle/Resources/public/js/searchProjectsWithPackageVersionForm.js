/**
 * Update the version select options with available versions of selected package.
 */
$('.js-packageSelect').on('change', function() {
    var selectedPackageName = $(this).val();
    fetchAvailableVersionsForPackage(selectedPackageName, setVersionSelectOptions)
});

$(document).ready(function() {
    var selectedPackageName = $('.js-packageSelect').val();
    if (selectedPackageName != null && selectedPackageName.length > 3) {
        fetchAvailableVersionsForPackage(selectedPackageName, setVersionSelectOptions)
    }

    var $versionConstraintValueSelect = $('.js-versionConstraintValueSelect');
    var $versionConstraintOperatorSelect = $('.js-versionConstraintOperatorSelect');

    $versionConstraintOperatorSelect.prop('disabled', true);
    $versionConstraintValueSelect.prop('disabled', true);
});

function setVersionSelectOptions(versions) {
    var $versionConstraintValueSelect = $('.js-versionConstraintValueSelect');
    var $versionConstraintOperatorSelect = $('.js-versionConstraintOperatorSelect');

    if ($versionConstraintOperatorSelect.val() === '') {
        $versionConstraintOperatorSelect.val('all');
    }

    $versionConstraintOperatorSelect.prop('disabled', false);
    $versionConstraintValueSelect.prop('disabled', false);

    // Clear the old options
    $versionConstraintValueSelect.find('option').remove();

    // Load the new options
    versions.forEach( function (normalizedVersionString) {
        $versionConstraintValueSelect.append('<option value="' + normalizedVersionString + '">' + normalizedVersionString + '</option>');
    });

    if ($versionConstraintValueSelect.attr('data-originally-selected-version')) {
        $versionConstraintValueSelect.val($versionConstraintValueSelect.attr('data-originally-selected-version'));
    }
}

function fetchAvailableVersionsForPackage(name, callback) {
    $.ajax({
        url: "/package-versions/" + name,
        dataType: "json",
        success: function(data) {
            callback(data.versions);
        }
    });
}
