document.addEventListener('DOMContentLoaded', () => {
    // -------------------------------------------------------------------------
    // 1. UNIQUE USER SESSION SCOPING & STRUCTURAL LOCATORS
    // -------------------------------------------------------------------------
    function getCurrentUserKey() {
        const welcomeEl = document.querySelector('.profile-container strong') || document.querySelector('.info-group h1');
        if (welcomeEl) {
            const text = welcomeEl.textContent || '';
            const match = text.match(/Welcome,\s*([^!]+)/) || text.match(/Welcome back,\s*([^!]+)/);
            if (match) return match.trim();
        }
        return 'generic_user';
    }

    const userScope = getCurrentUserKey();

    // DOM Elements linked to the visual file upload interface
    const serviceImageUploader = document.getElementById('serviceImageUploader');
    const userUploadedGrid = document.getElementById('userUploadedGrid');
    const userUploadsSection = document.getElementById('userUploadsSection');

    // -------------------------------------------------------------------------
    // 2. IMAGE GRAPHICS RENDERING & ASYNCHRONOUS BACKEND UPLOADS (GLOBAL COOP)
    // -------------------------------------------------------------------------
    
    // Helper function to build and mount an upload card directly onto the community display container
    function createGalleryCard(imageSrc, uploaderName = 'You') {
        if (!userUploadedGrid) return;
        
        const card = document.createElement('div');
        card.className = 'uploaded-card';
        card.innerHTML = `
            <img src="${imageSrc}" alt="User Masterpiece">
            <p>By: ${uploaderName} (Just now)</p>
        `;
        
        // Inserts the new image at the beginning of the grid so the newest work shows first
        if (userUploadedGrid.firstChild) {
            userUploadedGrid.insertBefore(card, userUploadedGrid.firstChild);
        } else {
            userUploadedGrid.appendChild(card);
        }
        
        if (userUploadsSection) {
            userUploadsSection.style.display = 'block';
        }
    }

    // Monitor file selection triggers dispatched from the Navigation panel button
    if (serviceImageUploader) {
        serviceImageUploader.addEventListener('change', function (event) {
            const files = event.target.files;
            if (!files || files.length === 0) return;

            const formData = new FormData();
            Array.from(files).forEach((file) => {
                formData.append('files[]', file);
            });

            // Stream file binaries safely to the remote backend folder engine
            fetch('supload.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success' || data.status === 'partial_error') {
                    // Render each successfully uploaded image into the public view area instantly
                    data.images.forEach(path => {
                        createGalleryCard(path, 'You');
                    });
                }
                if (data.errors && data.errors.length > 0) {
                    console.error("Upload process notifications:", data.errors);
                }
            })
            .catch(err => console.error("Communication error dispatched to upload terminal:", err));
        });
    }    
});
