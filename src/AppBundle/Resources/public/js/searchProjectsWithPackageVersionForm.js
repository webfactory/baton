/**
 * Displays a list of projects matching the criteria in the submitted search form.
 */
$('.js-findProjectsForm').on('submit', function(event) {
    event.preventDefault();

    $.ajax({
        url: "/package/" + $('.js-packageSelect').val() + ";json?operator=" + $('.js-versionConstraintOperatorSelect').val() + "&versionString=" + $('.js-versionConstraintValueSelect').val(),
        dataType: "json",
        success: function(data) {
            var list = $("<ul>").addClass('list-group mb-5');

            var package = data.package;
            var versions = package.versions;
            versions.forEach( function (version) {
                var listItem = "<li class='list-group-item'><strong>Version " + version.prettyVersion + "</strong><br/>Used by: ";

                version.projects.forEach( function (project, index) {
                    listItem += "<a href='/project/" + project.name + "'>" + project.name + "</a>";
                    index !== version.projects.length - 1 ? listItem += ", " : listItem += "";
                });

                listItem += "</li>";
                list.append(listItem);
            });
            if(versions.length === 0) {
                list = "<p>No results.</p>";
            }
            $("#results").html(list).prepend("<h2>Matching Projects</h2><p>Using <a href='/package/" + package.name + "'>" + package.name + "</a></p>");
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
