<?php
if (isset($_GET['image'])) {
    $imagePath = $_GET['image'];

    // Ensure security by limiting deletion to a specific directory
    $allowedDirectory = "uploads/";
    if (strpos(realpath($imagePath), realpath($allowedDirectory)) === 0) {
        if (file_exists($imagePath)) {
            unlink($imagePath);
            echo "Deleted successfully.";
        } else {
            echo "File not found.";
        }
    } else {
        echo "Unauthorized deletion attempt.";
    }
} else {
    echo "No image specified.";
}
?>
