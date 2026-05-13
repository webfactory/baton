/**
 * Update the version select options with available versions of selected package.
 */
$('.js-packageSelect').on('change', function() {
    var selectedPackageName = $(this).val();
    fetchAvailableVersionsForPackage(selectedPackageName, setVersionSelectOptions)
});

$(document).ready(function() {
    var $versionConstraintValueSelect = $('.js-versionConstraintValueSelect');
    var $versionConstraintOperatorSelect = $('.js-versionConstraintOperatorSelect');

    $versionConstraintOperatorSelect.prop('disabled', true);
    $versionConstraintValueSelect.hide();

    var selectedPackageName = $('.js-packageSelect').val();
    if (selectedPackageName != null && selectedPackageName.length > 3) {
        fetchAvailableVersionsForPackage(selectedPackageName, setVersionSelectOptions)
    }

});

function setVersionSelectOptions(versions) {
    var $versionConstraintValueSelect = $('.js-versionConstraintValueSelect');
    var $versionConstraintOperatorSelect = $('.js-versionConstraintOperatorSelect');

    if ($versionConstraintOperatorSelect.val() === '') {
        $versionConstraintOperatorSelect.val('all');
    }

    // Clear the old options
    $versionConstraintValueSelect.find('option').remove();

    // Load the new options
    versions.forEach( function (normalizedVersionString) {
        $versionConstraintValueSelect.append('<option value="' + normalizedVersionString + '">' + normalizedVersionString + '</option>');
    });

    if ($versionConstraintValueSelect.attr('data-originally-selected-version')) {
        $versionConstraintValueSelect.val($versionConstraintValueSelect.attr('data-originally-selected-version'));
    }

    $versionConstraintOperatorSelect.prop('disabled', false);
    setVersionConstraintValueHiddenState();
}

$('.js-versionConstraintOperatorSelect').on('change', setVersionConstraintValueHiddenState);

function setVersionConstraintValueHiddenState() {
    var $versionConstraintValueSelect = $('.js-versionConstraintValueSelect');
    var selectedOperator = $('.js-versionConstraintOperatorSelect').val();

    if (selectedOperator === 'all') {
        $versionConstraintValueSelect.hide();
        $versionConstraintValueSelect.val('');
    } else {
        $versionConstraintValueSelect.show();
        if (!$versionConstraintValueSelect.val()) {
            $versionConstraintValueSelect.val($(".js-versionConstraintValueSelect option:first").val());
        } else {
        }
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
