<!-- header_admin.php -->
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="./assets/css/admin.css">
    <link rel="stylesheet" href="./assets/css/import.css">
    <link rel="stylesheet" href="./assets/css/dashboard_content.css">
    <link rel="stylesheet" href="../member/assets/css/member.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <header class="admin-header">
        <div class="logo">Admin Panel</div>
        <div class="user-info">
            <span>Xin chào, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
            <a href="../scripts/logout.php">Đăng xuất</a>
        </div>
    </header>