<?php
require_once 'helpers/database.php';
?>
<!DOCTYPE html>
<html lang="vi">

<head>
  <meta charset="UTF-8">
  <title>Thực đơn - Sản phẩm</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
  <link rel="stylesheet" href="./assets/css/style.css">
  <style>
    .pagination {
      display: flex;
      justify-content: center;
      align-items: center;
      margin: 40px 0 20px 0;
      gap: 6px;
      flex-wrap: wrap;
    }

    .pagination a,
    .pagination span {
      padding: 8px 14px;
      margin: 0 2px;
      background: #fff;
      border: 1px solid #e0e0e0;
      border-radius: 6px;
      color: #557c3e;
      text-decoration: none;
      font-weight: 500;
      transition: all 0.2s;
      font-size: 1rem;
      min-width: 38px;
      text-align: center;
      cursor: pointer;
    }


    .pagination a.nav-btn {
      background: #f9fafb;
      color: #557c3e;
      border: 1px solid #d4af37;
      font-weight: bold;
    }
    .pagination a.nav-btn:hover {
      background: #e0e0e0;
      color: #333;
    }

    /* .pagination a.nav-btn.disabled {
      color: #aaa !important;
      border: 1px solid #eee;
      background: #fafafa;
      pointer-events: none;
    } */

    .pagination span {
      background: transparent;
      border: none;
      color: #888;
      padding: 8px 0;
      font-size: 1rem;
      pointer-events: none;
    }

    @media (max-width: 600px) {
      .pagination {
        gap: 2px;
        margin: 25px 0 15px 0;
      }

      .pagination a,
      .pagination span {
        padding: 6px 8px;
        font-size: 0.95rem;
        min-width: 28px;
      }
    }

    .no-product-found {
      text-align: center;
      color: #e74c3c;
      font-size: 1.2rem;
      padding: 40px 0;
      background: #fff;
      font-weight: 600;
      border-radius: 8px;
      margin: 30px 0;
    }
  </style>

</head>

<body>
  <?php include 'includes/header.php'; ?>
  <?php
  // Pagination logic
  $limit = 12;
  $search = trim($_GET['q'] ?? '');
  $category_id = isset($_GET['category_id']) ? intval($_GET['category_id']) : 0;
  $min_price = isset($_GET['min_price']) ? floatval($_GET['min_price']) : null;
  $max_price = isset($_GET['max_price']) ? floatval($_GET['max_price']) : null;

  // Build WHERE clause and parameters
  $whereArr = [];
  $params = [];

  if ($search !== '') {
    $whereArr[] = "name LIKE ?";
    $params[] = "%$search%";
  }
  if ($category_id > 0) {
    $whereArr[] = "category_id = ?";
    $params[] = $category_id;
  }
  if ($min_price !== null && $min_price !== '' && $min_price >= 0) {
    $whereArr[] = "price >= ?";
    $params[] = $min_price;
  }
  if ($max_price !== null && $max_price !== '' && $max_price > 0) {
    $whereArr[] = "price <= ?";
    $params[] = $max_price;
  }

  $where = '';
  if ($whereArr) {
    $where = 'WHERE ' . implode(' AND ', $whereArr);
  }

  // Count total products for pagination
  $total_products = db_query_value("SELECT COUNT(*) FROM products $where", $params);
  $total_pages = max(1, ceil($total_products / $limit));
  $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
  if ($page > $total_pages) $page = $total_pages;
  $offset = ($page - 1) * $limit;

  // Get products for current page
  $products = db_query_all("SELECT * FROM products $where ORDER BY id DESC LIMIT $limit OFFSET $offset", $params);
  ?>

  <section class="search-section">
    <form class="search-box" method="get" action="">
      <input
        type="text"
        name="q"
        value="<?= htmlspecialchars($_GET['q'] ?? '') ?>"
        placeholder="Tìm kiếm món ăn...">
      <button type="submit"><i class="fas fa-search"></i></button>
    </form>
  </section>

  <div class="main-container">
    <!-- Sidebar bên trái -->
    <?php include 'includes/sidebar.php'; ?>
    <!-- Khu vực nội dung chính -->
    <div class="main-content">
      <?php if (empty($products)): ?>
        <div class="no-product-found">Không tìm thấy sản phẩm phù hợp.</div>
      <?php else:  ?>
        <div class="product-grid">

          <?php foreach ($products as $product): ?>
            <div class="product-card">
              <?php
              $image_src = '';
              if (!empty($product['image']) && file_exists($product['image'])) {
                $image_src = htmlspecialchars($product['image']);
              } else {
                $image_src = 'https://placehold.co/300x200/cccccc/666666?text=No+Image';
              }
              ?>
              <img src="<?= $image_src ?>" alt="<?= htmlspecialchars($product['name']) ?>">
              <div class="product-title"><?= htmlspecialchars($product['name']) ?></div>
              <div class="product-price"><?= number_format($product['price'], 0, ',', '.') ?> VNĐ</div>
              <a href="product-detail.php?product_id=<?= $product['id'] ?>" class="btn-detail">Xem chi tiết</a>
              <button class="btn-add-cart" onclick="addToCart(<?= $product['id'] ?>, '<?= htmlspecialchars($product['name']) ?>', <?= $product['price'] ?>)">
                <i class="fas fa-shopping-cart"></i> Thêm vào giỏ
              </button>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>


      <?php
      $queryParams = $_GET;
      unset($queryParams['page']);
      $qs = http_build_query($queryParams);
      $qs = $qs ? $qs . '&' : '';
      $prev_page = $page > 1 ? $page - 1 : 1;
      $next_page = $page < $total_pages ? $page + 1 : $total_pages;
      $range = 2; // How many page links on each side
      $start = max(1, $page - $range);
      $end = min($total_pages, $page + $range);
      ?>

      <?php if ($total_pages > 1): ?>
        <div class="pagination">
          <a href="?<?= $qs ?>page=<?= $prev_page ?>" class="nav-btn<?= $page == 1 ? ' disabled' : '' ?>">
            <i class="fas fa-chevron-left"></i> Trước
          </a>
          <?php if ($start > 1): ?>
            <a href="?<?= $qs ?>page=1">1</a>
            <?php if ($start > 2): ?><span>...</span><?php endif; ?>
          <?php endif; ?>

          <?php for ($i = $start; $i <= $end; $i++): ?>
            <a href="?<?= $qs ?>page=<?= $i ?>" class="<?= $i == $page ? 'active' : '' ?>"><?= $i ?></a>
          <?php endfor; ?>

          <?php if ($end < $total_pages): ?>
            <?php if ($end < $total_pages - 1): ?><span>...</span><?php endif; ?>
            <a href="?<?= $qs ?>page=<?= $total_pages ?>"><?= $total_pages ?></a>
          <?php endif; ?>

          <a href="?<?= $qs ?>page=<?= $next_page ?>" class="nav-btn<?= $page == $total_pages ? ' disabled' : '' ?>">
            Tiếp <i class="fas fa-chevron-right"></i>
          </a>
        </div>
      <?php endif; ?>
    </div>
  </div>
  <?php include 'includes/footer.php'; ?>

</body>

</html>