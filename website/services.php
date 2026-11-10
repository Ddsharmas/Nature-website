<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');

// 1. Check if the user is logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

require_once "db.php";

$profile_pic = "https://ssc.gov.in/candidate-portal/assets/image/Profile_White.svg";
$all_uploaded_images = []; // Clear definition for scoping

try {
    // 2. Fetch details of the logged-in user
    $stmt = $pdo->prepare("SELECT profile_pic FROM users WHERE id = :id");
    $stmt->execute(['id' => $_SESSION['id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        session_destroy();
        header("Location: login.php");
        exit();
    }

    if (!empty($user['profile_pic']) && file_exists($user['profile_pic'])) {
        $profile_pic = $user['profile_pic'];
    }

    // 3. Query the database for the permanent user masterpieces
    $uploadStmt = $pdo->prepare("SELECT u.file_path, us.username
        FROM user_uploads u
        JOIN users us ON u.user_id = us.id
        ORDER BY u.id DESC
    ");

    $uploadStmt->execute();
    $all_uploaded_images = $uploadStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Services</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="dashboard.css">
</head>

<body>
    <header>
        <h1>Weclome To The Service Page.</h1>
    </header>
    <nav class="navbar">
        <button class="nav-toggle" aria-label="Toggle navigation" aria-expanded="false">☰</button>
        <ul class="nav-items">
            <li><a href="home.php">Home</a></li>
            <li><a href="services.php">Services</a></li>
            <li><a href="contact.php">Contact</a></li>
            <li>
                <label for="serviceImageUploader" class="nav-upload-btn">📤 Upload Masterpiece</label>
                <input type="file" id="serviceImageUploader" class="hidden-uploader" accept="image/*" multiple>
            </li>
            <li class="dashboard-card">
                <a href="dashboard.php">
                    <img class="profile profile-preview" id="dashboardVisual" src="<?php echo htmlspecialchars($profile_pic); ?>" alt="Profile Picture">
                </a>
            </li>
        </ul>
    </nav>
    <div class="page-content-wrapper">
        <div class="container">
            <main>
                <!-- FIXED: Changed variable to $all_uploaded_images to accurately display database rows -->
                <div id="userUploadsSection" style="<?php echo empty($all_uploaded_images) ? 'display: none;' : 'display: block;'; ?>">
                    <h2>Users Uploaded Masterpieces</h2>
                    <div id="userUploadedGrid" class="user-uploaded-grid">
                        <?php if (!empty($all_uploaded_images)): ?>
                            <?php foreach ($all_uploaded_images as $index => $upload): ?>
                                <div class="uploaded-card">
                                    <img src="<?php echo htmlspecialchars($upload['file_path']); ?>" alt="User Masterpiece">
                                    <p>Uploaded by: <?php echo htmlspecialchars($upload['username']); ?></p>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    <hr class="section-divider">
                </div>

                <!-- 25 DEFAULT PLACEHOLDER IMAGES -->
                <div class="nature-grid">
                    <h2>Our Services</h2>
                    <p>We provide the best Nature photography and related services.</p>
                    <div class="image-container" id="imageGallery">
                        <img src="https://media.istockphoto.com/id/2166282428/photo/a-beautiful-and-lush-green-forest-canopy-illuminated-by-warm-sunlight-streaming-through.jpg?s=1024x1024&w=is&k=20&c=qITYAeDOloxytWkjXvdsJ7xnTwOCfo9shjwuc-ZKgmc="
                            alt="Nature Image"></img>
                        <img src="https://i0.wp.com/picjumbo.com/wp-content/uploads/beautiful-fall-nature-scenery-free-image.jpeg?w=600&quality=80"
                            alt="Nature Image"></img>
                        <img src="https://i0.wp.com/picjumbo.com/wp-content/uploads/beautiful-nature-mountain-scenery-with-flowers-free-photo.jpg?w=1500&quality=50"
                            alt="Nature Image"></img>
                        <img src="https://as1.ftcdn.net/jpg/00/60/42/60/220_F_60426024_Eogr3qTbpBWnfZGFICNPYOn8d1XyYZNc.jpg"
                            alt="Nature Image"></img>
                        <img src="https://as1.ftcdn.net/jpg/03/08/47/94/220_F_308479436_f7oH1ssJPwogl2dtLdLuFqOo5Y6RJQ4p.jpg"
                            alt="Nature Image"></img>
                        <img src="https://as2.ftcdn.net/jpg/02/15/83/37/220_F_215833754_CUFijxKZxO17foPa9w52lbW3qfrQq5Mt.jpg"
                            alt="Nature Image"></img>
                        <img src="https://image.shutterstock.com/image-photo/misty-mountain-landscape-mysterious-fir-450w-2510695537.jpg"
                            alt="Nature Image"></img>
                        <img src="https://image.shutterstock.com/image-photo/ocean-water-background-foam-splash-450w-2460168659.jpg"
                            alt="Nature Image"></img>
                        <img src="https://image.shutterstock.com/image-photo/mysterious-landscape-milford-sound-rahotu-450w-2486135973.jpg"
                            alt="Nature Image"></img>
                        <img src="https://image.shutterstock.com/image-photo/autumn-grass-mountains-sunset-macro-450w-2656223717.jpg"
                            alt="Nature Image"></img>
                        <img src="https://image.shutterstock.com/image-photo/beautiful-view-emerald-lake-snow-450w-2358718721.jpg"
                            alt="Nature Image"></img>
                        <img src="https://image.shutterstock.com/image-photo/beautiful-landscape-pacific-ocean-coast-450w-2515576389.jpg"
                            alt="Nature Image"></img>
                        <img src="https://cdn.stocksnap.io/img-thumbs/280h/flowers-garden_AVBDMZXLRA.jpg"
                            alt="Nature Image"></img>
                        <img src="https://cdn.stocksnap.io/img-thumbs/280h/purple-flowers_TRQWSOGMI5.jpg"
                            alt="Nature Image"></img>
                        <img src="https://cdn.stocksnap.io/img-thumbs/280h/footprints-sand_GBUFTJIRSS.jpg"
                            alt="Nature Image"></img>
                        <img src="https://cdn.stocksnap.io/img-thumbs/280h/butterflies-nature_IFVYIKNJC1.jpg"
                            alt="Nature Image"></img>
                        <img src="https://cdn.stocksnap.io/img-thumbs/280h/winter-forest_0W4T8YHEM4.jpg"
                            alt="Nature Image"></img>
                        <img src="https://cdn.stocksnap.io/img-thumbs/280h/mountains-clouds_A4Y2Q66EL4.jpg"
                            alt="Nature Image"></img>
                        <img src="https://cdn.stocksnap.io/img-thumbs/280h/lake-mountains_VZBJUVPO25.jpg"
                            alt="Nature Image"></img>
                        <img src="https://cdn.stocksnap.io/img-thumbs/280h/calm-sea_8V46UZCS0V.jpg"
                            alt="Nature Image"></img>
                        <img src="https://images.unsplash.com/photo-1751225750479-43ad27b94fa0?w=500&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8Mnx8bmF0dXJlfGVufDB8fDB8fHww"
                            alt="Nature Image"></img>
                        <img src="https://plus.unsplash.com/premium_photo-1671805322278-13793483b464?w=500&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8NXx8bmF0dXJlfGVufDB8fDB8fHww"
                            alt="Nature Image"></img>
                        <img src="https://images.unsplash.com/photo-1472396961693-142e6e269027?w=500&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8Nnx8bmF0dXJlfGVufDB8fDB8fHww"
                            alt="Nature Image"></img>
                        <img src="https://plus.unsplash.com/premium_photo-1720706435477-bb1d79c2224c?w=500&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8OXx8bmF0dXJlfGVufDB8fDB8fHww"
                            alt="Nature Image"></img>
                        <img src="https://images.unsplash.com/photo-1472214103451-9374bd1c798e?w=500&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MTR8fG5hdHVyZXxlbnwwfHwwfHx8MA%3D%3D"
                            alt="Nature Image"></img>
                    </div>
                    <footer>
                        <p>&copy; 2026 My Website. All rights reserved.</p>
                    </footer>
                </div>
            </main>
        </div>
    </div>
    <div id="currentTitle"></div>
    <ul id="historyList" class="history-list"></ul>
    <script src="main.js"></script>
    <script src="dashboard.js"></script>
    <script src="history.js"></script>
</body>

</html>