$('.js-findProjectsForm').on('submit', function(event) {
    event.preventDefault();

    $.ajax({
        url: "/api/projects/slug." + $('.js-packageSelect').val() + "/" + $('.js-versionConstraintOperatorSelect').val() + "/" + $('.js-versionConstraintValueSelect').val(),
        dataType: "json",
        success: function(data) {
            var list = $("<ul>").addClass('list-group');
            // Load the new options
            var package = data.package;
            var projects = data.projects; // Or whatever source information you're working with
            projects.forEach( function (project) {
                list.append("<li class='list-group-item'><a href='/project/" + slugify(project["name"]) + '.' + project["id"] + "'>" + project["name"] + "</a> with version " + project['packageVersion'] + "</li>");
            });
            $("#results").html(list).prepend("<h2>Matching Projects</h2><p>Using <a href='/package/" + slugify(package["name"]) + "." + package["id"] + "'>" + package["name"] + "</a></p>");
        }
    });
});

$('.js-packageSelect').on('change', function() {
    $.ajax({
        url: "/api/package/" + this.value + "/versions",
        dataType: "json",
        success: function(trackedVersions) {
            var $versionConstraintValueSelect = $('.js-versionConstraintValueSelect');
            var $versionConstraintOperatorSelect = $('.js-versionConstraintOperatorSelect');

            if($versionConstraintOperatorSelect.val() !== 'all') {
                $versionConstraintValueSelect.prop('disabled', false);
            }
            $versionConstraintOperatorSelect.prop('disabled', false);

            // Clear the old options
            $versionConstraintValueSelect.find('option').remove();

            // Load the new options
            for(var i = 0; i < trackedVersions.length; i++){
                $versionConstraintValueSelect.append('<option value="' + trackedVersions[i] + '">' + trackedVersions[i] + '</option>');
            }
        }
    });
});

$('.js-versionConstraintOperatorSelect').on('change', function() {
    if(this.value === 'all') {
        $('.js-versionConstraintValueSelect').prop('disabled', true);
    } else {
        $('.js-versionConstraintValueSelect').prop('disabled', false);
    }
});

function slugify(text) {
    return text.toString().toLowerCase()
        .replace(/\s+/g, '-')           // Replace spaces with -
        .replace(/[^\w\-]+/g, '')       // Remove all non-word chars
        .replace(/\-\-+/g, '-')         // Replace multiple - with single -
        .replace(/^-+/, '')             // Trim - from start of text
        .replace(/-+$/, '');            // Trim - from end of text
}
