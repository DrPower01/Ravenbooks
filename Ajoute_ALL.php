<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tabs Example</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<?php include 'sidebar.php'; ?>
<style>
    .container-fluid{
        margin-top: 0 !important;
    }
</style>
<div class="container-fluid">
  <div class="row">
    <!-- Sidebar (assuming it's part of sidebar.php) -->
    <div class="col-md-3">
      <!-- You can add custom styles to the sidebar if necessary -->
      <?php include 'sidebar.php'; ?>
    </div>

    <!-- Main content -->
    <div class="col-md-9">
      <h2>Add Books Panel</h2>

      <!-- Nav tabs -->
      <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item" role="presentation">
          <a class="nav-link active" id="home-tab" data-bs-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true">Add by ISBN</a>
        </li>
        <li class="nav-item" role="presentation">
          <a class="nav-link" id="profile-tab" data-bs-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="false">Add by CSV</a>
        </li>
        <li class="nav-item" role="presentation">
          <a class="nav-link" id="contact-tab" data-bs-toggle="tab" href="#contact" role="tab" aria-controls="contact" aria-selected="false">Manual Add</a>
        </li>
      </ul>

      <!-- Tab content -->
      <div class="tab-content mt-3" id="myTabContent">
        <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
          <h4>Home Tab</h4>
          <?php include 'Ajouter-livres-par-isbn.php'; ?>
        </div>
        <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
          <h4>Profile Tab</h4>
          <?php include 'Ajouter-livres-par-csv.php'; ?>
        </div>
        <div class="tab-pane fade" id="contact" role="tabpanel" aria-labelledby="contact-tab">
          <h4>Contact Tab</h4>
          <?php include 'Ajoute_manuel.php'; ?>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Bootstrap JS and dependencies -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>

</body>
</html>
