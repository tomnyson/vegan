
// Global chart instance and state management
let salesChart = null;
let isChartInitialized = false;
let currentChartType = null;
let chartLoadingTimeout = null;
let isChartRendering = false;
let lastRenderedLabels = null;
let lastRenderedData = null;

// Edit functions for modals
function editCategory(id, name) {
    document.getElementById('editCategoryId').value = id;
    document.getElementById('editCategoryName').value = name;
    new bootstrap.Modal(document.getElementById('editCategoryModal')).show();
}

function editOptionGroup(id, name, type, is_required, applies_to) {
    document.getElementById('editGroupId').value = id;
    document.getElementById('editGroupName').value = name;
    document.getElementById('editGroupType').value = type;
    document.getElementById('editIsRequired').value = is_required;
    document.getElementById('editAppliesTo').value = applies_to;
    new bootstrap.Modal(document.getElementById('editOptionGroupModal')).show();
}

function editOptionItem(id, group_id, label, price) {
    document.getElementById('editItemId').value = id;
    document.getElementById('editItemGroupId').value = group_id;
    document.getElementById('editItemLabel').value = label;
    document.getElementById('editItemPrice').value = price;
    new bootstrap.Modal(document.getElementById('editOptionItemModal')).show();
}

function editProduct(product, selectedGroupIds) {
    document.getElementById('editProductId').value = product.id;
    document.getElementById('editProductName').value = product.name;
    document.getElementById('editProductPrice').value = product.price;
    document.getElementById('editProductCategory').value = product.category_id;
    document.getElementById('editProductSlug').value = product.slug;
    document.getElementById('editProductImage').value = product.image;
    document.getElementById('editProductDesc').value = product.description;
    document.getElementById('editProductIngredients').value = product.ingredients;
    document.getElementById('editIsFeatured').checked = product.is_featured;

    // Clear and set option group checkboxes
    document.querySelectorAll('input[name="selected_option_groups[]"]').forEach(checkbox => {
        checkbox.checked = selectedGroupIds.includes(parseInt(checkbox.value));
    });

    new bootstrap.Modal(document.getElementById('editProductModal')).show();
}

function manageProductOptions(productId, selectedGroupIds, productName) {
    document.getElementById('manageProductId').value = productId;
    document.getElementById('productNameInModal').textContent = productName;

    // Clear and set option group checkboxes for manage modal only
    document.querySelectorAll('input[name="manage_selected_option_groups[]"]').forEach(checkbox => {
        checkbox.checked = selectedGroupIds.includes(parseInt(checkbox.value));
    });

    new bootstrap.Modal(document.getElementById('manageProductOptionsModal')).show();
}

// Auto-generate slug from product name
function setupSlugGenerator(nameInputId, slugInputId) {
    const nameInput = document.getElementById(nameInputId);
    const slugInput = document.getElementById(slugInputId);
    if (nameInput && slugInput) {
        nameInput.addEventListener('input', function() {
            const slug = this.value.toLowerCase()
                .replace(/[^\w\s-]/g, '')
                .replace(/\s+/g, '-')
                .replace(/^-+|-+$/g, '');
            slugInput.value = slug;
        });
    }
}

setupSlugGenerator('productName', 'productSlug');
setupSlugGenerator('editProductName', 'editProductSlug');

// Form validation
document.querySelectorAll('form').forEach(form => {
    form.addEventListener('submit', function(e) {
        let valid = true;

        // Validate required inputs
        this.querySelectorAll('input[required], select[required]').forEach(input => {
            if (!input.value.trim()) {
                valid = false;
                input.classList.add('is-invalid');
            } else {
                input.classList.remove('is-invalid');
            }
        });

        // Validate option groups for product forms
        if (this.id === 'addProductForm' || this.id === 'editProductModal') {
            const checkboxes = this.querySelectorAll('input[name="selected_option_groups[]"]:checked');
            if (checkboxes.length === 0) {
                valid = false;
                alert('Vui lòng chọn ít nhất một nhóm tùy chọn.');
            }
        }

        // Validate option groups for manage options form
        if (this.id === 'manageProductOptionsForm') {
            const checkboxes = this.querySelectorAll('input[name="manage_selected_option_groups[]"]:checked');
            if (checkboxes.length === 0) {
                valid = false;
                alert('Vui lòng chọn ít nhất một nhóm tùy chọn.');
            }
        }

        if (!valid) {
            e.preventDefault();
        }
    });
});

// Confirm option group updates
const manageForm = document.getElementById('manageProductOptionsForm');
if (manageForm) {
    manageForm.addEventListener('submit', function(e) {
        if (!confirm('Xác nhận cập nhật nhóm tùy chọn cho sản phẩm này?')) {
            e.preventDefault();
        }
    });
}

// Chart functionality - fixed and optimized
function renderSalesChart(data) {
    // Prevent rendering if already in progress
    if (isChartRendering) {
        console.log('Chart rendering already in progress, skipping...');
        return;
    }

    const ctx = document.getElementById('salesHistoryChart');
    if (!ctx) {
        console.error('Chart canvas not found');
        return;
    }

    const labels = data.map(item => item.label);
    const salesValues = data.map(item => item.total_sales || 0);
    const ordersValues = data.map(item => item.orders || 0);

    // Check if data has actually changed
    const labelsString = JSON.stringify(labels);
    const salesString = JSON.stringify(salesValues);

    if (lastRenderedLabels === labelsString && lastRenderedData === salesString) {
        console.log('Chart data unchanged, skipping render');
        return;
    }

    // Set rendering flag
    isChartRendering = true;

    try {
        // Destroy existing chart if it exists
        if (salesChart && typeof salesChart.destroy === 'function') {
            salesChart.destroy();
            salesChart = null;
        }

        // Create new chart
        salesChart = new Chart(ctx.getContext('2d'), {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Doanh thu (VNĐ)',
                    data: salesValues,
                    borderColor: '#557c3e',
                    backgroundColor: 'rgba(85, 124, 62, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 5,
                    pointHoverRadius: 7,
                    pointBackgroundColor: '#557c3e',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2
                }, {
                    label: 'Số đơn hàng',
                    data: ordersValues,
                    borderColor: '#d4af37',
                    backgroundColor: 'rgba(212, 175, 55, 0.1)',
                    borderWidth: 2,
                    fill: false,
                    tension: 0.4,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    pointBackgroundColor: '#d4af37',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    yAxisID: 'y1'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                plugins: {
                    legend: {
                        position: 'top'
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.datasetIndex === 0) {
                                    label += new Intl.NumberFormat('vi-VN').format(context.parsed.y) + 'đ';
                                } else {
                                    label += context.parsed.y + ' đơn';
                                }
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        display: true,
                        title: {
                            display: true,
                            text: 'Thời gian'
                        }
                    },
                    y: {
                        display: true,
                        position: 'left',
                        title: {
                            display: true,
                            text: 'Doanh thu (VNĐ)'
                        },
                        ticks: {
                            callback: function(value) {
                                return new Intl.NumberFormat('vi-VN').format(value) + 'đ';
                            }
                        }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        title: {
                            display: true,
                            text: 'Số đơn hàng'
                        },
                        grid: {
                            drawOnChartArea: false,
                        },
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });

        // Update cache
        lastRenderedLabels = labelsString;
        lastRenderedData = salesString;
        isChartInitialized = true;

        console.log('Chart rendered successfully');
    } catch (error) {
        console.error('Error rendering chart:', error);
    } finally {
        // Always reset rendering flag
        isChartRendering = false;
    }
}

function updateButtonStates(activeType) {
    const buttons = ['btn-day', 'btn-week', 'btn-month', 'btn-year', 'btn-all'];
    buttons.forEach(btnId => {
        const btn = document.getElementById(btnId);
        if (btn) {
            btn.classList.remove('btn-primary');
            btn.classList.add('btn-outline-primary');
        }
    });

    const activeBtn = document.getElementById(`btn-${activeType}`);
    if (activeBtn) {
        activeBtn.classList.remove('btn-outline-primary');
        activeBtn.classList.add('btn-primary');
    }
}

function fetchStats(type) {
    // Prevent duplicate requests
    if (currentChartType === type && isChartInitialized) {
        console.log('Stats already loaded for type:', type);
        return;
    }

    // Clear previous timeout
    if (chartLoadingTimeout) {
        clearTimeout(chartLoadingTimeout);
        chartLoadingTimeout = null;
    }

    currentChartType = type;
    updateButtonStates(type);

    const loadingElement = document.getElementById('chart-loading');
    if (loadingElement) {
        loadingElement.classList.remove('d-none');
    }

    chartLoadingTimeout = setTimeout(() => {
        fetch(`/vegan/admin/sales_stats.php?type=${type}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (Array.isArray(data)) {
                    renderSalesChart(data);
                } else {
                    console.error('Invalid data format received:', data);
                }
            })
            .catch(error => {
                console.error('Error fetching sales stats:', error);
                // Reset chart state on error
                currentChartType = null;
                isChartInitialized = false;
            })
            .finally(() => {
                if (loadingElement) {
                    loadingElement.classList.add('d-none');
                }
                chartLoadingTimeout = null;
            });
    }, 300); // Reduced timeout for better UX
}

// Initialize chart with default data on page load
document.addEventListener('DOMContentLoaded', function() {
    // Load default chart data (week view)
    setTimeout(() => fetchStats('week'), 100);
});
document.addEventListener('DOMContentLoaded', function() {
    // Đảm bảo đã có dữ liệu orderStatusStats
    if (typeof orderStatusStats === "undefined") {
        console.error('orderStatusStats is undefined');
        return;
    }
    const statusMap = {
        'pending': {
            label: 'Chờ xử lý',
            color: '#f6c23e'
        },
        'processing': {
            label: 'Đang xử lý',
            color: '#4e73df'
        },
        'completed': {
            label: 'Hoàn thành',
            color: '#1cc88a'
        },
        'cancelled': {
            label: 'Đã hủy',
            color: '#e74a3b'
        }
    };

    const stats = orderStatusStats;
    const labels = stats.map(row => statusMap[row.status] ? statusMap[row.status].label : row.status);
    const data = stats.map(row => row.count);
    const backgroundColors = stats.map(row => statusMap[row.status] ? statusMap[row.status].color : '#999');

    // Xoá chart cũ nếu tồn tại
    if (window.orderStatusChartInstance) {
        window.orderStatusChartInstance.destroy();
    }

    // Render chart
    const ctx = document.getElementById('orderStatusChart');
    if (ctx) {
        window.orderStatusChartInstance = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    backgroundColor: backgroundColors,
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return `${context.label}: ${context.parsed} đơn`;
                            }
                        }
                    }
                }
            }
        });
    } else {
        console.error('Canvas with id "orderStatusChart" not found.');
    }
});

// Ensure the correct tab is active based on the URL hash
document.addEventListener('DOMContentLoaded', function() {
    var hash = window.location.hash;
    if (hash) {
        var tabTrigger = document.querySelector('.nav-link[data-bs-target="' + hash + '"]');
        if (tabTrigger) {
            var tab = new bootstrap.Tab(tabTrigger);
            tab.show();
        }
    }
    // Update the hash when a tab is shown
    var tabLinks = document.querySelectorAll('.nav-link[data-bs-toggle="tab"]');
    tabLinks.forEach(function(tabLink) {
        tabLink.addEventListener('shown.bs.tab', function(event) {
            var target = tabLink.getAttribute('data-bs-target');
            if (target) {
                history.replaceState(null, null, target);
            }
        });
    });
});
// Enhanced Order Status Update Functions
function toggleStatusUpdate(orderId) {
    const badge = document.getElementById(`status-badge-${orderId}`);
    const dropdown = document.getElementById(`status-dropdown-${orderId}`);
    
    if (badge && dropdown) {
        badge.classList.toggle('d-none');
        dropdown.classList.toggle('d-none');
        
        // Focus on select element when showing dropdown
        if (!dropdown.classList.contains('d-none')) {
            const select = dropdown.querySelector('select');
            if (select) select.focus();
        }
    }
}

function updateOrderStatus(selectElement, orderId, currentStatus) {
    console.log(`Updating order #${orderId} status from "${currentStatus}" to "${selectElement.value}"`);
    const newStatus = selectElement.value;
    console.log(`New status selected: ${newStatus}`);
    // Validate that we have a proper status value
    if (!newStatus || newStatus === '' || newStatus === currentStatus) {
        toggleStatusUpdate(orderId);
        return;
    }

    // Show confirmation dialog
    if (!confirm(`Xác nhận thay đổi trạng thái đơn hàng #${orderId} thành "${selectElement.options[selectElement.selectedIndex].text}"?`)) {
        // Reset select value and hide dropdown
        selectElement.value = currentStatus;
        toggleStatusUpdate(orderId);
        return;
    }

    // Disable the select to prevent multiple submissions
    selectElement.disabled = true;
    
    // Submit form immediately without adding loading option
    const form = selectElement.closest('form');
    if (form) {
        // Ensure the form has all required fields
        const orderStatusInput = document.createElement('input');
        orderStatusInput.type = 'hidden';
        orderStatusInput.name = 'order_status';
        orderStatusInput.value = newStatus;
        form.appendChild(orderStatusInput);
        console.log('Form data:', new FormData(form));
        form.submit();
    } else {
        console.error('Form not found for order', orderId);
        selectElement.disabled = false;
                          }
}

function quickCancelOrder(orderId) {
    if (confirm(`Xác nhận hủy đơn hàng #${orderId}?`)) {
        // Create and submit a form to cancel the order
        const form = document.createElement('form');
        form.method = 'POST';
        form.style.display
        
        const orderIdInput = document.createElement('input');
        orderIdInput.type = 'hidden';
        orderIdInput.name = 'order_id';
        orderIdInput.value = orderId;
        
        const statusInput = document.createElement('input');
        statusInput.type = 'hidden';
        statusInput.name = 'order_status';
        statusInput.value = 'cancelled';
        
        const actionInput = document.createElement('input');
        actionInput.type = 'hidden';
        actionInput.name = 'update_order_status';
        actionInput.value = '1';

        const actionStatusInput = document.createElement('input');
        actionStatusInput.type = 'hidden';
        actionStatusInput.name = 'action';
        actionStatusInput.value = 'update_status';
        
        const tabInput = document.createElement('input');
        tabInput.type = 'hidden';
        tabInput.name = 'current_tab';
        tabInput.value = 'orders';
        
        form.appendChild(orderIdInput);
        form.appendChild(statusInput);
        form.appendChild(actionInput);
        form.appendChild(tabInput);
        
        document.body.appendChild(form);
        form.submit();
    }
}

function filterOrdersByStatus(status) {
    const table = document.getElementById('ordersTable');
    const rows = table.querySelectorAll('tbody tr');
    
    rows.forEach(row => {
        const orderStatus = row.getAttribute('data-order-status');
        if (status === '' || orderStatus === status) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

function refreshOrders() {
    // Simple page reload to refresh orders
    const currentTab = 'orders';
    const url = new URL(window.location.href);
    url.hash = currentTab;
    window.location.href = url.toString();
}

function viewOrderDetails(orderId) {
    // Show the modal
    const modal = new bootstrap.Modal(document.getElementById('orderDetailModal'));
    modal.show();
    
    // Set order ID in modal title
    document.getElementById('orderDetailId').textContent = orderId;
    
    // Show loading state
    document.getElementById('orderDetailLoading').style.display = 'block';
    document.getElementById('orderDetailContent').style.display = 'none';
    document.getElementById('orderDetailError').style.display = 'none';
    
    // Fetch order details
    fetch(`get_order_details.php?id=${orderId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayOrderDetails(data.order, data.items);
            } else {
                showOrderDetailError(data.message || 'Không thể tải chi tiết đơn hàng');
            }
        })
        .catch(error => {
            console.error('Error fetching order details:', error);
            showOrderDetailError('Có lỗi xảy ra khi tải dữ liệu');
        })
        .finally(() => {
            document.getElementById('orderDetailLoading').style.display = 'none';
        });
}

function displayOrderDetails(order, items) {
    // Display customer information
    document.getElementById('customerName').textContent = order.customer_name || 'Không có';
    document.getElementById('customerEmail').textContent = order.email || order.customer_email || 'Không có';
    document.getElementById('customerPhone').textContent = order.customer_phone || 'Không có';
    document.getElementById('deliveryAddress').textContent = order.delivery_address || 'Không có';
    
    // Display order information
    document.getElementById('orderNumber').textContent = order.order_number || 'Không có';
    document.getElementById('orderDate').textContent = formatDate(order.created_at);
    document.getElementById('orderStatus').innerHTML = `<span class="badge bg-${getStatusColor(order.status)}">${getStatusLabel(order.status)}</span>`;
    document.getElementById('paymentMethod').textContent = formatPaymentMethod(order.payment_method);
    document.getElementById('orderNotes').textContent = order.notes || 'Không có';
    
    // Display order items
    const itemsTableBody = document.getElementById('orderItemsTable');
    itemsTableBody.innerHTML = '';
    
    items.forEach(item => {
        const row = document.createElement('tr');
        
        // Parse options for display
        let optionsHtml = 'Không có';
        if (item.parsed_options && item.parsed_options.selectedOptions) {
            optionsHtml = formatItemOptions(item.parsed_options);
        }
        
        row.innerHTML = `
            <td>
                <div class="fw-bold">${escapeHtml(item.product_name)}</div>
            </td>
            <td>
                ${item.image ? 
                    `<img src="${escapeHtml(item.image)}" alt="Product" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">` : 
                    '<img src="https://placehold.co/50x50/cccccc/666666?text=No+Image" alt="No image" style="width: 50px; height: 50px; border-radius: 4px;">'
                }
            </td>
            <td>
                <div class="small">${optionsHtml}</div>
            </td>
            <td class="text-center">${item.quantity}</td>
            <td class="text-end">${formatCurrency(item.product_price || 0)}</td>
            <td class="text-end fw-bold">${formatCurrency(item.item_total_calculated || 0)}</td>
        `;
        
        itemsTableBody.appendChild(row);
    });
    
    // Display order summary
    document.getElementById('orderSubtotal').textContent = formatCurrency(order.subtotal || 0);
    document.getElementById('orderShippingFee').textContent = formatCurrency(order.shipping_fee || 0);
    document.getElementById('orderTotalAmount').textContent = formatCurrency(order.total_amount || 0);
    
    // Show content
    document.getElementById('orderDetailContent').style.display = 'block';
}

function showOrderDetailError(message) {
    document.getElementById('orderDetailErrorMessage').textContent = message;
    document.getElementById('orderDetailError').style.display = 'block';
}

function formatItemOptions(options) {
    let html = '';
    
    if (options.selectedOptions) {
        Object.keys(options.selectedOptions).forEach(groupId => {
            const groupData = options.selectedOptions[groupId];
            
            if (groupData.type === 'single' && groupData.label) {
                // Single choice option
                let groupName = 'Tùy chọn';
                if (groupId == '1') groupName = 'Kích thước';
                else if (groupId == '2') groupName = 'Hương vị';
                
                html += `<div class="mb-1"><strong>${groupName}:</strong> ${escapeHtml(groupData.label)}`;
                if (groupData.price && groupData.price > 0) {
                    html += ` (+${formatCurrency(groupData.price)})`;
                }
                html += '</div>';
            } else if (groupData.type === 'multiple' && groupData.items) {
                // Multiple choice options
                html += '<div class="mb-1"><strong>Topping:</strong> ';
                const toppingLabels = groupData.items.map(item => {
                    let label = escapeHtml(item.label);
                    if (item.price && item.price > 0) {
                        label += ` (+${formatCurrency(item.price)})`;
                    }
                    return label;
                });
                html += toppingLabels.join(', ') + '</div>';
            }
        });
    }
    
    // Display note if exists
    if (options.note && options.note.trim()) {
        html += `<div class="mb-1"><strong>Ghi chú:</strong> ${escapeHtml(options.note)}</div>`;
    }
    
    return html || 'Không có';
}

function formatDate(dateString) {
    if (!dateString) return 'Không có';
    const date = new Date(dateString);
    return date.toLocaleString('vi-VN', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

function formatPaymentMethod(method) {
    const methods = {
        'cod': 'Thanh toán khi nhận hàng',
        'bank_transfer': 'Chuyển khoản ngân hàng',
        'credit_card': 'Thẻ tín dụng',
        'momo': 'Ví MoMo',
        'zalopay': 'ZaloPay'
    };
    return methods[method] || method || 'Không có';
}

function formatCurrency(amount) {
    return new Intl.NumberFormat('vi-VN').format(amount) + 'đ';
}

function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function getStatusColor(status) {
    const colors = {
        'pending': 'warning',
        'confirmed': 'info',
        'processing': 'primary',
        'shipped': 'secondary',
        'delivered': 'success',
        'completed': 'success',
        'cancelled': 'danger',
        'refunded': 'dark'
    };
    return colors[status] || 'secondary';
}

function getStatusLabel(status) {
    const labels = {
        'pending': 'Chờ xác nhận',
        'confirmed': 'Đã xác nhận',
        'processing': 'Đang xử lý',
        'shipped': 'Đã gửi hàng',
        'delivered': 'Đã giao',
        'completed': 'Hoàn thành',
        'cancelled': 'Đã hủy',
        'refunded': 'Đã hoàn tiền'
    };
    return labels[status] || status;
}

function printOrderDetail() {
    // Create a printable version of the order details
    const orderContent = document.getElementById('orderDetailContent').cloneNode(true);
    const printWindow = window.open('', '_blank');
    
    printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>Chi tiết đơn hàng #${document.getElementById('orderDetailId').textContent}</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; }
                .card { border: 1px solid #ddd; margin-bottom: 20px; }
                .card-header { background: #f5f5f5; padding: 10px; font-weight: bold; }
                .card-body { padding: 15px; }
                table { width: 100%; border-collapse: collapse; margin-top: 10px; }
                th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                th { background-color: #f5f5f5; }
                .text-end { text-align: right; }
                .fw-bold { font-weight: bold; }
                .badge { padding: 4px 8px; border-radius: 4px; }
                .bg-warning { background-color: #ffc107; }
                .bg-success { background-color: #198754; color: white; }
                .bg-danger { background-color: #dc3545; color: white; }
                .bg-primary { background-color: #0d6efd; color: white; }
                .bg-info { background-color: #0dcaf0; }
                .bg-secondary { background-color: #6c757d; color: white; }
                img { max-width: 50px; max-height: 50px; }
            </style>
        </head>
        <body>
            <h1>Chi tiết đơn hàng #${document.getElementById('orderDetailId').textContent}</h1>
            ${orderContent.innerHTML}
            <script>window.print(); window.close();</script>
        </body>
        </html>
    `);
    
    printWindow.document.close();
}
