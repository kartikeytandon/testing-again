/**
 * CRM Dashboard JavaScript
 * Handles dashboard functionality and API integration
 */

import apiService from '../services/api.js';

class CRMDashboard {
    constructor() {
        this.init();
    }

    async init() {
        try {
            await this.loadDashboardData();
            this.setupEventListeners();
            this.initializeCharts();
        } catch (error) {
            console.error('Error initializing CRM Dashboard:', error);
            this.showError('Failed to load dashboard data');
        }
    }

    setupEventListeners() {
        // Follow-up checkbox handler
        document.getElementById('followUpRequired').addEventListener('change', function () {
            const followUpDateDiv = document.getElementById('followUpDateDiv');
            if (this.checked) {
                followUpDateDiv.style.display = 'block';
            } else {
                followUpDateDiv.style.display = 'none';
            }
        });

        // Set today's date for task date and visit date
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('taskDate').value = today;
        document.getElementById('visitDate').value = today;
    }

    async loadDashboardData() {
        try {
            // Load dashboard stats
            const statsResponse = await apiService.getDashboardStats();
            if (statsResponse.success) {
                this.updateStatsCards(statsResponse.data);
            }

            // Load recent orders
            await this.loadRecentOrders();

            // Load recent tasks
            await this.loadRecentTasks();

            // Load clients for dropdowns
            await this.loadClients();

        } catch (error) {
            console.error('Error loading dashboard data:', error);
            this.showError('Failed to load dashboard data');
        }
    }

    updateStatsCards(stats) {
        document.getElementById('total-orders').textContent = stats.total_orders || 0;
        document.getElementById('completed-tasks').textContent = stats.completed_tasks || 0;
        document.getElementById('active-users').textContent = stats.active_users || 0;
        document.getElementById('sales-value').textContent = this.formatCurrency(stats.total_sales_value || 0);
    }

    async loadRecentOrders() {
        try {
            const response = await apiService.getOrders({ per_page: 5 });
            if (response.success) {
                this.renderRecentOrders(response.data.data);
            }
        } catch (error) {
            console.error('Error loading recent orders:', error);
            document.getElementById('recent-orders-table').innerHTML =
                '<tr><td colspan="5" class="text-center text-muted">Failed to load orders</td></tr>';
        }
    }

    renderRecentOrders(orders) {
        const tbody = document.getElementById('recent-orders-table');

        if (orders.length === 0) {
            tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted">No orders found</td></tr>';
            return;
        }

        tbody.innerHTML = orders.map(order => `
            <tr>
                <td>#${order.id}</td>
                <td>${order.client ? order.client.name : 'N/A'}</td>
                <td><span class="badge badge-soft-${this.getStatusColor(order.status)}">${this.formatStatus(order.status)}</span></td>
                <td>${this.formatDate(order.created_at)}</td>
                <td>
                    <button class="btn btn-sm btn-soft-primary" onclick="viewOrder(${order.id})">View</button>
                </td>
            </tr>
        `).join('');
    }

    async loadRecentTasks() {
        try {
            const response = await apiService.getTasks({ per_page: 5 });
            if (response.success) {
                this.renderRecentTasks(response.data.data);
            }
        } catch (error) {
            console.error('Error loading recent tasks:', error);
            document.getElementById('recent-tasks-list').innerHTML =
                '<div class="text-center text-muted">Failed to load tasks</div>';
        }
    }

    renderRecentTasks(tasks) {
        const container = document.getElementById('recent-tasks-list');

        if (tasks.length === 0) {
            container.innerHTML = '<div class="text-center text-muted">No tasks found</div>';
            return;
        }

        container.innerHTML = tasks.map(task => `
            <div class="d-flex align-items-center mb-3">
                <div class="flex-shrink-0">
                    <div class="avatar-xs">
                        <div class="avatar-title rounded-circle bg-${this.getPriorityColor(task.priority)} bg-opacity-10 text-${this.getPriorityColor(task.priority)}">
                            <iconify-icon icon="iconamoon:check-circle-duotone"></iconify-icon>
                        </div>
                    </div>
                </div>
                <div class="flex-grow-1 ms-3">
                    <h6 class="mb-1">${task.task_title}</h6>
                    <p class="text-muted mb-0 fs-13">${this.formatDate(task.task_date)}</p>
                </div>
                <div class="flex-shrink-0">
                    <span class="badge badge-soft-${this.getStatusColor(task.status)}">${this.formatStatus(task.status)}</span>
                </div>
            </div>
        `).join('');
    }

    async loadClients() {
        try {
            const response = await apiService.searchClients('');
            if (response.success) {
                const clients = response.data.filter(user => user.role === 'client');
                this.populateClientDropdowns(clients);
            }
        } catch (error) {
            console.error('Error loading clients:', error);
        }
    }

    populateClientDropdowns(clients) {
        const orderClientSelect = document.getElementById('orderClient');
        const salesClientSelect = document.getElementById('salesClient');

        const options = clients.map(client =>
            `<option value="${client.id}">${client.name}</option>`
        ).join('');

        orderClientSelect.innerHTML = '<option value="">Select Client</option>' + options;
        salesClientSelect.innerHTML = '<option value="">Select Client</option>' + options;
    }

    initializeCharts() {
        // Initialize sales performance chart
        this.initSalesChart();
    }

    initSalesChart() {
        // This would integrate with ApexCharts
        // For now, we'll create a placeholder
        const chartContainer = document.getElementById('sales-chart');
        chartContainer.innerHTML = '<div class="text-center py-4"><p class="text-muted">Sales chart will be loaded here</p></div>';
    }

    // Utility methods
    formatCurrency(amount) {
        return new Intl.NumberFormat('en-US', {
            style: 'currency',
            currency: 'USD'
        }).format(amount);
    }

    formatDate(dateString) {
        return new Date(dateString).toLocaleDateString();
    }

    formatStatus(status) {
        return status.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase());
    }

    getStatusColor(status) {
        const colors = {
            'pending': 'warning',
            'completed': 'success',
            'done': 'success',
            'ordered_with_supplier': 'info',
            'billing_dispatch': 'primary',
            'delivered': 'success',
            'confirmed': 'success',
            'rejected': 'danger',
            'uploaded': 'info',
            'present': 'success',
            'absent': 'danger',
            'late': 'warning',
            'half_day': 'info',
            'leave': 'secondary'
        };
        return colors[status] || 'secondary';
    }

    getPriorityColor(priority) {
        const colors = {
            'low': 'secondary',
            'medium': 'info',
            'high': 'warning',
            'urgent': 'danger'
        };
        return colors[priority] || 'secondary';
    }

    showError(message) {
        // You can integrate with a toast notification system here
        console.error(message);
        alert(message); // Temporary solution
    }

    showSuccess(message) {
        // You can integrate with a toast notification system here
        console.log(message);
        alert(message); // Temporary solution
    }
}

// Global functions for modal interactions
window.showCreateOrderModal = function () {
    const modal = new bootstrap.Modal(document.getElementById('createOrderModal'));
    modal.show();
};

window.showCreateTaskModal = function () {
    const modal = new bootstrap.Modal(document.getElementById('createTaskModal'));
    modal.show();
};

window.showCreateSalesReportModal = function () {
    const modal = new bootstrap.Modal(document.getElementById('createSalesReportModal'));
    modal.show();
};

window.createOrder = async function () {
    try {
        const orderData = {
            client_id: document.getElementById('orderClient').value,
            order_details: document.getElementById('orderDetails').value,
            remark: document.getElementById('orderRemark').value
        };

        const response = await apiService.createOrder(orderData);
        if (response.success) {
            bootstrap.Modal.getInstance(document.getElementById('createOrderModal')).hide();
            document.getElementById('createOrderForm').reset();
            window.crmDashboard.loadRecentOrders();
            window.crmDashboard.showSuccess('Order created successfully');
        }
    } catch (error) {
        window.crmDashboard.showError('Failed to create order: ' + error.message);
    }
};

window.createTask = async function () {
    try {
        const taskData = {
            task_title: document.getElementById('taskTitle').value,
            task_description: document.getElementById('taskDescription').value,
            task_date: document.getElementById('taskDate').value,
            priority: document.getElementById('taskPriority').value,
            due_date: document.getElementById('taskDueDate').value || null
        };

        const response = await apiService.createTask(taskData);
        if (response.success) {
            bootstrap.Modal.getInstance(document.getElementById('createTaskModal')).hide();
            document.getElementById('createTaskForm').reset();
            window.crmDashboard.loadRecentTasks();
            window.crmDashboard.showSuccess('Task created successfully');
        }
    } catch (error) {
        window.crmDashboard.showError('Failed to create task: ' + error.message);
    }
};

window.createSalesReport = async function () {
    try {
        const reportData = {
            client_id: document.getElementById('salesClient').value,
            visit_date: document.getElementById('visitDate').value,
            order_value: document.getElementById('orderValue').value || null,
            visit_purpose: document.getElementById('visitPurpose').value,
            client_feedback: document.getElementById('clientFeedback').value,
            follow_up_required: document.getElementById('followUpRequired').checked,
            follow_up_date: document.getElementById('followUpDate').value || null
        };

        const response = await apiService.createSalesReport(reportData);
        if (response.success) {
            bootstrap.Modal.getInstance(document.getElementById('createSalesReportModal')).hide();
            document.getElementById('createSalesReportForm').reset();
            window.crmDashboard.showSuccess('Sales report created successfully');
        }
    } catch (error) {
        window.crmDashboard.showError('Failed to create sales report: ' + error.message);
    }
};

window.checkIn = async function () {
    try {
        const response = await apiService.checkIn();
        if (response.success) {
            window.crmDashboard.showSuccess('Checked in successfully');
        }
    } catch (error) {
        window.crmDashboard.showError('Failed to check in: ' + error.message);
    }
};

window.viewOrder = function (orderId) {
    // Navigate to order details page
    window.location.href = `/orders/${orderId}`;
};

window.loadOrders = function () {
    window.location.href = '/orders';
};

window.loadTasks = function () {
    window.location.href = '/tasks';
};

// Initialize dashboard when DOM is loaded
document.addEventListener('DOMContentLoaded', function () {
    window.crmDashboard = new CRMDashboard();
});
