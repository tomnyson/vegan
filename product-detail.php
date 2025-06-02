<?php
require_once 'helpers/database.php';

$product_id = isset($_GET['product_id']) ? (int)$_GET['product_id'] : 1;
$pdo = db_connect();

// Lấy thông tin sản phẩm
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

// If product doesn't exist, redirect or show error
if (!$product) {
  echo "<script>alert('Sản phẩm không tồn tại!'); window.location.href='products.php';</script>";
  exit;
}

// Try to get option groups, but handle gracefully if tables don't exist or are empty
$option_groups = [];
$option_items = [];

try {
  // Lấy các nhóm tùy chọn của sản phẩm - chỉ những nhóm được đánh dấu hiển thị
  $stmt = $pdo->prepare("
  SELECT og.id, og.name, og.type, og.is_required, og.applies_to FROM option_groups og JOIN product_options po ON po.option_group_id = og.id WHERE po.product_id = ? ORDER BY og.id;
");
  $stmt->execute([$product_id]);
  $option_groups = $stmt->fetchAll(PDO::FETCH_ASSOC);

  // Lấy tất cả các tùy chọn con theo nhóm
  foreach ($option_groups as $group) {
    $stmt = $pdo->prepare("SELECT * FROM option_items WHERE group_id = ? ORDER BY price ASC, label ASC");
    $stmt->execute([$group['id']]);
    $option_items[$group['id']] = $stmt->fetchAll(PDO::FETCH_ASSOC);
  }
} catch (PDOException $e) {
  // If tables don't exist, create default options for display
  $option_groups = [];
  $option_items = [];
}

// Get related products (exclude current product)
$stmt = $pdo->prepare("SELECT * FROM products WHERE id != ? LIMIT 4");
$stmt->execute([$product_id]);
$related_products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="vi">

<head>
  <meta charset="UTF-8">
  <title><?php echo htmlspecialchars($product['name']); ?> - Nhà Hàng Chay</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
  <link rel="stylesheet" href="./assets/css/style.css">
</head>

<body>
  <?php include 'includes/header.php'; ?>

  <div class="detail-container">
    <div class="detail-img">
      <?php
      // Check if image exists and is not empty
      $image_src = '';
      if (!empty($product['image'])) {
        $image_src = htmlspecialchars($product['image']);
      } else {
        $image_src = 'https://placehold.co/500x400/cccccc/666666?text=No+Image';
      }
      ?>
      <img src="<?= $image_src ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
    </div>
    <div class="detail-info">
      <h2><?php echo htmlspecialchars($product['name']); ?></h2>
      <div class="price"><?php echo number_format($product['price'], 0, ',', '.'); ?> VNĐ</div>
      <table class="info-table">
        <tr>
          <td class="label">Mô tả:</td>
          <td><?php echo htmlspecialchars($product['description']); ?></td>
        </tr>
        <tr>
          <td class="label">Nguyên liệu:</td>
          <td><?php echo !empty($product['ingredients']) ? htmlspecialchars($product['ingredients']) : ''; ?></td>
        </tr>
        <tr>
          <td class="label">Hương vị:</td>
          <td><?php echo !empty($product['flavor_profile']) ? htmlspecialchars($product['flavor_profile']) : ''; ?></td>
        </tr>
        <tr>
          <td class="label">Kích thước:</td>
          <td><?php echo !empty($product['size_options']) ? htmlspecialchars($product['size_options']) : ''; ?></td>
        </tr>
        <tr>
          <td class="label">Tùy chọn:</td>
          <td><?php echo !empty($product['addon_options']) ? htmlspecialchars($product['addon_options']) : ''; ?></td>
        </tr>
      </table>

      <form>
        <?php foreach ($option_groups as $group): ?>
          <fieldset class="options-section">
            <legend class="options-heading">
              <i class="fas fa-cogs"></i>
              <?php echo htmlspecialchars($group['name']); ?>
              <?php
              // Optionally, show description based on group name
              if (stripos($group['name'], 'kích') !== false) echo ' <span class="group-desc">(Chọn kích thước)</span>';
              elseif (stripos($group['name'], 'hương') !== false) echo ' <span class="group-desc">(Chọn hương vị)</span>';
              elseif (stripos($group['name'], 'phụ') !== false || stripos($group['name'], 'topping') !== false) echo ' <span class="group-desc">(Chọn món phụ / topping)</span>';
              ?>
            </legend>
            <?php if ($group['type'] === 'single'): ?>
              <select class="select-styled" name="option_<?php echo $group['id']; ?>">
                <?php foreach ($option_items[$group['id']] as $item): ?>
                  <option value="<?php echo $item['id']; ?>">
                    <?php echo htmlspecialchars($item['label']); ?>
                    <?php if ($item['price'] > 0) echo ' (+' . number_format($item['price'], 0, ',', '.') . 'đ)'; ?>
                  </option>
                <?php endforeach; ?>
              </select>
            <?php else: ?>
              <div class="topping-grid">
                <?php foreach ($option_items[$group['id']] as $item): ?>
                  <label class="topping-item">
                    <input type="checkbox" name="option_<?php echo $group['id']; ?>[]" value="<?php echo $item['id']; ?>">
                    <div class="topping-card">
                      <?php
                      $image_src = '';
                      if (!empty($item['image']) && file_exists($item['image'])) {
                        $image_src = htmlspecialchars($item['image']);
                      } else {
                        $image_src = 'https://placehold.co/300x200/cccccc/666666?text=No+Image';
                      }
                      ?>
                      <img src="<?= $image_src ?>" alt="<?php echo htmlspecialchars($item['label']); ?>">
                      <span class="topping-name"><?php echo htmlspecialchars($item['label']); ?></span>
                      <?php if ($item['price'] > 0): ?>
                        <span class="topping-price">+<?php echo number_format($item['price'], 0, ',', '.'); ?>đ</span>
                      <?php endif; ?>
                    </div>
                  </label>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>
          </fieldset>
        <?php endforeach; ?>

        <!-- Ghi chú và nút thêm giỏ hàng -->
        <div class="options-section">
          <label for="note" class="note-label"><i class="fas fa-sticky-note"></i> Ghi chú đặc biệt:</label>
          <textarea id="note" class="special-note" rows="3" placeholder="Nhập ghi chú đặc biệt nếu có..."></textarea>

          <div class="order-actions">
            <div class="quantity-selector">
              <button type="button" class="qty-btn" onclick="decreaseQty()">-</button>
              <input type="text" id="quantity" value="1" readonly>
              <button type="button" class="qty-btn" onclick="increaseQty()">+</button>
            </div>
            <?php if (isset($_SESSION['user_id'])): ?>
              <button type="button" class="btn-add" id="add-to-cart-btn">
                <i class="fas fa-shopping-cart"></i> Thêm vào giỏ hàng
              </button>
            <?php else: ?>
              <button type="button" class="btn-add" onclick="alert('Vui lòng đăng nhập để thêm sản phẩm vào giỏ hàng!');">
                <i class="fas fa-shopping-cart"></i> Thêm vào giỏ hàng
              </button>
            <?php endif; ?>
          </div>
        </div>

        <!-- Hidden input for product ID -->
        <input type="hidden" id="product-id" value="<?php echo $product['id']; ?>">
        <input type="hidden" id="product-name" value="<?php echo htmlspecialchars($product['name']); ?>">
        <input type="hidden" id="product-price" value="<?php echo $product['price']; ?>">
        <input type="hidden" id="size" value="<?php echo htmlspecialchars($product['size_options']); ?>"> <!-- Added hidden input for size -->
      </form>
    </div>
  </div>

  <!-- Gợi ý món đặc sắc khác -->
  <div class="other-dishes">
    <div class="container">
      <h3 class="other-title">Món ăn đặc sắc khác</h3>
      <div class="dish-list">
        <?php foreach ($related_products as $related): ?>
          <div class="dish-item" onclick="location.href='product-detail.php?product_id=<?php echo $related['id']; ?>'">
            <?php
            // Check if related product image exists
            $related_image_src = '';
            if (!empty($related['image']) && file_exists($related['image'])) {
              $related_image_src = htmlspecialchars($related['image']);
            } else {
              $related_image_src = 'https://placehold.co/220x140/cccccc/666666?text=No+Image';
            }
            ?>
            <img src="<?= $related_image_src ?>" alt="<?php echo htmlspecialchars($related['name']); ?>">
            <div class="name"><?php echo htmlspecialchars($related['name']); ?></div>
            <div class="price"><?php echo number_format($related['price'], 0, ',', '.'); ?> VNĐ</div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>

  <?php include 'includes/footer.php'; ?>

  <script>
    function increaseQty() {
      const qty = document.getElementById('quantity');
      qty.value = parseInt(qty.value) + 1;
    }

    function decreaseQty() {
      const qty = document.getElementById('quantity');
      if (parseInt(qty.value) > 1) {
        qty.value = parseInt(qty.value) - 1;
      }
    }

    // Updated add to cart function to use dynamic product data
    function addToCart() {
      const productId = document.getElementById('product-id').value;
      const productName = document.getElementById('product-name').value;
      const productPrice = document.getElementById('product-price').value;
      const quantity = document.getElementById('quantity').value;
      const note = document.getElementById('note').value;

      // Collect all selected options dynamically
      const selectedOptions = {};

      // Get all select options (single choice)
      document.querySelectorAll('select[name^="option_"]').forEach(select => {
        const groupId = select.name.replace('option_', '');
        const selectedOption = select.options[select.selectedIndex];
        if (selectedOption.value) {
          // Extract price from option text (e.g., "Lớn (+25.000đ)")
          const optionText = selectedOption.text;
          const priceMatch = optionText.match(/\(\+([0-9.,]+)đ\)/);
          const price = priceMatch ? parseFloat(priceMatch[1].replace(/[,.]/g, '')) : 0;

          selectedOptions[groupId] = {
            id: selectedOption.value,
            label: selectedOption.text.replace(/\s*\(\+.*?\)/, ''), // Remove price from label
            price: price,
            type: 'single'
          };
        }
      });

      // Get all checkbox options (multiple choice)
      const checkboxGroups = {};
      document.querySelectorAll('input[type="checkbox"][name^="option_"]:checked').forEach(checkbox => {
        const groupId = checkbox.name.replace('option_', '').replace('[]', '');
        if (!checkboxGroups[groupId]) {
          checkboxGroups[groupId] = [];
        }

        const toppingItem = checkbox.closest('.topping-item');
        const label = toppingItem.querySelector('.topping-name').textContent;
        const priceElement = toppingItem.querySelector('.topping-price');

        // Extract price from topping price text (e.g., "+8.000đ")
        let price = 0;
        if (priceElement) {
          const priceText = priceElement.textContent;
          const priceMatch = priceText.match(/\+([0-9.,]+)đ/);
          price = priceMatch ? parseFloat(priceMatch[1].replace(/[,.]/g, '')) : 0;
        }

        checkboxGroups[groupId].push({
          id: checkbox.value,
          label: label,
          price: price
        });
      });

      // Add checkbox groups to selected options
      Object.keys(checkboxGroups).forEach(groupId => {
        selectedOptions[groupId] = {
          items: checkboxGroups[groupId],
          type: 'multiple'
        };
      });

      const orderData = {
        productId: productId,
        productName: productName,
        productPrice: parseFloat(productPrice),
        quantity: parseInt(quantity),
        selectedOptions: selectedOptions,
        note: note
      };

      console.log('Adding to cart:', orderData);

      // Show loading state
      const addButton = document.getElementById('add-to-cart-btn');
      const originalText = addButton.innerHTML;
      addButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang thêm...';
      addButton.disabled = true;

      // Send the data to backend
      fetch('add_to_cart.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify(orderData)
        })
        .then(response => {
          if (!response.ok) {
            throw new Error('Network response was not ok');
          }
          return response.json();
        })
        .then(data => {
          if (data.success) {
            alert('Đã thêm vào giỏ hàng!');
            // Update cart counter in header
            updateCartCounter();
          } else {
            alert('Có lỗi xảy ra: ' + (data.error || data.message || 'Unknown error'));
          }
        })
        .catch(error => {
          console.error('Error:', error);
          alert('Có lỗi xảy ra khi thêm vào giỏ hàng!');
        })
        .finally(() => {
          // Restore button state
          addButton.innerHTML = originalText;
          addButton.disabled = false;
        });
    }

    // Function to update cart counter (optional)
    function updateCartCounter() {
      fetch('get_cart_count.php')
        .then(response => response.json())
        .then(data => {
          const cartCounter = document.querySelector('.cart-counter');
          if (cartCounter && data.count !== undefined) {
            cartCounter.textContent = data.count;
            cartCounter.style.display = data.count > 0 ? 'block' : 'none';
          }
        })
        .catch(error => console.error('Error updating cart counter:', error));
    }

    // Attach event listener when DOM is loaded
    document.addEventListener('DOMContentLoaded', function() {
      const addButton = document.getElementById('add-to-cart-btn');

      if (addButton) {
        addButton.addEventListener('click', function(e) {
          e.preventDefault();
          addToCart();
        });
      }
    });
  </script>

</body>

</html>