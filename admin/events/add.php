<?php
session_start();
require_once '../../includes/config.php'; 
$page_title = "Add New Event";
require_once '../includes/header.php';

$error = '';
$success = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $content_body = trim($_POST['content_body']);
    $event_date = $_POST['event_date'];
    $event_location = trim($_POST['event_location']);
    $status = $_POST['status'];
    
    try {
        // Validate required fields
        if (empty($title) || empty($description)) {
            throw new Exception('Title and description are required.');
        }
        
        // Handle image upload
        $featured_image = null;
        if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] === UPLOAD_ERR_OK) {
            $featured_image = uploadImage($_FILES['featured_image'], 'events');
        }
        
        // Insert event into database
        $stmt = $pdo->prepare("
            INSERT INTO content (title, content_type, description, content_body, featured_image, event_date, event_location, status, author_id) 
            VALUES (?, 'event', ?, ?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([
            $title,
            $description,
            $content_body,
            $featured_image,
            $event_date ?: null,
            $event_location,
            $status,
            $_SESSION['user_id']
        ]);
        
        $success = 'Event created successfully!';
        
        // Clear form fields
        $title = $description = $content_body = $event_date = $event_location = '';
        
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Add New Event</h1>
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
                               value="<?php echo isset($_POST['title']) ? htmlspecialchars($_POST['title']) : ''; ?>" 
                               required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Short Description *</label>
                        <textarea class="form-control" id="description" name="description" rows="3" 
                                  required><?php echo isset($_POST['description']) ? htmlspecialchars($_POST['description']) : ''; ?></textarea>
                        <div class="form-text">A brief description that appears on the events listing page.</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="content_body" class="form-label">Full Event Details</label>
                        <textarea class="form-control" id="content_body" name="content_body" rows="6"><?php echo isset($_POST['content_body']) ? htmlspecialchars($_POST['content_body']) : ''; ?></textarea>
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
                                       value="<?php echo isset($_POST['event_date']) ? $_POST['event_date'] : ''; ?>">
                            </div>
                            
                            <div class="mb-3">
                                <label for="event_location" class="form-label">Event Location</label>
                                <input type="text" class="form-control" id="event_location" name="event_location" 
                                       value="<?php echo isset($_POST['event_location']) ? htmlspecialchars($_POST['event_location']) : ''; ?>">
                            </div>
                            
                            <div class="mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="draft" <?php echo (isset($_POST['status']) && $_POST['status'] == 'draft') ? 'selected' : ''; ?>>Draft</option>
                                    <option value="published" <?php echo (isset($_POST['status']) && $_POST['status'] == 'published') ? 'selected' : ''; ?>>Published</option>
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
                            <div class="mb-3">
                                <label for="featured_image" class="form-label">Upload Image</label>
                                <input type="file" class="form-control" id="featured_image" name="featured_image" 
                                       accept="image/jpeg,image/png,image/gif">
                                <div class="form-text">
                                    Recommended size: 400x300px. Max file size: 5MB.
                                </div>
                            </div>
                            
                            <?php if (isset($featured_image) && $featured_image): ?>
                                <div class="mt-2">
                                    <img src="../uploads/events/<?php echo $featured_image; ?>" 
                                         alt="Uploaded image" 
                                         style="max-width: 100%; height: auto;" 
                                         class="rounded">
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle"></i> Create Event
                </button>
                <button type="reset" class="btn btn-outline-secondary">Reset Form</button>
                <a href="index.php" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>