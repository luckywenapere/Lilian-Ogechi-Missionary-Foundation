<?php
$page_title = "Add News Article";
require_once '../includes/header.php';

$error = '';
$success = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $content_body = trim($_POST['content_body']);
    $status = $_POST['status'];
    
    try {
        // Validate required fields
        if (empty($title) || empty($description) || empty($content_body)) {
            throw new Exception('Title, description, and content are required.');
        }
        
        // Handle image upload
        $featured_image = null;
        if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] === UPLOAD_ERR_OK) {
            $featured_image = uploadImage($_FILES['featured_image'], 'news');
        }
        
        // Insert news into database
        $stmt = $pdo->prepare("
            INSERT INTO content (title, content_type, description, content_body, featured_image, status, author_id) 
            VALUES (?, 'news', ?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([
            $title,
            $description,
            $content_body,
            $featured_image,
            $status,
            $_SESSION['user_id']
        ]);
        
        $success = 'News article created successfully!';
        
        // Clear form fields
        $title = $description = $content_body = '';
        
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Add News Article</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="index.php" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back to News
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
                        <label for="title" class="form-label">News Title *</label>
                        <input type="text" class="form-control" id="title" name="title" 
                               value="<?php echo isset($_POST['title']) ? htmlspecialchars($_POST['title']) : ''; ?>" 
                               required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Short Description/Excerpt *</label>
                        <textarea class="form-control" id="description" name="description" rows="3" 
                                  required><?php echo isset($_POST['description']) ? htmlspecialchars($_POST['description']) : ''; ?></textarea>
                        <div class="form-text">A brief summary that appears on the news listing page.</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="content_body" class="form-label">News Content *</label>
                        <textarea class="form-control" id="content_body" name="content_body" rows="12" 
                                  required><?php echo isset($_POST['content_body']) ? htmlspecialchars($_POST['content_body']) : ''; ?></textarea>
                        <div class="form-text">The full content of your news article.</div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <!-- Publishing Options -->
                    <div class="card">
                        <div class="card-header">
                            <h6 class="card-title mb-0">Publishing Options</h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="