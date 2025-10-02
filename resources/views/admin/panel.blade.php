@extends('layouts.vertical', ['title' => 'Admin Panel'])

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Admin Dashboard</h4>
            </div>
        </div>
    </div>

    <!-- Top Summary Cards -->
    <div class="row">
        <div class="col-xxl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <div class="avatar-md bg-primary bg-opacity-10 rounded">
                                <iconify-icon icon="iconamoon:clock-duotone" class="avatar-title text-primary fs-32"></iconify-icon>
                            </div>
                        </div>
                        <div class="col-6 text-end">
                            <p class="text-muted mb-0 text-truncate">Pending Daily Work</p>
                            <h3 class="text-dark mt-1 mb-0" id="admin-pending-daily">0</h3>
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
                            <p class="text-muted mb-0 text-truncate">Completed Daily Work</p>
                            <h3 class="text-dark mt-1 mb-0" id="admin-completed-daily">0</h3>
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
                                <iconify-icon icon="iconamoon:notification-bing-duotone" class="avatar-title text-warning fs-32"></iconify-icon>
                            </div>
                        </div>
                        <div class="col-6 text-end">
                            <p class="text-muted mb-0 text-truncate">Pending SMM Tasks</p>
                            <h3 class="text-dark mt-1 mb-0" id="admin-pending-smm">0</h3>
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
                                <iconify-icon icon="iconamoon:check-double-duotone" class="avatar-title text-info fs-32"></iconify-icon>
                            </div>
                        </div>
                        <div class="col-6 text-end">
                            <p class="text-muted mb-0 text-truncate">Done SMM Tasks</p>
                            <h3 class="text-dark mt-1 mb-0" id="admin-done-smm">0</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Big Red Card Row -->
    <div class="row">
        <div class="col-xxl-6 col-md-12">
            <div class="card border-0" style="background:#ef4444;">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h3 class="text-white mb-1" id="admin-completed-smm">0</h3>
                            <p class="text-white-50 mb-0">Completed SMM Task</p>
                        </div>
                        <iconify-icon icon="iconamoon:close-circle-duotone" class="text-white fs-32"></iconify-icon>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xxl-6 col-md-12">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0">Quick Access</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex flex-wrap gap-2">
                        <button class="btn btn-outline-primary" id="qa-attendance">
                            <iconify-icon icon="iconamoon:time-duotone" class="me-1"></iconify-icon>
                            Attendance Overview
                        </button>
                        <button class="btn btn-outline-success" id="qa-sales">
                            <iconify-icon icon="iconamoon:chart-line-duotone" class="me-1"></iconify-icon>
                            Sales Reports
                        </button>
                        <button class="btn btn-outline-info" id="qa-orders">
                            <iconify-icon icon="iconamoon:shopping-cart-duotone" class="me-1"></iconify-icon>
                            Orders
                        </button>
                        <button class="btn btn-outline-warning" id="qa-tasks">
                            <iconify-icon icon="iconamoon:check-circle-duotone" class="me-1"></iconify-icon>
                            Tasks
                        </button>
                        <button class="btn btn-primary" id="qa-create-user">
                            <iconify-icon icon="iconamoon:user-add-duotone" class="me-1"></iconify-icon>
                            Create Employee
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Create User</h5>
                </div>
                <div class="card-body">
                    <form id="createUserForm">
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control" id="name" placeholder="Enter full name" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" placeholder="Enter email" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" placeholder="Enter password" required>
                        </div>
                        <div class="mb-3">
                            <label for="role" class="form-label">Role</label>
                            <select class="form-select" id="role">
                                <option value="admin">Admin</option>
                                <option value="sales_person">Sales Person</option>
                                <option value="office_staff">Office Staff</option>
                                <option value="client">Client</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary" id="createBtn">Create</button>
                    </form>
                </div>
            </div>
        </div>

    </div>
@endsection

@section('script')
    <script>
        (function () {
            const debug = (msg, obj) => {
                const el = document.getElementById('debugBox');
                try {
                    el.textContent += `\n${msg}` + (obj ? `: ${JSON.stringify(obj)}` : '');
                } catch (_) {
                    el.textContent += `\n${msg}`;
                }
            };

            const userJson = localStorage.getItem('user_data');
            const token = localStorage.getItem('auth_token');
            let user = null;
            try { user = userJson ? JSON.parse(userJson) : null; } catch (_) {}

            if (!token || !user) {
                alert('You must be logged in. Redirecting to login.');
                window.location.href = '/crm-login';
                return;
            }

            if (!user.role || String(user.role).toLowerCase() !== 'admin') {
                alert('Admins only. Redirecting to dashboard.');
                window.location.href = '/crm';
                return;
            }

            debug('Admin verified', { email: user.email, role: user.role });

            const form = document.getElementById('createUserForm');
            const btn = document.getElementById('createBtn');
            // Load admin stats
            loadAdminStats(token);
            // Quick access handlers
            const go = (section) => { try { localStorage.setItem('navigate_to', section); } catch(_) {}; window.location.href = '/crm'; };
            document.getElementById('qa-attendance').addEventListener('click', () => go('attendance'));
            document.getElementById('qa-sales').addEventListener('click', () => go('sales'));
            document.getElementById('qa-orders').addEventListener('click', () => go('orders'));
            document.getElementById('qa-tasks').addEventListener('click', () => go('tasks'));
            document.getElementById('qa-create-user').addEventListener('click', () => window.scrollTo({ top: document.getElementById('createUserForm').getBoundingClientRect().top + window.scrollY - 80, behavior: 'smooth' }));
            form.addEventListener('submit', async function (e) {
                e.preventDefault();

                const name = document.getElementById('name').value.trim();
                const email = document.getElementById('email').value.trim();
                const password = document.getElementById('password').value;
                const role = document.getElementById('role').value;

                if (!name || !email || !password) {
                    alert('Please fill all fields');
                    return;
                }

                btn.disabled = true;
                try {
                    const res = await fetch('/api/users', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'Authorization': `Bearer ${token}`
                        },
                        body: JSON.stringify({ name, email, password, role })
                    });

                    const data = await res.json().catch(() => ({}));
                    debug('Create user response', { status: res.status, data });
                    if (res.ok) {
                        alert('User created successfully');
                        form.reset();
                    } else if (res.status === 403) {
                        alert('Forbidden: Admins only.');
                    } else {
                        alert(data.message || 'Failed to create user');
                    }
                } catch (err) {
                    debug('Error', { message: err.message });
                    alert('Network error. Please try again.');
                } finally {
                    btn.disabled = false;
                }
            });
            async function loadAdminStats(token) {
                try {
                    const statsResponse = await fetch('/api/dashboard/stats', {
                        headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
                    });
                    if (statsResponse.ok) {
                        const stats = await statsResponse.json();
                        if (stats.success) {
                            const data = stats.data || {};
                            // Map existing stats to the new cards where possible
                            document.getElementById('admin-pending-daily').textContent = data.pending_daily_work ?? (data.pending_tasks ?? 0);
                            document.getElementById('admin-completed-daily').textContent = data.completed_daily_work ?? (data.completed_tasks ?? 0);
                            document.getElementById('admin-pending-smm').textContent = data.pending_smm_tasks ?? 0;
                            document.getElementById('admin-done-smm').textContent = data.done_smm_tasks ?? 0;
                            document.getElementById('admin-completed-smm').textContent = data.completed_smm_total ?? 0;
                        }
                    }
                } catch (e) {
                    debug('Failed to load admin stats', { message: e.message });
                }
            }

        })();
    </script>
@endsection


