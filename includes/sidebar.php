<?php

$categories = db_query_all("SELECT * FROM categories ORDER BY id");
$best_selling_products = db_query_all("SELECT * FROM products WHERE is_featured = 1 ORDER BY id DESC LIMIT 5");

?>
<div class="sidebar">
    <h3><i class="fas fa-list"></i> Danh Mục Món Ăn</h3>
    <ul class="category-list category-list--sidebar">
        <li><a href="products.php"><i class="fas fa-utensils"></i> Tất cả món</a></li>
        <?php foreach ($categories as $category): ?>
            <li>
                <a href="products.php?category_id=<?= $category['id'] ?>" 
                   class="<?= isset($_GET['category_id']) && $_GET['category_id'] == $category['id'] ? 'active' : '' ?>">
                    <i class="fas fa-leaf"></i>
                    <?= htmlspecialchars($category['name']) ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>

    <!-- Price Filter -->
    <div class="price-filter">
        <h4><i class="fas fa-filter"></i> Lọc theo giá</h4>
        <form method="get">
            <div class="price-range">
                <input type="number" placeholder="Từ (VNĐ)" name="min_price" value="<?= isset($_GET['min_price']) ? (int)$_GET['min_price'] : '' ?>">
                <input type="number" placeholder="Đến (VNĐ)" name="max_price" value="<?= isset($_GET['max_price']) ? (int)$_GET['max_price'] : '' ?>">
            </div>
            <button class="btn-filter" type="submit">
                <i class="fas fa-search"></i> Lọc
            </button>
        </form>
    </div>

    <!-- Best Selling Products -->
    <?php if (!empty($best_selling_products)): ?>
    <div style="margin-top: 30px; padding-top: 25px; border-top: 1px solid #eee;">
        <h4 style="color: var(--primary-color); margin-bottom: 15px; display: flex; align-items: center; gap: 8px;">
            <i class="fas fa-fire" style="color: var(--accent-color);"></i> 
            Món Bán Chạy
        </h4>
        <?php foreach ($best_selling_products as $product): ?>
            <div style="display: flex; gap: 10px; margin-bottom: 15px; padding: 10px; background: var(--bg-light); border-radius: 8px;">
            <?php
              $image_src = '';
              if (!empty($product['image'])) {
                $image_src = htmlspecialchars($product['image']);
              } else {
                $image_src = 'https://placehold.co/300x200/cccccc/666666?text=No+Image';
              }
            ?>    
            <img src="<?= $image_src ?>" 
                 alt="<?= htmlspecialchars($product['name']) ?>"
                 style="width: 60px; height: 60px; object-fit: cover; border-radius: 6px;">
                <div style="flex: 1;">
                    <h5 style="margin: 0 0 5px 0; font-size: 0.9rem; color: var(--text-dark);">
                        <?= htmlspecialchars($product['name']) ?>
                    </h5>
                    <div style="color: var(--accent-color); font-weight: bold; font-size: 0.85rem;">
                        <?= number_format($product['price'], 0, ',', '.') ?> VNĐ
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>