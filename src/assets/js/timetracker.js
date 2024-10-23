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