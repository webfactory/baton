/**
 * Displays a list of projects matching the criteria in the submitted search form.
 */
$('.js-findProjectsForm').on('submit', function(event) {
    event.preventDefault();

    var submittedPackage = $('.js-packageSelect').val();
    var submittedOperator = $('.js-versionConstraintOperatorSelect').val();
    var submittedVersionString = $('.js-versionConstraintValueSelect').val();

    $.ajax({
        url: "/usage-search/" + submittedPackage + ";html/" + submittedOperator + "/" + submittedVersionString,
        dataType: "html",
        success: function(resultsHtml) {
            $("#results").html(resultsHtml);
        }
    });
});


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
});

function setVersionSelectOptions(versions) {
    var $versionConstraintValueSelect = $('.js-versionConstraintValueSelect');
    var $versionConstraintOperatorSelect = $('.js-versionConstraintOperatorSelect');

    $versionConstraintOperatorSelect.val('all');
    $versionConstraintOperatorSelect.prop('disabled', false);
    $versionConstraintValueSelect.prop('disabled', false);

    // Clear the old options
    $versionConstraintValueSelect.find('option').remove();

    // Load the new options
    versions.forEach( function (normalizedVersionString) {
        $versionConstraintValueSelect.append('<option value="' + normalizedVersionString + '">' + normalizedVersionString + '</option>');
    });
}

function fetchAvailableVersionsForPackage(name, callback) {
    $.ajax({
        url: "/package/" + name + ";versions",
        dataType: "json",
        success: function(data) {
            callback(data.versions);
        }
    });
}
