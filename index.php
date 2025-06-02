<!DOCTYPE html>
<html lang="vi">
<?php
require_once 'helpers/database.php';
?>
<head>
  <meta charset="UTF-8">
  <title>Nhà Hàng Chay - Trang chủ</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
  <link rel="stylesheet" href="./assets/css/style.css">
</head>

<body>
  <?php include 'includes/header.php'; ?>
  <?php

  
  $categories = db_query_all("SELECT * FROM categories ORDER BY id");
  $featured_products = db_query_all("SELECT * FROM products WHERE is_featured = 1 ORDER BY id DESC LIMIT 4");
  $bestseller_products = db_query_all("
    SELECT
        p.id,
        p.name,
        p.image,
        p.price,
        SUM(oi.quantity) AS total_sold
    FROM
        order_items oi
        INNER JOIN products p ON oi.product_id = p.id
    GROUP BY
        p.id, p.name, p.image, p.price
    ORDER BY
        total_sold DESC
    LIMIT 5
");
  ?>
  <!-- Banner video -->
  <section class="main-banner">
    <iframe
      width="100%"
      height="100%"
      src="https://www.youtube.com/embed/lrMF15XgsyQ?autoplay=1&mute=1&loop=1&playlist=lrMF15XgsyQ"
      title="YouTube video player"
      frameborder="0"
      allow="autoplay; encrypted-media"
      allowfullscreen
      style="position:absolute;top:0;left:0;width:100%;height:100%;z-index:0;pointer-events:none;"></iframe>
    <div class="banner-overlay"></div>
    <div class="banner-content">
      <h1>Chào mừng đến với Ẩm Thực Chay Tinh Khiết</h1>
      <p>Bữa ăn thanh tịnh, tốt cho sức khỏe, trọn vị yêu thương!</p>
      <a href="products.php" class="cta-btn">Khám phá thực đơn <i class="fas fa-arrow-right"></i></a>
    </div>
  </section>

  <!-- Sản phẩm bán chạy -->
  <section class="bestseller-section">
    <div class="section">
      <div class="section-title">Sản phẩm bán chạy</div>
      <div class="product-list">
        <?php foreach ($bestseller_products as $product): ?>
          <div class="product-card bestseller-product">
            <div class="bestseller-badge">HOT</div>
            <?php
            $image_src = '';
            if (!empty($product['image'])) {
              $image_src = htmlspecialchars($product['image']);
            } else {
              $image_src = 'https://placehold.co/300x200/cccccc/666666?text=No+Image';
            }
            ?>
            <img src="<?= $image_src ?>" alt="<?= htmlspecialchars($product['name']) ?>">
            <div class="product-title"><?= htmlspecialchars($product['name']) ?></div>
            <div class="product-price"><?= number_format($product['price'], 0, ',', '.') ?> VNĐ</div>
            <a href="product-detail.php?product_id=<?= $product['id'] ?>" class="btn-detail">Xem chi tiết</a>
            <?php if (isset($_SESSION['user_id'])): ?>
              <button class="btn-add-cart" onclick="window.location.href='product-detail.php?product_id=<?= $product['id'] ?>'"><i class="fas fa-shopping-cart"></i> Thêm vào giỏ</button>
            <?php else: ?>
              <button class="btn-add-cart" onclick="window.location.href='login.php'"><i class="fas fa-shopping-cart"></i> Thêm vào giỏ</button>
            <?php endif; ?>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <!-- Danh mục món ăn -->
  <section class="section">
    <div class="section-title">Danh mục món ăn</div>
    <div class="category-list">
      <?php
      foreach ($categories as $category) {
        echo '<div class="category-card">';
        echo '<img src="' . htmlspecialchars($category['image']) . '" alt="' . htmlspecialchars($category['name']) . '">';
        echo '<div class="category-title">' . htmlspecialchars($category['name']) . '</div>';
        echo '<a href="products.php?category_id=' . $category['id'] . '" class="btn-view-category">Xem tất cả</a>';
        echo '</div>';
      }
      ?>

    </div>
  </section>

  <!-- Món nổi bật -->
  <section class="section">
    <div class="section-title">Món ăn nổi bật</div>
    <div class="product-list">
      <?php
        foreach ($featured_products as $product) {
          $image_src = '';
          if (!empty($product['image'])) {
            $image_src = htmlspecialchars($product['image']);
          } else {
            $image_src = 'https://placehold.co/300x200/cccccc/666666?text=No+Image';
          }
          echo '<div class="product-card">';
          echo '<img src="' . $image_src . '" alt="' . htmlspecialchars($product['name']) . '">';
          echo '<div class="product-title">' . htmlspecialchars($product['name']) . '</div>';
          echo '<div class="product-price">' . number_format($product['price'], 0, ',', '.') . ' VNĐ</div>';
            if (isset($_SESSION['user_id'])) {
            echo '<button class="btn-add-cart"><i class="fas fa-shopping-cart"></i> Thêm vào giỏ</button>';
            } else {
            echo '<button class="btn-add-cart" onclick="window.location.href=\'login.php\'">Thêm vào giỏ</button>';
            }
          echo '</div>';
      }
      ?>
    </div>
  </section>

  <!-- Về nhà hàng -->
  <section class="section about">
    <div class="section-title">Về nhà hàng</div>
    <p>Nhà hàng chay của chúng tôi tự hào phục vụ các món ăn chay thanh tịnh, bổ dưỡng, phù hợp với khẩu vị người Việt. Chúng tôi cam kết mang đến trải nghiệm ẩm thực chay đẳng cấp với nguyên liệu tươi sạch và không gian ấm cúng.</p>

    <div class="about-features">
      <div class="feature-item">
        <i class="fas fa-seedling"></i>
        <h3>Nguyên liệu sạch</h3>
        <p>100% từ rau củ quả sạch, không chất bảo quản</p>
      </div>
      <div class="feature-item">
        <i class="fas fa-heart"></i>
        <h3>Món ăn tâm linh</h3>
        <p>Chế biến với tâm từ bi, lành mạnh cho sức khỏe</p>
      </div>
      <div class="feature-item">
        <i class="fas fa-utensils"></i>
        <h3>Hương vị đặc biệt</h3>
        <p>Hương vị thuần Việt, đậm đà mà vẫn thanh nhẹ</p>
      </div>
    </div>

    <p style="margin-top: 30px;"><strong>Địa chỉ:</strong> 123 Đường Thanh Tịnh, Quận Tây Hồ, Hà Nội</p>
    <p><strong>Giờ mở cửa:</strong> 9:00 - 21:00 hàng ngày</p>
  </section>

  <!-- CTA đặt bàn -->
  <section class="cta-section">
    <div class="section">
      <h2>Đặt bàn hoặc đặt món ngay!</h2>
      <p style="margin-bottom: 10px">Hãy liên hệ với chúng tôi để có những trải nghiệm ẩm thực chay tuyệt vời</p>
      <div class="cta-buttons">
        <a href="tel:0909123456" class="btn">
          <i class="fas fa-phone-alt"></i> Gọi: 0909 123 456
        </a>
        <a href="reservation.html" class="btn btn-outline">
          <i class="fas fa-calendar-alt"></i> Đặt bàn online
        </a>
      </div>
    </div>
  </section>
  <?php include 'includes/footer.php'; ?>
  <script src="./assets/js/script.js"></script>

</body>

</html>