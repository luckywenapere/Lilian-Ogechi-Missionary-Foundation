<?php
$page_title = "Manage News";
require_once '../includes/header.php';

// Handle news deletion
if (isset($_GET['delete'])) {
    $news_id = intval($_GET['delete']);
    
    try {
        // Get news image first to delete it
        $stmt = $pdo->prepare("SELECT featured_image FROM content WHERE id = ?");
        $stmt->execute([$news_id]);
        $news = $stmt->fetch();
        
        // Delete news from database
        $stmt = $pdo->prepare("DELETE FROM content WHERE id = ?");
        $stmt->execute([$news_id]);
        
        // Delete associated image file
        if ($news && !empty($news['featured_image'])) {
            deleteImage($news['featured_image'], 'news');
        }
        
        $_SESSION['success_message'] = "News article deleted successfully!";
    } catch (PDOException $e) {
        $_SESSION['error_message'] = "Error deleting news article: " . $e->getMessage();
    }
    
    header('Location: index.php');
    exit;
}

// Get all news articles
$stmt = $pdo->prepare("
    SELECT c.*, u.username as author_name 
    FROM content c 
    LEFT JOIN users u ON c.author_id = u.id 
    WHERE c.content_type = 'news' 
    ORDER BY c.created_at DESC
");
$stmt->execute();
$news_articles = $stmt->fetchAll();
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Manage News</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="add.php" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Add News Article
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
        <?php if ($news_articles): ?>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Title</th>
                            <th>Excerpt</th>
                            <th>Status</th>
                            <th>Author</th>
                            <th>Published</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($news_articles as $news): ?>
                        <tr>
                            <td>
                                <?php if ($news['featured_image']): ?>
                                    <img src="../uploads/news/<?php echo $news['featured_image']; ?>" 
                                         alt="<?php echo $news['title']; ?>" 
                                         style="width: 60px; height: 40px; object-fit: cover;" 
                                         class="rounded">
                                <?php else: ?>
                                    <span class="text-muted">No image</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <strong><?php echo $news['title']; ?></strong>
                            </td>
                            <td>
                                <small class="text-muted">
                                    <?php 
                                    $excerpt = strip_tags($news['description']);
                                    echo strlen($excerpt) > 100 ? substr($excerpt, 0, 100) . '...' : $excerpt;
                                    ?>
                                </small>
                            </td>
                            <td>
                                <span class="badge bg-<?php echo $news['status'] == 'published' ? 'success' : 'secondary'; ?>">
                                    <?php echo ucfirst($news['status']); ?>
                                </span>
                            </td>
                            <td><?php echo $news['author_name']; ?></td>
                            <td>
                                <small><?php echo date('M j, Y', strtotime($news['created_at'])); ?></small>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="edit.php?id=<?php echo $news['id']; ?>" class="btn btn-outline-primary">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <a href="index.php?delete=<?php echo $news['id']; ?>" 
                                       class="btn btn-outline-danger" 
                                       onclick="return confirm('Are you sure you want to delete this news article?')">
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
                <i class="bi bi-newspaper" style="font-size: 3rem; color: #6c757d;"></i>
                <h4 class="mt-3">No News Articles</h4>
                <p class="text-muted">Get started by creating your first news article.</p>
                <a href="add.php" class="btn btn-primary">Create Your First News Article</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>