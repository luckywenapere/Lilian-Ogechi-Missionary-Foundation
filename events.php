<?php
// events.php
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Get published events from database
$events = getEvents('published');
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Events & Activities | Lilian Ogechi Missionary Foundation</title>
    <link rel="icon" type="image/png" sizes="512x512" href="assets/images/logo.png">
    <link rel="apple-touch-icon" href="assets/images/logo.png">
    <meta name="theme-color" content="#0d6efd">
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
      rel="stylesheet"
    />
    <style>
      body {
        font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
        line-height: 1.7;
      }
      .hero {
        background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)),
          url("assets/images/events-hero.png") center/cover no-repeat;
        color: #fff;
        padding: 100px 0;
        text-align: center;
      }
      .hero h1 {
        font-size: 3rem;
        font-weight: bold;
      }
      .event-card img {
        max-height: 200px;
        object-fit: cover;
      }
      .event-meta {
        font-size: 0.9rem;
        color: #6c757d;
      }
      footer {
        background: #212529;
        color: #ccc;
        padding: 40px 0;
        margin-top: 60px;
      }
      footer a {
        color: #0d6efd;
        text-decoration: none;
      }
      footer a:hover {
        text-decoration: underline;
      }

      /* Responsive Logo */
      .logo {
        max-height: 50px;
        width: auto;
      }

      @media (max-width: 768px) {
        .logo {
          max-height: 40px;
        }
      }

      @media (max-width: 576px) {
        .logo {
          max-height: 30px;
        }
      }
    </style>
  </head>
  <body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
      <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="index.html">
          <img
            src="assets/images/logo.png"
            alt="Foundation Logo"
            class="logo img-fluid me-2"
        /></a>
        <button
          class="navbar-toggler"
          type="button"
          data-bs-toggle="collapse"
          data-bs-target="#navmenu"
          aria-controls="navmenu"
          aria-expanded="false"
          aria-label="Toggle navigation"
        >
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navmenu">
          <ul class="navbar-nav ms-auto">
            <li class="nav-item">
              <a class="nav-link" href="index.html">Home</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="about.html">About</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="programs.html">Programs</a>
            </li>
            <li class="nav-item">
              <a class="nav-link active" href="events.php">Events</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="news.html">News</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="gallery.html">Gallery</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="donate.html">Donate</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="contact.html">Contact</a>
            </li>
          </ul>
        </div>
      </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
      <div class="container">
        <h1>Upcoming Events & Activities</h1>
        <p class="lead">
          Join us in making a difference through our projects, missions, and
          community outreach.
        </p>
      </div>
    </section>

    <!-- Events Section -->
    <section class="py-5">
      <div class="container">
        <div class="text-center mb-5">
          <h2 class="fw-bold">Our Upcoming Activities</h2>
          <p class="text-muted">
            Stay updated with our latest missions, projects, and volunteer
            opportunities.
          </p>
        </div>

        <div class="row g-4">
          <?php if ($events): ?>
            <?php foreach ($events as $event): ?>
            <div class="col-md-6 col-lg-4" id="event-<?php echo $event['id']; ?>">
              <div class="card shadow-sm event-card h-100">
                <?php if ($event['featured_image']): ?>
                  <img src="uploads/events/<?php echo $event['featured_image']; ?>" 
                       class="card-img-top" 
                       alt="<?php echo $event['title']; ?>">
                <?php else: ?>
                  <img src="assets/images/event-placeholder.jpg" 
                       class="card-img-top" 
                       alt="Event placeholder">
                <?php endif; ?>
                <div class="card-body">
                  <h5 class="card-title"><?php echo $event['title']; ?></h5>
                  <p class="event-meta">
                    <i class="bi bi-calendar-event"></i> 
                    <?php echo $event['event_date'] ? formatEventDate($event['event_date']) : 'Date TBA'; ?> 
                    <?php if ($event['event_location']): ?>
                    | <?php echo $event['event_location']; ?>
                    <?php endif; ?>
                  </p>
                  <p class="card-text"><?php echo $event['description']; ?></p>
                  <?php if ($event['content_body']): ?>
                    <a href="#" class="btn btn-primary btn-sm">Learn More</a>
                  <?php endif; ?>
                </div>
              </div>
            </div>
            <?php endforeach; ?>
          <?php else: ?>
            <div class="col-12 text-center py-5">
              <i class="bi bi-calendar-x" style="font-size: 4rem; color: #6c757d;"></i>
              <h4 class="mt-3">No Upcoming Events</h4>
              <p class="text-muted">Check back later for upcoming events and activities.</p>
            </div>
          <?php endif; ?>
        </div>

        <!-- Call to Action -->
        <div class="text-center mt-5">
          <h4>Want to get involved?</h4>
          <p>
            Sign up as a volunteer or support us with donations to make these
            events a success.
          </p>
          <a href="donate.html" class="btn btn-success me-2">Donate Now</a>
          <a href="contact.html" class="btn btn-outline-primary">Volunteer</a>
        </div>
      </div>
    </section>

    <!-- Footer -->
    <footer>
      <div class="container text-center">
        <p>
          &copy; 2025 Lilian Ogechi Missionary Foundation. All Rights Reserved.
        </p>
        <p>
          <a href="privacy.html">Privacy Policy</a> |
          <a href="terms.html">Terms of Service</a>
        </p>
      </div>
    </footer>

    <!-- Bootstrap + Icons -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css"
      rel="stylesheet"
    />
  </body>
</html>