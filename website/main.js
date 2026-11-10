document.addEventListener("DOMContentLoaded", () => {
    const toggle = document.querySelector(".nav-toggle");
    const navItems = document.querySelector(".nav-items");
    if (toggle && navItems) {
        toggle.addEventListener('click', () => {
            navItems.classList.toggle("active");
            
            // ADDED: Toggle the body class required by your layout CSS
            document.body.classList.toggle("nav-menu-open");
            
            const isExpanded = toggle.getAttribute("aria-expanded") === "true";
            toggle.setAttribute("aria-expanded", !isExpanded);
            toggle.textContent = navItems.classList.contains("active") ? "X" : "☰";
        });

        document.querySelectorAll(".nav-items a").forEach(link => {
            link.addEventListener("click", () => {
                navItems.classList.remove("active");
                
                // ADDED: Remove the body class when a link is clicked
                document.body.classList.remove("nav-menu-open");
                
                toggle.textContent = "☰";
                toggle.setAttribute("aria-expanded", "false");
            });
        });
    }

    const dashboardBtn = document.getElementById('dashboardBtn');
    const dropdownMenu = document.getElementById('dropdownMenu');

    if (dashboardBtn && dropdownMenu) {
        dashboardBtn.addEventListener('click', (event) => {
            dropdownMenu.classList.toggle('show');
            event.stopPropagation();
        });

        window.addEventListener('click', (event) => {
            if (!dropdownMenu.contains(event.target) && dropdownMenu.classList.contains('show')) {
                dropdownMenu.classList.remove('show');
            }
        });
    }
    const imageUploader = document.getElementById('imageUploader');
    if (imageUploader) {
        imageUploader.addEventListener('change', function (e) {
            const file = e.target.files[0];
            if (!file) return;

            const formData = new FormData();
            formData.append('profile_image', file);

            fetch('upload.php', {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.querySelectorAll('.profile-preview').forEach(img => {
                            img.src = data.filepath + '?t=' + new Date().getTime();
                        });
                        alert('Profile picture updated successfully for this session!');
                    } else {
                        alert('Upload failed: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        });
    }
});