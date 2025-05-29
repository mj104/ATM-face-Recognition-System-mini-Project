<?php
session_start();
if (!isset($_GET["original"]) || !isset($_GET["captured"])) {
    die("Invalid access!");
}

$original = $_GET["original"];
$captured = $_GET["captured"];

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Face Verification</title>
    <script defer src="https://cdn.jsdelivr.net/npm/@vladmandic/face-api@latest/dist/face-api.min.js"></script>
    <script>
        async function verifyFace() {
            await faceapi.nets.ssdMobilenetv1.loadFromUri("models");
            await faceapi.nets.faceLandmark68Net.loadFromUri("models");
            await faceapi.nets.faceRecognitionNet.loadFromUri("models");

            const img1 = await faceapi.fetchImage("<?php echo $original; ?>");
            const img2 = await faceapi.fetchImage("<?php echo $captured; ?>");

            const face1 = await faceapi.detectSingleFace(img1).withFaceLandmarks().withFaceDescriptor();
            const face2 = await faceapi.detectSingleFace(img2).withFaceLandmarks().withFaceDescriptor();

            if (!face1 || !face2) {
                session_unset();
                session_destroy();
                alert("❌ Face not detected! Try Again.");
                deleteTempImage("<?php echo $captured; ?>");
                window.location.href = "login.php";
                return;
            }
 
            const desc1 = face1.descriptor;
            const desc2 = face2.descriptor;
            const distance = faceapi.euclideanDistance(desc1, desc2);

            console.log("Face Match Distance:", distance);

            if (distance < 0.5) { // **More strict matching threshold**
                alert("✅ Face Matched! Login Successful.");
                deleteTempImage("<?php echo $captured; ?>");
                window.location.href = "dashboard.php";
            } else {
               
                alert("❌ Face Mismatch! Access Denied.");
                deleteTempImage("<?php echo $captured; ?>");
                window.location.href = "login.php";
                session_unset();
                session_destroy();
            }
        }

        function deleteTempImage(imagePath) {
            fetch("delete_temp.php?image=" + encodeURIComponent(imagePath))
                .then(response => console.log("Temporary image deleted:", imagePath))
                .catch(error => console.error("Error deleting image:", error));
        }

        window.onload = verifyFace;
    </script>
</head>

<body>
    <h2>Verifying Face...</h2>
</body>

</html>