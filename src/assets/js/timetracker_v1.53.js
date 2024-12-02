/* 
Time Tracking
@author Andrew Zakharov https://github.com/ZakharovAndrew
*/
function getActivityHtml(activity) {
    return `<div class="timeline-element">
                <div>
                    <span class="timeline-icon">
                        <i class="badge badge-dot activity-${activity.id}"> </i>
                    </span>
                    <div class="timeline-content">
                        <h4 class="timeline-title">${activity.activity}</h4>
                        <p> ${activity.comment}</p>
                        <span class="timeline-date">${activity.time}</span>
                    </div>
                </div>
            </div>`;
}

// filter
$(document).on('keyup', '.filter-control', function() {
    let str = $(this).val().toLowerCase();
    let filter_item = $(this).data('filter-item');

    if (str == '') {
        $(filter_item).show();
        return;
    }

    $(filter_item).each(function(){
        if ($(this).text().toLowerCase().includes(str)) {
            $(this).show();
        } else {
            $(this).hide();
        }
    });        
});

$(".comment-templates-item").on('click', function() {
    let comment = $("#timetracking-comment").val();
    if (comment.trim() !== '') {
        comment += '.';
    }
    let commentTemplate = $(this);
    if (commentTemplate) {
        $("#timetracking-comment").val(comment + commentTemplate.text());
    }
    $("#comment-templates-modal").modal('hide');
});

// filter
$(document).on('keyup', '#comment-templates-filter', function() {
    let str = $('#comment-templates-filter').val().toLowerCase();

    if (str === '') {
        $('.comment-templates-item').show();
        return;
    }

    $('.comment-templates-item').each(function(){
        if ($(this).html().toLowerCase().includes(str)) {
            $(this).show();
        } else {
            $(this).hide();
        }
    });        
});

$('.field-timetracking-comment').append($("#comment-menu"));

$("#timetracking-activity_id").on('change', function() {
    let id = $(this).val();
    $("#comment-menu").hide();
    $(".comment-templates").hide();
    if ($(".comment-templates-"+id).length > 0) {
        $("#comment-templates-filter").val("");
        $("#comment-menu").show();
        $(".comment-templates-"+id).show();
    }
}
);