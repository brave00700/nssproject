document.querySelector(".ham-icon").addEventListener("click", () => {
  document.querySelector(".nav > ul").classList.toggle("scroll");
});

// Adjust screen height
function adjustMainHeight() {
  const logoHeight = document.querySelector('header').offsetHeight;
  const navHeight = document.querySelector('.nav').offsetHeight;
  const mainElement = document.querySelector('.main');

  mainElement.style.minHeight = `calc(100vh - ${logoHeight + navHeight}px - 40px)`;
}

// Notifications
document.getElementById("notif-btn").addEventListener("click", function () {
  let panel = document.getElementById("notificationPanel");
  panel.classList.toggle("active");

});

// Close panel
document.getElementById("notif-close").addEventListener("click", function () {
  document.getElementById("notificationPanel").classList.remove("active");
  fetch('mark_notifications.php', { method: "POST" })
    .then(() => fetchNotifications());
});

// Close when clicking outside
document.addEventListener("click", function (event) {
  let panel = document.getElementById("notificationPanel");
  let button = document.getElementById("notif-btn");

  if (!panel.contains(event.target) && !button.contains(event.target)) {
    panel.classList.remove("active");
  }


});

// Format date
function formatDate(timestamp) {
  let date = new Date(timestamp);
  return date.toLocaleString("en-US", {
    day: "2-digit",
    month: "2-digit",
    year: "numeric",
    hour: "2-digit",
    minute: "2-digit",
    hour12: true
  });
}


// Fetch Notifications
function fetchNotifications() {
  let count = 0;
  fetch('fetch_notifications.php')
    .then(response => response.json())
    .then(data => {
      let notifList = document.getElementById("notif-list");
      notifList.innerHTML = ""; // Clear old notifications
      let notifCount = document.getElementById("notif-count");

      if (data.length > 0) {
        data.forEach(notif => {
          let notifItem = document.createElement("div");
          notifItem.className = "notif-item";
          if (notif.status === 'UNREAD') {
            notifItem.innerHTML = `<strong><i class="fa-solid fa-circle"></i>${notif.notice}</strong>
                                          <small>${formatDate(notif.created_at)}</small>`;
            count++;
          } else if (notif.status === 'READ') {
            notifItem.innerHTML = `<strong>${notif.notice}</strong>
                                          <small>${formatDate(notif.created_at)}</small>`;
          }

          notifList.appendChild(notifItem);
        });
      } else {
        notifList.innerHTML = "<p class='notif-empty'>No new notifications</p>";
      }
      if (count > 0) {
        notifCount.style.display = "inline-block"; // Show count
        notifCount.innerText = count;
      }
      else {
        notifCount.style.display = "none"; // Hide count if no notifications
      }
    });
}

// Load notifications on page load
document.addEventListener("DOMContentLoaded", fetchNotifications);