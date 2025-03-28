$(document).ready(function() {
    // Initialize any charts or specific functionality
    initializeCharts();
    loadRecentActivity();
});

function initializeCharts() {
    // Add chart initialization code here
}

function loadRecentActivity() {
    $.ajax({
        url: 'api/get-recent-activity.php',
        method: 'GET',
        success: function(data) {
            $('.activity-list').html(data);
        }
    });
}
