document.addEventListener('DOMContentLoaded', function () {
    // Xử lý form submit trong #admin-container
    document.getElementById('admin-container').addEventListener('submit', function (event) {
        if (event.target.tagName === 'FORM') {
            event.preventDefault();
            const form = event.target;
            const action = form.getAttribute('action');
            const data = new URLSearchParams(new FormData(form)).toString();
            const url = action + '?' + data;
            loadContent(url);
        }
    });

    // Xử lý click vào link phân trang
    document.addEventListener('click', function (event) {
        if (event.target.classList.contains('paginate-link')) {
            event.preventDefault();
            const pageUrl = event.target.getAttribute('data-page');
            loadContent(pageUrl);
        }
    });

    // Hàm load nội dung qua AJAX
    function loadContent(url) {
        const xhttp = new XMLHttpRequest();
        xhttp.open('GET', url, true);
        xhttp.onload = function () {
            if (xhttp.status === 200) {
                document.getElementById('admin-container').innerHTML = xhttp.response;
            } else {
                document.getElementById('admin-container').innerHTML = '<p>Không thể tải trang.</p>';
            }
        };
        xhttp.send();
    }
});
