<?php
session_start();
require_once '../includes/config.php'; 
$page_title = "Dashboard";
// require_once 'includes/header.php';
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Dashboard</h1>
</div>

<div class="row">
    <div class="col-md-3 mb-4">
        <div class="card text-white bg-primary">
            <div class="card-body">
                <div class="d-flex">
                    <div>
                        <h4 class="card-title">
                            <?php
                            $stmt = $pdo->prepare("SELECT COUNT(*) FROM content WHERE content_type = 'event' AND status = 'published'");
                            $stmt->execute();
                            echo $stmt->fetchColumn();
                            ?>
                        </h4>
                        <p class="card-text">Published Events</p>
                    </div>
                    <div class="ms-auto">
                        <i class="bi bi-calendar-event" style="font-size: 2rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-4">
        <div class="card text-white bg-success">
            <div class="card-body">
                <div class="d-flex">
                    <div>
                        <h4 class="card-title">
                            <?php
                            $stmt = $pdo->prepare("SELECT COUNT(*) FROM content WHERE content_type = 'news' AND status = 'published'");
                            $stmt->execute();
                            echo $stmt->fetchColumn();
                            ?>
                        </h4>
                        <p class="card-text">News Articles</p>
                    </div>
                    <div class="ms-auto">
                        <i class="bi bi-newspaper" style="font-size: 2rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-4">
        <div class="card text-white bg-warning">
            <div class="card-body">
                <div class="d-flex">
                    <div>
                        <h4 class="card-title">
                            <?php
                            $stmt = $pdo->prepare("SELECT COUNT(*) FROM content WHERE status = 'draft'");
                            $stmt->execute();
                            echo $stmt->fetchColumn();
                            ?>
                        </h4>
                        <p class="card-text">Draft Items</p>
                    </div>
                    <div class="ms-auto">
                        <i class="bi bi-pencil-square" style="font-size: 2rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-4">
        <div class="card text-white bg-info">
            <div class="card-body">
                <div class="d-flex">
                    <div>
                        <h4 class="card-title">
                            <?php
                            $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE is_active = TRUE");
                            $stmt->execute();
                            echo $stmt->fetchColumn();
                            ?>
                        </h4>
                        <p class="card-text">Active Users</p>
                    </div>
                    <div class="ms-auto">
                        <i class="bi bi-people" style="font-size: 2rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="events/add.php" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-2"></i>Add New Event
                    </a>
                    <a href="events/index.php" class="btn btn-outline-primary">
                        <i class="bi bi-list-ul me-2"></i>Manage Events
                    </a>
                    <a href="../events.php" class="btn btn-outline-secondary" target="_blank">
                        <i class="bi bi-eye me-2"></i>View Events Page
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Recent Activity</h5>
            </div>
            <div class="card-body">
                <?php
                $stmt = $pdo->prepare("
                    SELECT c.title, c.content_type, c.created_at, u.username 
                    FROM content c 
                    LEFT JOIN users u ON c.author_id = u.id 
                    ORDER BY c.created_at DESC 
                    LIMIT 5
                ");
                $stmt->execute();
                $recent_activities = $stmt->fetchAll();
                
                if ($recent_activities):
                    foreach ($recent_activities as $activity):
                ?>
                    <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-2">
                        <div>
                            <strong><?php echo $activity['title']; ?></strong>
                            <br>
                            <small class="text-muted">
                                <?php echo ucfirst($activity['content_type']); ?> • 
                                By <?php echo $activity['username']; ?>
                            </small>
                        </div>
                        <small class="text-muted">
                            <?php echo date('M j, g:i A', strtotime($activity['created_at'])); ?>
                        </small>
                    </div>
                <?php
                    endforeach;
                else:
                ?>
                    <p class="text-muted">No recent activity</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>