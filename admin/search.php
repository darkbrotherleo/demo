    <!-- Search Section -->
    <div class="search-container">
        <form method="POST" action="./core/process_search.php" class="search-form">
            <input type="text" name="searchCode" placeholder="Nhập mã Code để tìm kiếm" value="<?php echo htmlspecialchars($searchCode); ?>" required>
            <button type="submit">Tìm</button>
        </form>
    </div>