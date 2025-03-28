$(document).ready(function() {
    // Tải section mặc định
    loadSection('activation_codes');

    // Xử lý nhấp tab
    $('.nav-link').click(function(e) {
        e.preventDefault();
        $('.nav-link').removeClass('active');
        $(this).addClass('active');
        var section = $(this).data('section');
        loadSection(section);
    });

    // Xử lý phân trang
    $(document).on('click', '.paginate-link', function(e) {
        e.preventDefault();
        var section = $(this).data('section');
        var page = $(this).data('page');
        loadSection(section, page);
    });

    function loadSection(section, page = 1) {
        $.ajax({
            url: 'dashboard_content.php',
            type: 'GET',
            data: { section: section, page: page },
            success: function(data) {
                $('#content').fadeOut(200, function() {
                    $(this).html(data).fadeIn(200);
                });
                $.getScript('js/' + section + '.js');
            },
            error: function() {
                $('#content').html('<p>Đã xảy ra lỗi khi tải nội dung.</p>');
            }
        });
    }
});