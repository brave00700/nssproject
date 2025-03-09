<header>
  <div class="header-container">
    <img src="../assets/icons/sju_logo.png" class="logo" alt="SJU Logo" />
    <div class="header-content">
      <div class="header-text">NATIONAL SERVICE SCHEME</div>
      <div class="header-text">ST JOSEPH'S UNIVERSITY</div>
      <div class="header-subtext">STUDENT PORTAL</div>
    </div>
    <img src="../assets/icons/nss_logo.png" class="logo" alt="NSS Logo" />
  </div>
</header>
<?php if(!isset($hideNotifications) || !$hideNotifications): ?>

<!-- Notification Bell -->
<div class="notification-icon">
    <button id="notif-btn">
        <i class="fa-solid fa-bell"></i>
        <span id="notif-count">3</span> <!-- Dynamic count -->
    </button>
</div>

<!-- Floating Notification Panel -->
<div class="notification-panel" id="notificationPanel">
    <div class="notif-header">
        <h3>Notifications</h3>
        <button id="notif-close">&times;</button>
    </div>
    <div class="notif-body" id="notif-list">
        <p class="notif-empty">Loading...</p>
    </div>
</div>

<?php endif; ?>