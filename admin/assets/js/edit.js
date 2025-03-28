document.addEventListener('DOMContentLoaded', function() {
    // Form validation
    const form = document.getElementById('editProductForm');
    
    form.addEventListener('submit', function(event) {
        let valid = true;
        const customerName = document.getElementById('customerName').value.trim();
        const email = document.getElementById('email').value.trim();
        const phoneNumber = document.getElementById('phoneNumber').value.trim();
        
        // Validate customer name
        if (customerName === '') {
            valid = false;
            showError('customerName', 'Vui lòng nhập tên khách hàng');
        } else {
            removeError('customerName');
        }
        
        // Validate email format
        if (email !== '' && !isValidEmail(email)) {
            valid = false;
            showError('email', 'Email không hợp lệ');
        } else {
            removeError('email');
        }
        
        // Validate phone number
        if (phoneNumber !== '' && !isValidPhone(phoneNumber)) {
            valid = false;
            showError('phoneNumber', 'Số điện thoại không hợp lệ');
        } else {
            removeError('phoneNumber');
        }
        
        if (!valid) {
            event.preventDefault();
        }
    });
    
    // Helper functions
    function isValidEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }
    
    function isValidPhone(phone) {
        // Kiểm tra số điện thoại Việt Nam
        const re = /^(0|\+84)(\d{9,10})$/;
        return re.test(phone);
    }
    
    function showError(fieldId, message) {
        const field = document.getElementById(fieldId);
        let errorDiv = field.nextElementSibling;
        
        if (!errorDiv || !errorDiv.classList.contains('error-message')) {
            errorDiv = document.createElement('div');
            errorDiv.className = 'error-message';
            errorDiv.style.color = 'red';
            errorDiv.style.fontSize = '14px';
            errorDiv.style.marginTop = '5px';
            field.parentNode.insertBefore(errorDiv, field.nextSibling);
        }
        
        errorDiv.textContent = message;
        field.style.borderColor = 'red';
    }
    
    function removeError(fieldId) {
        const field = document.getElementById(fieldId);
        const errorDiv = field.nextElementSibling;
        
        if (errorDiv && errorDiv.classList.contains('error-message')) {
            errorDiv.remove();
        }
        
        field.style.borderColor = '';
    }
});

function goBack() {
    window.location.href = 'dashboard.php'; // Điều chỉnh theo đường dẫn thích hợp
}