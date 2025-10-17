<?php
$page_title = "Edit Event";
require_once '../includes/header.php';

// Check if event ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$event_id = intval($_GET['id']);
$error = '';
$success = '';

// Get current event data
try {
    $stmt = $pdo->prepare("SELECT * FROM content WHERE id = ? AND content_type = 'event'");
    $stmt->execute([$event_id]);
    $event = $stmt->fetch();
    
    if (!$event) {
        header('Location: index.php');
        exit;
    }
} catch (PDOException $e) {
    die("Error loading event: " . $e->getMessage());
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $content_body = trim($_POST['content_body']);
    $event_date = $_POST['event_date'];
    $event_location = trim($_POST['event_location']);
    $status = $_POST['status'];
    $remove_image = isset($_POST['remove_image']);
    
    try {
        // Validate required fields
        if (empty($title) || empty($description)) {
            throw new Exception('Title and description are required.');
        }
        
        // Handle image upload/removal
        $featured_image = $event['featured_image'];
        
        if ($remove_image && $featured_image) {
            deleteImage($featured_image, 'events');
            $featured_image = null;
        }
        
        if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] === UPLOAD_ERR_OK) {
            // Delete old image if exists
            if ($featured_image) {
                deleteImage($featured_image, 'events');
            }
            $featured_image = uploadImage($_FILES['featured_image'], 'events');
        }
        
        // Update event in database
        $stmt = $pdo->prepare("
            UPDATE content 
            SET title = ?, description = ?, content_body = ?, featured_image = ?, 
                event_date = ?, event_location = ?, status = ?, updated_at = CURRENT_TIMESTAMP 
            WHERE id = ?
        ");
        
        $stmt->execute([
            $title,
            $description,
            $content_body,
            $featured_image,
            $event_date ?: null,
            $event_location,
            $status,
            $event_id
        ]);
        
        $success = 'Event updated successfully!';
        
        // Refresh event data
        $stmt = $pdo->prepare("SELECT * FROM content WHERE id = ?");
        $stmt->execute([$event_id]);
        $event = $stmt->fetch();
        
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Edit Event</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="index.php" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back to Events
        </a>
    </div>
</div>

<?php if ($error): ?>
    <div class="alert alert-danger"><?php echo $error; ?></div>
<?php endif; ?>

<?php if ($success): ?>
    <div class="alert alert-success"><?php echo $success; ?></div>
<?php endif; ?>

<div class="card">
    <div class="card-body">
        <form method="POST" action="" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-8">
                    <!-- Basic Information -->
                    <div class="mb-3">
                        <label for="title" class="form-label">Event Title *</label>
                        <input type="text" class="form-control" id="title" name="title" 
                               value="<?php echo htmlspecialchars($event['title']); ?>" 
                               required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Short Description *</label>
                        <textarea class="form-control" id="description" name="description" rows="3" 
                                  required><?php echo htmlspecialchars($event['description']); ?></textarea>
                        <div class="form-text">A brief description that appears on the events listing page.</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="content_body" class="form-label">Full Event Details</label>
                        <textarea class="form-control" id="content_body" name="content_body" rows="6"><?php echo htmlspecialchars($event['content_body']); ?></textarea>
                        <div class="form-text">Full details about the event (optional).</div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <!-- Event Details -->
                    <div class="card">
                        <div class="card-header">
                            <h6 class="card-title mb-0">Event Details</h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="event_date" class="form-label">Event Date</label>
                                <input type="date" class="form-control" id="event_date" name="event_date" 
                                       value="<?php echo $event['event_date']; ?>">
                            </div>
                            
                            <div class="mb-3">
                                <label for="event_location" class="form-label">Event Location</label>
                                <input type="text" class="form-control" id="event_location" name="event_location" 
                                       value="<?php echo htmlspecialchars($event['event_location']); ?>">
                            </div>
                            
                            <div class="mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="draft" <?php echo $event['status'] == 'draft' ? 'selected' : ''; ?>>Draft</option>
                                    <option value="published" <?php echo $event['status'] == 'published' ? 'selected' : ''; ?>>Published</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Featured Image -->
                    <div class="card mt-3">
                        <div class="card-header">
                            <h6 class="card-title mb-0">Featured Image</h6>
                        </div>
                        <div class="card-body">
                            <?php if ($event['featured_image']): ?>
                                <div class="mb-3">
                                    <img src="../uploads/events/<?php echo $event['featured_image']; ?>" 
                                         alt="Current featured image" 
                                         style="max-width: 100%; height: auto;" 
                                         class="rounded mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="remove_image" name="remove_image">
                                        <label class="form-check-label" for="remove_image">
                                            Remove current image
                                        </label>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <div class="mb-3">
                                <label for="featured_image" class="form-label">
                                    <?php echo $event['featured_image'] ? 'Upload New Image' : 'Upload Image'; ?>
                                </label>
                                <input type="file" class="form-control" id="featured_image" name="featured_image" 
                                       accept="image/jpeg,image/png,image/gif">
                                <div class="form-text">
                                    Recommended size: 400x300px. Max file size: 5MB.
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Event Information -->
                    <div class="card mt-3">
                        <div class="card-header">
                            <h6 class="card-title mb-0">Event Information</h6>
                        </div>
                        <div class="card-body">
                            <small class="text-muted">
                                <strong>Created:</strong> <?php echo date('F j, Y g:i A', strtotime($event['created_at'])); ?><br>
                                <strong>Last Updated:</strong> <?php echo date('F j, Y g:i A', strtotime($event['updated_at'])); ?><br>
                                <strong>ID:</strong> <?php echo $event['id']; ?>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle"></i> Update Event
                </button>
                <a href="index.php" class="btn btn-outline-secondary">Cancel</a>
                <a href="index.php?delete=<?php echo $event['id']; ?>" 
                   class="btn btn-outline-danger float-end" 
                   onclick="return confirm('Are you sure you want to delete this event? This action cannot be undone.')">
                    <i class="bi bi-trash"></i> Delete Event
                </a>
            </div>
        </form>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>