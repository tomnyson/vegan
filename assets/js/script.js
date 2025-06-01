// Product click handler (for demonstration)
document.querySelectorAll('.product-card').forEach(card => {
  card.addEventListener('click', function(e) {
    // Don't navigate if clicking on the add to cart button
    if (e.target.classList.contains('btn-add-cart') || e.target.parentElement.classList.contains('btn-add-cart')) {
      e.stopPropagation();
      return;
    }
    // Get product ID from the detail link
    const detailLink = this.querySelector('.btn-detail');
    if (detailLink) {
      window.location.href = detailLink.href;
    }
  });
});

// Add to cart functionality
function addToCart(productId, productName, productPrice) {
  // Show loading state
  const button = event.target;
  const originalText = button.innerHTML;
  button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
  button.disabled = true;
  
  // Send AJAX request to add to cart
  fetch('add_to_cart.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded',
    },
    body: `product_id=${productId}&quantity=1`
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      // Show success message
      showNotification(`${productName} đã được thêm vào giỏ hàng!`, 'success');
      // Update cart count if element exists
      updateCartCount();
    } else {
      showNotification(data.message || 'Có lỗi xảy ra khi thêm vào giỏ hàng', 'error');
    }
  })
  .catch(error => {
    console.error('Error:', error);
    showNotification('Có lỗi xảy ra khi kết nối với server', 'error');
  })
  .finally(() => {
    // Restore button state
    button.innerHTML = originalText;
    button.disabled = false;
  });
}

// Price filter functionality
function filterByPrice() {
  const minPrice = document.getElementById('min-price').value;
  const maxPrice = document.getElementById('max-price').value;
  
  // Build URL with price filters
  const url = new URL(window.location);
  
  if (minPrice) {
    url.searchParams.set('min_price', minPrice);
  } else {
    url.searchParams.delete('min_price');
  }
  
  if (maxPrice) {
    url.searchParams.set('max_price', maxPrice);
  } else {
    url.searchParams.delete('max_price');
  }
  
  // Redirect to filtered page
  window.location.href = url.toString();
}

// Search functionality
function performSearch() {
  const searchInput = document.querySelector('.search-box input');
  const searchTerm = searchInput.value.trim();
  
  if (searchTerm) {
    const url = new URL(window.location);
    url.searchParams.set('search', searchTerm);
    window.location.href = url.toString();
  }
}

// Add search event listeners
document.addEventListener('DOMContentLoaded', function() {
  const searchInput = document.querySelector('.search-box input');
  const searchIcon = document.querySelector('.search-box i');
  
  if (searchInput) {
    // Search on Enter key
    searchInput.addEventListener('keypress', function(e) {
      if (e.key === 'Enter') {
        performSearch();
      }
    });
  }
  
  if (searchIcon) {
    // Search on icon click
    searchIcon.addEventListener('click', performSearch);
  }
});

// Cart count functionality
function updateCartCount() {
  fetch('get_cart_count.php')
    .then(response => response.json())
    .then(data => {
      const cartCountDesktop = document.getElementById('cart-count');
      const cartCountMobile = document.getElementById('cart-count-mobile');
      
      if (cartCountDesktop) {
        cartCountDesktop.textContent = data.count;
        cartCountDesktop.setAttribute('data-count', data.count);
        if (data.count > 0) {
          cartCountDesktop.style.display = 'inline-flex';
        } else {
          cartCountDesktop.style.display = 'none';
        }
      }
      
      if (cartCountMobile) {
        cartCountMobile.textContent = data.count;
        cartCountMobile.setAttribute('data-count', data.count);
        if (data.count > 0) {
          cartCountMobile.style.display = 'inline-flex';
        } else {
          cartCountMobile.style.display = 'none';
        }
      }
    })
    .catch(error => {
      console.error('Error updating cart count:', error);
    });
}

// Load cart count when page loads
document.addEventListener('DOMContentLoaded', function() {
  updateCartCount();
});

// Show notification
function showNotification(message, type = 'info') {
  // Remove existing notifications
  const existingNotifications = document.querySelectorAll('.notification');
  existingNotifications.forEach(notification => notification.remove());
  
  // Create notification element
  const notification = document.createElement('div');
  notification.className = `notification notification-${type}`;
  notification.innerHTML = `
    <div class="notification-content">
      <i class="fas ${type === 'success' ? 'fa-check-circle' : type === 'error' ? 'fa-exclamation-circle' : 'fa-info-circle'}"></i>
      <span>${message}</span>
      <button class="notification-close" onclick="this.parentElement.parentElement.remove()">
        <i class="fas fa-times"></i>
      </button>
    </div>
  `;
  
  // Add notification to page
  document.body.appendChild(notification);
  
  // Auto remove after 5 seconds
  setTimeout(() => {
    if (notification.parentElement) {
      notification.remove();
    }
  }, 5000);
}

// Registration form handling
document.addEventListener('DOMContentLoaded', function() {
    const registerForm = document.getElementById('registerForm');
    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('confirm-password');
    
    if (registerForm) {
        // Real-time password validation
        if (passwordInput) {
            passwordInput.addEventListener('input', validatePassword);
        }
        
        if (confirmPasswordInput) {
            confirmPasswordInput.addEventListener('input', validateConfirmPassword);
        }
        
        // Form submission
        registerForm.addEventListener('submit', handleRegistration);
    }
});

function validatePassword() {
    const password = document.getElementById('password').value;
    const requirements = {
        'length-req': password.length >= 8,
        'uppercase-req': /[A-Z]/.test(password),
        'number-req': /[0-9]/.test(password),
        'special-req': /[!@#$%^&*(),.?":{}|<>]/.test(password)
    };
    
    Object.keys(requirements).forEach(reqId => {
        const element = document.getElementById(reqId);
        if (element) {
            if (requirements[reqId]) {
                element.style.color = '#4CAF50';
                element.innerHTML = '✓ ' + element.textContent.replace('✓ ', '').replace('✗ ', '');
            } else {
                element.style.color = '#f44336';
                element.innerHTML = '✗ ' + element.textContent.replace('✓ ', '').replace('✗ ', '');
            }
        }
    });
}

function validateConfirmPassword() {
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirm-password').value;
    const errorElement = document.getElementById('confirm-password-error');
    
    if (confirmPassword && confirmPassword !== password) {
        errorElement.textContent = 'Mật khẩu xác nhận không khớp';
        errorElement.style.display = 'block';
    } else {
        errorElement.textContent = '';
        errorElement.style.display = 'none';
    }
}

function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

function validatePhone(phone) {
    const re = /^[0-9]{10,11}$/;
    return re.test(phone);
}

function clearErrors() {
    const errorElements = document.querySelectorAll('.error-message');
    errorElements.forEach(element => {
        element.textContent = '';
        element.style.display = 'none';
    });
}

function showError(fieldId, message) {
    const errorElement = document.getElementById(fieldId + '-error');
    if (errorElement) {
        errorElement.textContent = message;
        errorElement.style.display = 'block';
    }
}

function showMessage(message, isSuccess = false) {
    const messageContainer = document.getElementById('message-container');
    if (messageContainer) {
        messageContainer.innerHTML = `
            <div class="alert ${isSuccess ? 'alert-success' : 'alert-error'}">
                <i class="fas ${isSuccess ? 'fa-check-circle' : 'fa-exclamation-circle'}"></i>
                ${message}
            </div>
        `;
        
        // Auto hide after 5 seconds
        setTimeout(() => {
            messageContainer.innerHTML = '';
        }, 5000);
    }
}

function handleRegistration(e) {
    e.preventDefault();
    
    // Clear previous errors
    clearErrors();
    
    // Get form data
    const formData = new FormData(e.target);
    const data = {
        first_name: formData.get('first_name').trim(),
        last_name: formData.get('last_name').trim(),
        email: formData.get('email').trim(),
        phone: formData.get('phone').trim(),
        password: formData.get('password'),
        confirm_password: formData.get('confirm_password')
    };
    
    // Client-side validation
    let hasErrors = false;
    
    if (!data.first_name) {
        showError('first-name', 'Họ không được để trống');
        hasErrors = true;
    }
    
    if (!data.last_name) {
        showError('last-name', 'Tên không được để trống');
        hasErrors = true;
    }
    
    if (!data.email) {
        showError('email', 'Email không được để trống');
        hasErrors = true;
    } else if (!validateEmail(data.email)) {
        showError('email', 'Email không hợp lệ');
        hasErrors = true;
    }
    
    if (!data.phone) {
        showError('phone', 'Số điện thoại không được để trống');
        hasErrors = true;
    } else if (!validatePhone(data.phone)) {
        showError('phone', 'Số điện thoại phải có 10-11 chữ số');
        hasErrors = true;
    }
    
    if (!data.password) {
        showError('password', 'Mật khẩu không được để trống');
        hasErrors = true;
    } else if (data.password.length < 8) {
        showError('password', 'Mật khẩu phải có ít nhất 8 ký tự');
        hasErrors = true;
    }
    
    if (data.password !== data.confirm_password) {
        showError('confirm-password', 'Mật khẩu xác nhận không khớp');
        hasErrors = true;
    }
    
    if (hasErrors) {
        return;
    }
    
    // Show loading state
    const submitBtn = document.getElementById('submitBtn');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang xử lý...';
    submitBtn.disabled = true;
    
    // Send data to server
    fetch('process_register.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showMessage(data.message, true);
            // Reset form
            e.target.reset();
            // Redirect to login page after 2 seconds
            setTimeout(() => {
                window.location.href = 'login.php';
            }, 2000);
        } else {
            if (data.errors && Array.isArray(data.errors)) {
                data.errors.forEach(error => {
                    showMessage(error, false);
                });
            } else {
                showMessage(data.message || 'Có lỗi xảy ra', false);
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showMessage('Có lỗi xảy ra khi kết nối với server', false);
    })
    .finally(() => {
        // Restore button state
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
}

// Mobile menu functionality - SIMPLIFIED
function toggleMobileMenu() {
    const mobileMenu = document.querySelector('.mobile-menu');
    const mobileOverlay = document.querySelector('.mobile-overlay');
    const body = document.body;
    
    if (mobileMenu && mobileOverlay) {
        mobileMenu.classList.toggle('active');
        mobileOverlay.classList.toggle('active');
        body.classList.toggle('mobile-menu-open');
    }
}

// Initialize mobile menu when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Close mobile menu when clicking overlay
    const overlay = document.querySelector('.mobile-overlay');
    if (overlay) {
        overlay.addEventListener('click', function() {
            toggleMobileMenu();
        });
    }
    
    // Close mobile menu when pressing escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && document.querySelector('.mobile-menu').classList.contains('active')) {
            toggleMobileMenu();
        }
    });
    
    // Close mobile menu when clicking on menu links
    const mobileMenuLinks = document.querySelectorAll('.mobile-menu a');
    mobileMenuLinks.forEach(link => {
        link.addEventListener('click', function() {
            if (document.querySelector('.mobile-menu').classList.contains('active')) {
                toggleMobileMenu();
            }
        });
    });

    // ...existing search functionality code...
});