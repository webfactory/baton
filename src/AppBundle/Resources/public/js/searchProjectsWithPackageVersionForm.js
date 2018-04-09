/**
 * Displays a list of projects matching the criteria in the submitted search form.
 */
$('.js-findProjectsForm').on('submit', function(event) {
    event.preventDefault();

    $.ajax({
        url: "/project/search/json/slug." + $('.js-packageSelect').val() + "/" + $('.js-versionConstraintOperatorSelect').val() + "/" + $('.js-versionConstraintValueSelect').val(),
        dataType: "json",
        success: function(data) {
            var list = $("<ul>").addClass('list-group mb-5');

            var package = data.package;
            var versions = package.versions;
            versions.forEach( function (version) {
                var listItem = "<li class='list-group-item'><strong>Version " + version.prettyVersion + "</strong><br/>Used by: ";

                version.projects.forEach( function (project, index) {
                    listItem += "<a href='/project/" + slugify(project.name) + '.' + project.id + "'>" + project.name + "</a>";
                    index !== version.projects.length - 1 ? listItem += ", " : listItem += "";
                });

                listItem += "</li>";
                list.append(listItem);
            });
            if(versions.length === 0) {
                list = "<p>No results.</p>";
            }
            $("#results").html(list).prepend("<h2>Matching Projects</h2><p>Using <a href='/package/" + slugify(package["name"]) + "." + package.id + "'>" + package.name + "</a></p>");
        }
    });
});

/**
 * Updates the version select options with available versions of this package.
 */
$('.js-packageSelect').on('change', function() {
    $.ajax({
        url: "/api/package/" + this.value + "/versions",
        dataType: "json",
        success: function(trackedVersions) {
            var $versionConstraintValueSelect = $('.js-versionConstraintValueSelect');
            var $versionConstraintOperatorSelect = $('.js-versionConstraintOperatorSelect');

            $versionConstraintOperatorSelect.val('all');
            $versionConstraintOperatorSelect.prop('disabled', false);
            $versionConstraintValueSelect.prop('disabled', false);

            // Clear the old options
            $versionConstraintValueSelect.find('option').remove();

            // Load the new options
            for(var i = 0; i < trackedVersions.length; i++){
                $versionConstraintValueSelect.append('<option value="' + trackedVersions[i] + '">' + trackedVersions[i] + '</option>');
            }
        }
    });
});

function slugify(text) {
    return text.toString().toLowerCase()
        .replace(/\s+/g, '-')           // Replace spaces with -
        .replace(/[^\w\-]+/g, '')       // Remove all non-word chars
        .replace(/\-\-+/g, '-')         // Replace multiple - with single -
        .replace(/^-+/, '')             // Trim - from start of text
        .replace(/-+$/, '');            // Trim - from end of text
}
