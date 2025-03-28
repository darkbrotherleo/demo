<nav class="admin-menu">
    <ul>
        <li><i class="fa-solid fa-house"></i> <a href="#" data-page="dashboard_content.php" class="menu-link">Trang chủ</a></li>
        <li><i class="fa-solid fa-users"></i> <a href="#" data-page="users_content.php" class="menu-link">Quản lý người dùng</a></li>
        <li><i class="fa-solid fa-file-import"></i> <a href="#" data-page="import_admin.php" class="menu-link">Import Code</a></li>
        <li><i class="fa-solid fa-gear"></i> <a href="#" data-page="setting_admin.php" class="menu-link">Cài đặt</a></li>
    </ul>
</nav>
<!-- JavaScript xử lý AJAX cho phân trang -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.paginate-link').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const url = this.getAttribute('data-page');
                fetch(url)
                    .then(response => response.text())
                    .then(html => {
                        document.querySelector('.content-body').innerHTML = html;
                        // Gán lại sự kiện cho các link phân trang mới
                        attachPaginationEvents();
                    })
                    .catch(error => console.error('Error:', error));
            });
        });
    });

    // Hàm để gán sự kiện cho các link phân trang sau khi tải lại nội dung
    function attachPaginationEvents() {
        document.querySelectorAll('.paginate-link').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const url = this.getAttribute('data-page');
                fetch(url)
                    .then(response => response.text())
                    .then(html => {
                        document.querySelector('.content-body').innerHTML = html;
                        attachPaginationEvents(); // Gọi lại để gắn sự kiện cho các link mới
                    })
                    .catch(error => console.error('Error:', error));
            });
        });
    }
    </script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const defaultPage = 'dashboard_content.php'; // Trang mặc định
    const container = document.getElementById('admin-container');

    // Kiểm tra trạng thái trang active từ localStorage
    const savedPage = localStorage.getItem('activePage') || defaultPage;

    // Tải trang active khi reload hoặc lần đầu truy cập
    loadPage(savedPage);

    // Thêm sự kiện click cho các liên kết trong menu
    document.querySelectorAll('.admin-menu a').forEach(function (link) {
        link.addEventListener('click', function (event) {
            event.preventDefault(); // Ngăn hành vi mặc định của liên kết
            const page = this.getAttribute('data-page');
            localStorage.setItem('activePage', page); // Lưu trang đang active vào localStorage
            setActiveLink(this); // Đặt trạng thái active cho liên kết
            loadPage(page); // Tải nội dung trang
        });
    });

    // Xử lý form submit trong #admin-container (chọn số dòng)
    container.addEventListener('submit', function (event) {
        if (event.target.classList.contains('row-select-form')) {
            event.preventDefault();
            const form = event.target;
            const action = form.getAttribute('action');
            const data = new URLSearchParams(new FormData(form)).toString();
            const url = action + '?' + data;
            loadPage(url);
        }
    });

    // Xử lý click vào link phân trang
    document.addEventListener('click', function (event) {
        if (event.target.classList.contains('paginate-link')) {
            event.preventDefault();
            const pageUrl = event.target.getAttribute('data-page');
            loadPage(pageUrl);
        }
    });

    // Hàm tải nội dung qua AJAX
    function loadPage(page) {
        const xhr = new XMLHttpRequest();
        xhr.open('GET', page, true);
        xhr.onload = function () {
            if (xhr.status === 200) {
                container.innerHTML = xhr.responseText;
            } else {
                container.innerHTML = '<p>Không thể tải trang.</p>';
            }
        };
        xhr.onerror = function () {
            container.innerHTML = '<p>Đã xảy ra lỗi khi tải trang.</p>';
        };
        xhr.send();
    }

    // Hàm đặt trạng thái active cho liên kết
    function setActiveLink(activeLink) {
        document.querySelectorAll('.admin-menu a').forEach(function (link) {
            link.classList.remove('active'); // Loại bỏ trạng thái active khỏi tất cả các liên kết
        });
        activeLink.classList.add('active'); // Thêm trạng thái active cho liên kết được nhấn
    }

    // Đặt trạng thái active cho liên kết khi tải lại trang
    const activeLink = document.querySelector(`.admin-menu a[data-page="${savedPage}"]`);
    if (activeLink) {
        setActiveLink(activeLink);
    }
});
</script>

<style>
/* Thêm style cho trạng thái active */
.admin-menu a.active {
    font-weight: bold;
    color: #007bff;
    text-decoration: underline;
}
</style>