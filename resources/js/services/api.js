/**
 * API Service Layer for CRM Integration
 * Handles all API communications with the Laravel backend
 */

class ApiService {
    constructor() {
        this.baseURL = '/api';
        this.token = localStorage.getItem('auth_token');
        this.setupInterceptors();
    }

    setupInterceptors() {
        // Add token to all requests if available
        if (this.token) {
            this.setAuthToken(this.token);
        }
    }

    setAuthToken(token) {
        this.token = token;
        localStorage.setItem('auth_token', token);
    }

    clearAuthToken() {
        this.token = null;
        localStorage.removeItem('auth_token');
    }

    async request(endpoint, options = {}) {
        const url = `${this.baseURL}${endpoint}`;
        const config = {
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                ...options.headers
            },
            ...options
        };

        if (this.token) {
            config.headers['Authorization'] = `Bearer ${this.token}`;
        }

        try {
            const response = await fetch(url, config);
            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.message || 'API request failed');
            }

            return data;
        } catch (error) {
            console.error('API Error:', error);
            throw error;
        }
    }

    // Authentication Methods
    async login(email, password) {
        const response = await this.request('/auth/login', {
            method: 'POST',
            body: JSON.stringify({ email, password })
        });

        if (response.success && response.data.token) {
            this.setAuthToken(response.data.token);
        }

        return response;
    }

    async logout() {
        try {
            await this.request('/auth/logout', { method: 'POST' });
        } finally {
            this.clearAuthToken();
        }
    }

    async getCurrentUser() {
        return await this.request('/auth/me');
    }

    async changePassword(currentPassword, newPassword) {
        return await this.request('/auth/change-password', {
            method: 'POST',
            body: JSON.stringify({
                current_password: currentPassword,
                new_password: newPassword
            })
        });
    }

    // Dashboard Methods
    async getDashboardStats() {
        return await this.request('/dashboard/stats');
    }

    async getDashboardOverview() {
        return await this.request('/dashboard/overview');
    }

    async getRecentActivity() {
        return await this.request('/dashboard/recent-activity');
    }

    // User Management Methods
    async getUsers(params = {}) {
        const queryString = new URLSearchParams(params).toString();
        return await this.request(`/users${queryString ? `?${queryString}` : ''}`);
    }

    async createUser(userData) {
        return await this.request('/users', {
            method: 'POST',
            body: JSON.stringify(userData)
        });
    }

    async updateUser(id, userData) {
        return await this.request(`/users/${id}`, {
            method: 'PUT',
            body: JSON.stringify(userData)
        });
    }

    async deleteUser(id) {
        return await this.request(`/users/${id}`, { method: 'DELETE' });
    }

    async getUserRoles() {
        return await this.request('/users/roles');
    }

    async updateUserRole(id, role) {
        return await this.request(`/users/${id}/role`, {
            method: 'PUT',
            body: JSON.stringify({ role })
        });
    }

    async updateUserModules(id, modules) {
        return await this.request(`/users/${id}/modules`, {
            method: 'PUT',
            body: JSON.stringify({ modules })
        });
    }

    // Order Management Methods
    async getOrders(params = {}) {
        const queryString = new URLSearchParams(params).toString();
        return await this.request(`/orders${queryString ? `?${queryString}` : ''}`);
    }

    async createOrder(orderData) {
        return await this.request('/orders', {
            method: 'POST',
            body: JSON.stringify(orderData)
        });
    }

    async updateOrder(id, orderData) {
        return await this.request(`/orders/${id}`, {
            method: 'PUT',
            body: JSON.stringify(orderData)
        });
    }

    async deleteOrder(id) {
        return await this.request(`/orders/${id}`, { method: 'DELETE' });
    }

    async updateOrderStatus(id, status) {
        return await this.request(`/orders/${id}/status`, {
            method: 'PUT',
            body: JSON.stringify({ status })
        });
    }

    async getOrdersByStatus(status) {
        return await this.request(`/orders/status/${status}`);
    }

    async getOrdersByClient(clientId) {
        return await this.request(`/orders/client/${clientId}`);
    }

    async searchOrders(query) {
        return await this.request(`/orders/search?q=${encodeURIComponent(query)}`);
    }

    async getOrderStatuses() {
        return await this.request('/orders/statuses');
    }

    // Task Management Methods
    async getTasks(params = {}) {
        const queryString = new URLSearchParams(params).toString();
        return await this.request(`/tasks${queryString ? `?${queryString}` : ''}`);
    }

    async createTask(taskData) {
        return await this.request('/tasks', {
            method: 'POST',
            body: JSON.stringify(taskData)
        });
    }

    async updateTask(id, taskData) {
        return await this.request(`/tasks/${id}`, {
            method: 'PUT',
            body: JSON.stringify(taskData)
        });
    }

    async deleteTask(id) {
        return await this.request(`/tasks/${id}`, { method: 'DELETE' });
    }

    async updateTaskStatus(id, status) {
        return await this.request(`/tasks/${id}/status`, {
            method: 'PUT',
            body: JSON.stringify({ status })
        });
    }

    async completeTask(id) {
        return await this.request(`/tasks/${id}/complete`, { method: 'PUT' });
    }

    async getTasksByStatus(status) {
        return await this.request(`/tasks/status/${status}`);
    }

    async getTasksByPriority(priority) {
        return await this.request(`/tasks/priority/${priority}`);
    }

    async getOverdueTasks() {
        return await this.request('/tasks/overdue');
    }

    async getTasksByDate(date) {
        return await this.request(`/tasks/date/${date}`);
    }

    async getTasksByUser(userId) {
        return await this.request(`/tasks/user/${userId}`);
    }

    async assignTask(taskData) {
        return await this.request('/tasks/assign', {
            method: 'POST',
            body: JSON.stringify(taskData)
        });
    }

    async searchTasks(query) {
        return await this.request(`/tasks/search?q=${encodeURIComponent(query)}`);
    }

    // Ledger Request Methods
    async getLedgerRequests(params = {}) {
        const queryString = new URLSearchParams(params).toString();
        return await this.request(`/ledger-requests${queryString ? `?${queryString}` : ''}`);
    }

    async createLedgerRequest(requestData) {
        return await this.request('/ledger-requests', {
            method: 'POST',
            body: JSON.stringify(requestData)
        });
    }

    async updateLedgerRequest(id, requestData) {
        return await this.request(`/ledger-requests/${id}`, {
            method: 'PUT',
            body: JSON.stringify(requestData)
        });
    }

    async deleteLedgerRequest(id) {
        return await this.request(`/ledger-requests/${id}`, { method: 'DELETE' });
    }

    async updateLedgerRequestStatus(id, status, remarks = null) {
        return await this.request(`/ledger-requests/${id}/status`, {
            method: 'PUT',
            body: JSON.stringify({ status, admin_remarks: remarks })
        });
    }

    async getLedgerRequestsByStatus(status) {
        return await this.request(`/ledger-requests/status/${status}`);
    }

    async uploadLedgerFile(id, file) {
        const formData = new FormData();
        formData.append('file', file);

        return await this.request(`/admin/ledger-requests/${id}/upload`, {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${this.token}`
            },
            body: formData
        });
    }

    async confirmLedgerRequest(id) {
        return await this.request(`/admin/ledger-requests/${id}/confirm`, { method: 'PUT' });
    }

    async rejectLedgerRequest(id, remarks) {
        return await this.request(`/admin/ledger-requests/${id}/reject`, {
            method: 'PUT',
            body: JSON.stringify({ admin_remarks: remarks })
        });
    }

    async downloadLedgerFile(id) {
        const response = await fetch(`${this.baseURL}/admin/ledger-requests/${id}/download`, {
            headers: {
                'Authorization': `Bearer ${this.token}`
            }
        });
        return response.blob();
    }

    // Attendance Methods
    async getAttendance(params = {}) {
        const queryString = new URLSearchParams(params).toString();
        return await this.request(`/attendance${queryString ? `?${queryString}` : ''}`);
    }

    async createAttendance(attendanceData) {
        return await this.request('/attendance', {
            method: 'POST',
            body: JSON.stringify(attendanceData)
        });
    }

    async updateAttendance(id, attendanceData) {
        return await this.request(`/attendance/${id}`, {
            method: 'PUT',
            body: JSON.stringify(attendanceData)
        });
    }

    async deleteAttendance(id) {
        return await this.request(`/attendance/${id}`, { method: 'DELETE' });
    }

    async checkIn(location = null, remarks = null) {
        return await this.request('/attendance/check-in', {
            method: 'POST',
            body: JSON.stringify({ location, remarks })
        });
    }

    async checkOut(remarks = null) {
        return await this.request('/attendance/check-out', {
            method: 'POST',
            body: JSON.stringify({ remarks })
        });
    }

    async getTodayAttendance() {
        return await this.request('/attendance/today');
    }

    async getAttendanceByDate(date) {
        return await this.request(`/attendance/date/${date}`);
    }

    async getAttendanceByRange(startDate, endDate) {
        return await this.request(`/attendance/range?start_date=${startDate}&end_date=${endDate}`);
    }

    async getAttendanceByStatus(status) {
        return await this.request(`/attendance/status/${status}`);
    }

    async getAttendanceByUser(userId) {
        return await this.request(`/attendance/user/${userId}`);
    }

    async getAttendanceReport(startDate, endDate, userId = null) {
        const params = new URLSearchParams({ start_date: startDate, end_date: endDate });
        if (userId) params.append('user_id', userId);
        return await this.request(`/attendance/report?${params}`);
    }

    async getAttendanceSummary(startDate, endDate) {
        return await this.request(`/attendance/summary?start_date=${startDate}&end_date=${endDate}`);
    }

    // Sales Report Methods
    async getSalesReports(params = {}) {
        const queryString = new URLSearchParams(params).toString();
        return await this.request(`/sales-reports${queryString ? `?${queryString}` : ''}`);
    }

    async createSalesReport(reportData) {
        return await this.request('/sales-reports', {
            method: 'POST',
            body: JSON.stringify(reportData)
        });
    }

    async updateSalesReport(id, reportData) {
        return await this.request(`/sales-reports/${id}`, {
            method: 'PUT',
            body: JSON.stringify(reportData)
        });
    }

    async deleteSalesReport(id) {
        return await this.request(`/sales-reports/${id}`, { method: 'DELETE' });
    }

    async uploadVisitPhoto(id, file) {
        const formData = new FormData();
        formData.append('photo', file);

        return await this.request(`/sales-reports/${id}/photo`, {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${this.token}`
            },
            body: formData
        });
    }

    async getVisitPhoto(id) {
        return await this.request(`/sales-reports/${id}/photo`);
    }

    async getSalesReportsByDate(date) {
        return await this.request(`/sales-reports/date/${date}`);
    }

    async getSalesReportsByClient(clientId) {
        return await this.request(`/sales-reports/client/${clientId}`);
    }

    async getSalesReportsBySalesperson(userId) {
        return await this.request(`/sales-reports/salesperson/${userId}`);
    }

    async getSalesAnalytics(startDate = null, endDate = null) {
        const params = new URLSearchParams();
        if (startDate) params.append('start_date', startDate);
        if (endDate) params.append('end_date', endDate);
        return await this.request(`/sales-reports/analytics?${params}`);
    }

    async getSalesSummary(startDate = null, endDate = null) {
        const params = new URLSearchParams();
        if (startDate) params.append('start_date', startDate);
        if (endDate) params.append('end_date', endDate);
        return await this.request(`/sales-reports/summary?${params}`);
    }

    async getTopClients(startDate = null, endDate = null) {
        const params = new URLSearchParams();
        if (startDate) params.append('start_date', startDate);
        if (endDate) params.append('end_date', endDate);
        return await this.request(`/sales-reports/top-clients?${params}`);
    }

    async getSalesPerformance(startDate = null, endDate = null) {
        const params = new URLSearchParams();
        if (startDate) params.append('start_date', startDate);
        if (endDate) params.append('end_date', endDate);
        return await this.request(`/sales-reports/performance?${params}`);
    }

    // Module Access Methods
    async getModules() {
        return await this.request('/modules');
    }

    async getPermissions() {
        return await this.request('/modules/permissions');
    }

    async getUserModuleAccess(userId) {
        return await this.request(`/modules/${userId}/access`);
    }

    async updateUserModuleAccess(userId, moduleName, permissions, isActive) {
        return await this.request(`/modules/${userId}/access`, {
            method: 'PUT',
            body: JSON.stringify({
                module_name: moduleName,
                permissions: permissions,
                is_active: isActive
            })
        });
    }

    async assignModule(userId, moduleName, permissions) {
        return await this.request('/modules/assign', {
            method: 'POST',
            body: JSON.stringify({
                user_id: userId,
                module_name: moduleName,
                permissions: permissions
            })
        });
    }

    async revokeModule(userId, moduleName) {
        return await this.request('/modules/revoke', {
            method: 'DELETE',
            body: JSON.stringify({
                user_id: userId,
                module_name: moduleName
            })
        });
    }

    // Search Methods
    async searchUsers(query) {
        return await this.request(`/search/users?q=${encodeURIComponent(query)}`);
    }

    async searchClients(query) {
        return await this.request(`/search/clients?q=${encodeURIComponent(query)}`);
    }

    async searchOrders(query) {
        return await this.request(`/search/orders?q=${encodeURIComponent(query)}`);
    }

    async searchTasks(query) {
        return await this.request(`/search/tasks?q=${encodeURIComponent(query)}`);
    }

    // File Upload Methods
    async uploadLedger(file, requestDate) {
        const formData = new FormData();
        formData.append('file', file);
        formData.append('request_date', requestDate);

        return await this.request('/upload/ledger', {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${this.token}`
            },
            body: formData
        });
    }

    async uploadVisitPhoto(file) {
        const formData = new FormData();
        formData.append('photo', file);

        return await this.request('/upload/visit-photo', {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${this.token}`
            },
            body: formData
        });
    }

    async downloadFile(file) {
        const response = await fetch(`${this.baseURL}/upload/files/${file}`, {
            headers: {
                'Authorization': `Bearer ${this.token}`
            }
        });
        return response.blob();
    }

    async deleteFile(file) {
        return await this.request(`/upload/files/${file}`, { method: 'DELETE' });
    }
}

// Create a global instance
window.apiService = new ApiService();

export default window.apiService;
