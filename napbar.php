<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
        <a class="navbar-brand" href="index.php">Rural Job Solution</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="index.php">Home</a>
                </li>
                <?php if (isset($_SESSION['role'])): ?>
                    <?php if ($_SESSION['role'] == 'client'): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="client_dashboard.php">Dashboard</a>
                        </li>
                    <?php elseif ($_SESSION['role'] == 'agent'): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="agent_dashboard.php">Dashboard</a>
                        </li>
                    <?php elseif ($_SESSION['role'] == 'worker'): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="worker_dashboard.php">Dashboard</a>
                        </li>
                    <?php elseif ($_SESSION['role'] == 'admin'): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="admin_dashboard.php">Admin Panel</a>
                        </li>
                    <?php endif; ?>
                    <li class="nav-item">
                        <a class="nav-link" href="log_out.php">Logout</a>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="register.php">Register</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="log_out.php">Login</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>