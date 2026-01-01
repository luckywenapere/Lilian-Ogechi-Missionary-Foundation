<?php
$page_title = "Manage Events";
require_once '../includes/header.php';

// Handle event deletion
if (isset($_GET['delete'])) {
    $event_id = intval($_GET['delete']);
    
    try {
        // Get event image first to delete it
        $stmt = $pdo->prepare("SELECT featured_image FROM content WHERE id = ?");
        $stmt->execute([$event_id]);
        $event = $stmt->fetch();
        
        // Delete event from database
        $stmt = $pdo->prepare("DELETE FROM content WHERE id = ?");
        $stmt->execute([$event_id]);
        
        // Delete associated image file
        if ($event && !empty($event['featured_image'])) {
            deleteImage($event['featured_image'], 'events');
        }
        
        $_SESSION['success_message'] = "Event deleted successfully!";
    } catch (PDOException $e) {
        $_SESSION['error_message'] = "Error deleting event: " . $e->getMessage();
    }
    
    header('Location: index.php');
    exit;
}

// Get all events
$stmt = $pdo->prepare("
    SELECT c.*, u.username as author_name 
    FROM content c 
    LEFT JOIN users u ON c.author_id = u.id 
    WHERE c.content_type = 'event' 
    ORDER BY c.event_date DESC
");
$stmt->execute();
$events = $stmt->fetchAll();
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Manage Events</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="add.php" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Add New Event
        </a>
    </div>
</div>

<?php if (isset($_SESSION['success_message'])): ?>
    <div class="alert alert-success"><?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?></div>
<?php endif; ?>

<?php if (isset($_SESSION['error_message'])): ?>
    <div class="alert alert-danger"><?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?></div>
<?php endif; ?>

<div class="card">
    <div class="card-body">
        <?php if ($events): ?>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Title</th>
                            <th>Date & Location</th>
                            <th>Status</th>
                            <th>Author</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($events as $event): ?>
                        <tr>
                            <td>
                                <?php if ($event['featured_image']): ?>
                                    <img src="../uploads/events/<?php echo $event['featured_image']; ?>" 
                                         alt="<?php echo $event['title']; ?>" 
                                         style="width: 60px; height: 40px; object-fit: cover;" 
                                         class="rounded">
                                <?php else: ?>
                                    <span class="text-muted">No image</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <strong><?php echo $event['title']; ?></strong>
                                <br>
                                <small class="text-muted"><?php echo substr($event['description'], 0, 100); ?>...</small>
                            </td>
                            <td>
                                <small>
                                    <strong>Date:</strong> <?php echo $event['event_date'] ? formatEventDate($event['event_date']) : 'Not set'; ?><br>
                                    <strong>Location:</strong> <?php echo $event['event_location'] ?: 'Not set'; ?>
                                </small>
                            </td>
                            <td>
                                <span class="badge bg-<?php echo $event['status'] == 'published' ? 'success' : 'secondary'; ?>">
                                    <?php echo ucfirst($event['status']); ?>
                                </span>
                            </td>
                            <td><?php echo $event['author_name']; ?></td>
                            <td>
                                <small><?php echo date('M j, Y', strtotime($event['created_at'])); ?></small>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="edit.php?id=<?php echo $event['id']; ?>" class="btn btn-outline-primary">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <a href="../events.php#event-<?php echo $event['id']; ?>" 
                                       target="_blank" 
                                       class="btn btn-outline-info" 
                                       title="View on website">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="index.php?delete=<?php echo $event['id']; ?>" 
                                       class="btn btn-outline-danger" 
                                       onclick="return confirm('Are you sure you want to delete this event?')">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="text-center py-4">
                <i class="bi bi-calendar-x" style="font-size: 3rem; color: #6c757d;"></i>
                <h4 class="mt-3">No Events Found</h4>
                <p class="text-muted">Get started by creating your first event.</p>
                <a href="add.php" class="btn btn-primary">Create Your First Event</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>