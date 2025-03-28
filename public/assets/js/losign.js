document.getElementById('registerForm').addEventListener('submit', function(e) {
    e.preventDefault();
    var formData = new FormData(this);
    fetch('register.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        alert(data);
        if (data === "Đăng ký thành công!") {
            window.location.href = 'login.php';
        }
    })
    .catch(error => console.error('Error:', error));
});