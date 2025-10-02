@extends('layouts.vertical', ['title' => 'CRM Dashboard', 'subTitle' => 'Customer Relationship Management'])

@section('content')
    <div class="row">
        <!-- Stats Cards -->
        <div class="col-xxl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <div class="avatar-md bg-primary bg-opacity-10 rounded">
                                <iconify-icon icon="iconamoon:shopping-cart-duotone" class="avatar-title text-primary fs-32"></iconify-icon>
                            </div>
                        </div>
                        <div class="col-6 text-end">
                            <p class="text-muted mb-0 text-truncate">Total Orders</p>
                            <h3 class="text-dark mt-1 mb-0" id="total-orders">0</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xxl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <div class="avatar-md bg-success bg-opacity-10 rounded">
                                <iconify-icon icon="iconamoon:check-circle-duotone" class="avatar-title text-success fs-32"></iconify-icon>
                            </div>
                        </div>
                        <div class="col-6 text-end">
                            <p class="text-muted mb-0 text-truncate">Completed Tasks</p>
                            <h3 class="text-dark mt-1 mb-0" id="completed-tasks">0</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xxl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <div class="avatar-md bg-warning bg-opacity-10 rounded">
                                <iconify-icon icon="iconamoon:profile-circle-duotone" class="avatar-title text-warning fs-32"></iconify-icon>
                            </div>
                        </div>
                        <div class="col-6 text-end">
                            <p class="text-muted mb-0 text-truncate">Active Users</p>
                            <h3 class="text-dark mt-1 mb-0" id="active-users">0</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xxl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <div class="avatar-md bg-info bg-opacity-10 rounded">
                                <iconify-icon icon="iconamoon:chart-line-duotone" class="avatar-title text-info fs-32"></iconify-icon>
                            </div>
                        </div>
                        <div class="col-6 text-end">
                            <p class="text-muted mb-0 text-truncate">Sales Value</p>
                            <h3 class="text-dark mt-1 mb-0" id="sales-value">$0</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Recent Orders -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h4 class="card-title">Recent Orders</h4>
                    <a href="#" class="btn btn-sm btn-soft-primary" onclick="loadOrders()">View All</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-nowrap table-centered m-0">
                            <thead class="bg-light bg-opacity-50">
                                <tr>
                                    <th class="text-muted py-1">Order ID</th>
                                    <th class="text-muted py-1">Client</th>
                                    <th class="text-muted py-1">Status</th>
                                    <th class="text-muted py-1">Date</th>
                                    <th class="text-muted py-1">Action</th>
                                </tr>
                            </thead>
                            <tbody id="recent-orders-table">
                                <tr>
                                    <td colspan="5" class="text-center py-4">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Tasks -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h4 class="card-title">Recent Tasks</h4>
                    <a href="#" class="btn btn-sm btn-soft-primary" onclick="loadTasks()">View All</a>
                </div>
                <div class="card-body">
                    <div id="recent-tasks-list">
                        <div class="text-center py-4">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Sales Chart -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Sales Performance</h4>
                </div>
                <div class="card-body">
                    <div id="sales-chart" class="apex-charts"></div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Quick Actions</h4>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button class="btn btn-primary" onclick="showCreateOrderModal()">
                            <iconify-icon icon="iconamoon:plus-duotone" class="me-1"></iconify-icon>
                            Create Order
                        </button>
                        <button class="btn btn-success" onclick="showCreateTaskModal()">
                            <iconify-icon icon="iconamoon:check-circle-duotone" class="me-1"></iconify-icon>
                            Create Task
                        </button>
                        <button class="btn btn-info" onclick="showCreateSalesReportModal()">
                            <iconify-icon icon="iconamoon:chart-line-duotone" class="me-1"></iconify-icon>
                            Sales Report
                        </button>
                        <button class="btn btn-warning" onclick="checkIn()">
                            <iconify-icon icon="iconamoon:time-duotone" class="me-1"></iconify-icon>
                            Check In
                        </button>
                        <button class="btn btn-dark" id="create-users-btn" style="display:none;">
                            <iconify-icon icon="iconamoon:user-add-duotone" class="me-1"></iconify-icon>
                            Create Users
                        </button>
                        <button class="btn btn-dark" id="admin-panel-btn" style="display:none;">
                            <iconify-icon icon="iconamoon:shield-check-duotone" class="me-1"></iconify-icon>
                            Admin Panel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Order Modal -->
    <div class="modal fade" id="createOrderModal" tabindex="-1" aria-labelledby="createOrderModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createOrderModalLabel">Create New Order</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="createOrderForm">
                        <div class="mb-3">
                            <label for="orderClient" class="form-label">Client</label>
                            <select class="form-select" id="orderClient" required>
                                <option value="">Select Client</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="orderDetails" class="form-label">Order Details</label>
                            <textarea class="form-control" id="orderDetails" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="orderRemark" class="form-label">Remark</label>
                            <textarea class="form-control" id="orderRemark" rows="2"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="createOrder()">Create Order</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Task Modal -->
    <div class="modal fade" id="createTaskModal" tabindex="-1" aria-labelledby="createTaskModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createTaskModalLabel">Create New Task</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="createTaskForm">
                        <div class="mb-3">
                            <label for="taskTitle" class="form-label">Task Title</label>
                            <input type="text" class="form-control" id="taskTitle" required>
                        </div>
                        <div class="mb-3">
                            <label for="taskDescription" class="form-label">Description</label>
                            <textarea class="form-control" id="taskDescription" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="taskDate" class="form-label">Task Date</label>
                            <input type="date" class="form-control" id="taskDate" required>
                        </div>
                        <div class="mb-3">
                            <label for="taskPriority" class="form-label">Priority</label>
                            <select class="form-select" id="taskPriority" required>
                                <option value="low">Low</option>
                                <option value="medium" selected>Medium</option>
                                <option value="high">High</option>
                                <option value="urgent">Urgent</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="taskDueDate" class="form-label">Due Date</label>
                            <input type="date" class="form-control" id="taskDueDate">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="createTask()">Create Task</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Sales Report Modal -->
    <div class="modal fade" id="createSalesReportModal" tabindex="-1" aria-labelledby="createSalesReportModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createSalesReportModalLabel">Create Sales Report</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="createSalesReportForm">
                        <div class="mb-3">
                            <label for="salesClient" class="form-label">Client</label>
                            <select class="form-select" id="salesClient" required>
                                <option value="">Select Client</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="visitDate" class="form-label">Visit Date</label>
                            <input type="date" class="form-control" id="visitDate" required>
                        </div>
                        <div class="mb-3">
                            <label for="orderValue" class="form-label">Order Value</label>
                            <input type="number" class="form-control" id="orderValue" step="0.01" min="0">
                        </div>
                        <div class="mb-3">
                            <label for="visitPurpose" class="form-label">Visit Purpose</label>
                            <input type="text" class="form-control" id="visitPurpose">
                        </div>
                        <div class="mb-3">
                            <label for="clientFeedback" class="form-label">Client Feedback</label>
                            <textarea class="form-control" id="clientFeedback" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="followUpRequired">
                                <label class="form-check-label" for="followUpRequired">
                                    Follow-up Required
                                </label>
                            </div>
                        </div>
                        <div class="mb-3" id="followUpDateDiv" style="display: none;">
                            <label for="followUpDate" class="form-label">Follow-up Date</label>
                            <input type="date" class="form-control" id="followUpDate">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="createSalesReport()">Create Report</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="{{ asset('build/assets/app-DhVc4B2T.js') }}"></script>
    <script>
        // Inline CRM Dashboard JavaScript
        document.addEventListener('DOMContentLoaded', function() {
            // Basic dashboard functionality
            const userJson = localStorage.getItem('user_data');
            const role = (() => { try { return (userJson ? JSON.parse(userJson) : null)?.role || ''; } catch(_) { return ''; } })();
            if (String(role).toLowerCase() === 'admin') {
                // Redirect admins to admin dashboard
                window.location.replace('/admin');
                return;
            }
            loadDashboardData();
            setupAdminButtons();
            honorNavigateTo();
            
            async function loadDashboardData() {
                try {
                    const token = localStorage.getItem('auth_token');
                    if (!token) {
                        window.location.href = '/crm-login';
                        return;
                    }
                    
                    // Load dashboard stats
                    const statsResponse = await fetch('/api/dashboard/stats', {
                        headers: {
                            'Authorization': `Bearer ${token}`,
                            'Accept': 'application/json'
                        }
                    });
                    
                    if (statsResponse.ok) {
                        const stats = await statsResponse.json();
                        if (stats.success) {
                            updateStatsCards(stats.data);
                        }
                    }
                    
                    // Load recent orders
                    loadRecentOrders();
                    
                } catch (error) {
                    console.error('Error loading dashboard:', error);
                }
            }
            
            function updateStatsCards(stats) {
                document.getElementById('total-orders').textContent = stats.total_orders || 0;
                document.getElementById('completed-tasks').textContent = stats.completed_tasks || 0;
                document.getElementById('active-users').textContent = stats.active_users || 0;
                document.getElementById('sales-value').textContent = formatCurrency(stats.total_sales_value || 0);
            }
            
            async function loadRecentOrders() {
                try {
                    const token = localStorage.getItem('auth_token');
                    const response = await fetch('/api/orders?per_page=5', {
                        headers: {
                            'Authorization': `Bearer ${token}`,
                            'Accept': 'application/json'
                        }
                    });
                    
                    if (response.ok) {
                        const data = await response.json();
                        if (data.success) {
                            renderRecentOrders(data.data.data);
                        }
                    }
                } catch (error) {
                    console.error('Error loading orders:', error);
                }
            }
            
            function renderRecentOrders(orders) {
                const tbody = document.getElementById('recent-orders-table');
                
                if (orders.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted">No orders found</td></tr>';
                    return;
                }
                
                tbody.innerHTML = orders.map(order => `
                    <tr>
                        <td>#${order.id}</td>
                        <td>${order.client ? order.client.name : 'N/A'}</td>
                        <td><span class="badge badge-soft-${getStatusColor(order.status)}">${formatStatus(order.status)}</span></td>
                        <td>${formatDate(order.created_at)}</td>
                        <td>
                            <button class="btn btn-sm btn-soft-primary" onclick="viewOrder(${order.id})">View</button>
                        </td>
                    </tr>
                `).join('');
            }
            
            function formatCurrency(amount) {
                return new Intl.NumberFormat('en-US', {
                    style: 'currency',
                    currency: 'USD'
                }).format(amount);
            }
            
            function formatDate(dateString) {
                return new Date(dateString).toLocaleDateString();
            }
            
            function formatStatus(status) {
                return status.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase());
            }
            
            function getStatusColor(status) {
                const colors = {
                    'pending': 'warning',
                    'completed': 'success',
                    'done': 'success',
                    'ordered_with_supplier': 'info',
                    'billing_dispatch': 'primary',
                    'delivered': 'success'
                };
                return colors[status] || 'secondary';
            }
            
            // Global functions
            window.showCreateOrderModal = function() {
                const modal = new bootstrap.Modal(document.getElementById('createOrderModal'));
                modal.show();
            };
            
            window.showCreateTaskModal = function() {
                const modal = new bootstrap.Modal(document.getElementById('createTaskModal'));
                modal.show();
            };
            
            window.showCreateSalesReportModal = function() {
                const modal = new bootstrap.Modal(document.getElementById('createSalesReportModal'));
                modal.show();
            };
            
            window.checkIn = async function() {
                try {
                    const token = localStorage.getItem('auth_token');
                    const response = await fetch('/api/attendance/check-in', {
                        method: 'POST',
                        headers: {
                            'Authorization': `Bearer ${token}`,
                            'Accept': 'application/json'
                        }
                    });
                    
                    if (response.ok) {
                        alert('Checked in successfully');
                    } else {
                        alert('Failed to check in');
                    }
                } catch (error) {
                    alert('Failed to check in');
                }
            };
            
            window.viewOrder = function(orderId) {
                alert('View order: ' + orderId);
            };
            
            window.loadOrders = function() {
                alert('Load orders page');
            };
            
            window.loadTasks = function() {
                alert('Load tasks page');
            };
            
            function setupAdminButtons() {
                try {
                    const userJson = localStorage.getItem('user_data');
                    const user = userJson ? JSON.parse(userJson) : null;
                    const adminBtn = document.getElementById('admin-panel-btn');
                    const createUsersBtn = document.getElementById('create-users-btn');
                    const isAdmin = user && String(user.role || '').toLowerCase() === 'admin';
                    if (adminBtn && isAdmin) {
                        adminBtn.style.display = '';
                        adminBtn.addEventListener('click', function() {
                            window.location.href = '/admin';
                        });
                    }
                    if (createUsersBtn && isAdmin) {
                        createUsersBtn.style.display = '';
                        createUsersBtn.addEventListener('click', function() {
                            window.location.href = '/admin';
                        });
                    }
                } catch (_) {}
            }

            function honorNavigateTo() {
                try {
                    const nav = localStorage.getItem('navigate_to');
                    if (!nav) return;
                    localStorage.removeItem('navigate_to');
                    switch (nav) {
                        case 'attendance':
                            checkIn();
                            break;
                        case 'sales':
                            showCreateSalesReportModal();
                            break;
                        case 'orders':
                            showCreateOrderModal();
                            break;
                        case 'tasks':
                            showCreateTaskModal();
                            break;
                    }
                } catch (_) {}
            }
        });
    </script>
@endsection
