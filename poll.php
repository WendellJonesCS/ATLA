<?php
// define the characters
$characters = ['Aang', 'Katara', 'Sokka', 'Toph', 'Zuko', 'Iroh'];

// database connection
$conn = MySQLi_Connect('localhost','wjone164','wjone164','wjone164_db');
if(MySQLi_Connect_Errno()) {
    $error_message = "Unable to connect to database. Please try again later.";
} else {
    // process vote submission
    if(isset($_POST['vote']) && isset($_POST['character'])) {
        $character_name = $_POST['character'];
        
        // validating character is in our list
        if(in_array($character_name, $characters)) {
            // Insert vote into database
            $insert_query = "INSERT INTO votes (character_name) VALUES (?)";
            $stmt = $conn->prepare($insert_query);
            $stmt->bind_param("s", $character_name);
            
            if($stmt->execute()) {
                // Redirect to results page
                header("Location: poll_results.php?voted=1");
                exit();
            } else {
                $error_message = "Error recording your vote. Please try again.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Character Poll - Avatar Wiki</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
    <link href="styles.css" rel="stylesheet">
    <style>
        .vote-character-image {
            width: 200px;
            height: 200px;
            border-radius: 50%;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .vote-character-image:hover {
            transform: scale(1.05);
            box-shadow: 0 0 15px rgba(252, 220, 123, 0.8);
            cursor: pointer;
        }
    </style>
</head>
<body class="body">
    <div class="container">
        <div class="header">
            <h1>Character Poll</h1>
            <hr>
            <p>Vote for your favorite Avatar character!</p>
        </div>
        
        <div class="poll-container">
            <form method="post" action="poll.php">
                <div class="poll-options">
                    <?php if(isset($error_message)): ?>
                        <div class="alert alert-danger"><?php echo $error_message; ?></div>
                    <?php endif; ?>
                    
                    <div class="character-grid">
                        <?php
                        if(isset($characters) && !empty($characters)) {
                            foreach($characters as $index => $character) {
                                $image_file = strtolower($character) . '.jpg';
                                echo '<div class="character-vote-card">';
                                echo '<input type="radio" name="character" id="char'.$index.'" value="'.$character.'" required>';
                                echo '<label for="char'.$index.'">';
                                echo '<img src="'.$image_file.'" alt="'.$character.'" class="vote-character-image">';
                                echo '<span class="character-name">'.$character.'</span>';
                                echo '</label>';
                                echo '</div>';
                            }
                        } else {
                            echo '<p>No characters available for voting.</p>';
                        }
                        ?>
                    </div>
                </div>
                
                <button type="submit" name="vote" class="vote-button">
                    <i class="bi bi-check-circle-fill"></i> Submit Your Vote
                </button>
            </form>
        </div>
        
        <a href="poll_results.php" class="results-link"><i class="bi bi-bar-chart-fill"></i> View Results</a>
        <a href="index.html" class="home-link"><i class="bi bi-house-fill"></i> Back to Character Wiki</a>
    </div>
</body>
</html>