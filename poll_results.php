<?php
// database connection
$conn = MySQLi_Connect('localhost','wjone164','wjone164','wjone164_db');
if(MySQLi_Connect_Errno()) {
    echo "<tr align='center'> <td colspan='5'> Failed to connect to MySQL </td> </tr>";
} else {
    // get vote results
    $query = "SELECT character_name, COUNT(*) as votes FROM votes GROUP BY character_name ORDER BY votes DESC";
    $result = $conn->query($query);
    
    // calculate total votes
    $total_votes_query = "SELECT COUNT(*) as total FROM votes";
    $total_result = $conn->query($total_votes_query);
    $total_row = $total_result->fetch_assoc();
    $total_votes = $total_row['total'];
    
    // store results in array for display
    $vote_results = [];
    if($result && $result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $vote_results[] = $row;
        }
    }
    
    // characters array  to make sure to show even those with zero votes
    $all_characters = ['Aang', 'Katara', 'Sokka', 'Toph', 'Zuko', 'Iroh'];
    
    // Add any missing characters with zero votes
    $existing_characters = array_column($vote_results, 'character_name');
    foreach($all_characters as $character) {
        if(!in_array($character, $existing_characters)) {
            $vote_results[] = [
                'character_name' => $character,
                'votes' => 0
            ];
        }
    }
    
    // sort  votes (descending)
    usort($vote_results, function($a, $b) {
        return $b['votes'] - $a['votes'];
    });
    
    // get  most recent vote timestamp
    $recent_vote_query = "SELECT vote_time FROM votes ORDER BY vote_time DESC LIMIT 1";
    $recent_result = $conn->query($recent_vote_query);
    $most_recent_vote = ($recent_result && $recent_result->num_rows > 0) ? 
                        $recent_result->fetch_assoc()['vote_time'] : 'No votes yet';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Poll Results - Avatar Wiki</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
    <link href="styles.css" rel="stylesheet">
</head>
<body class="body">
    <div class="container">
        <div class="header">
            <h1>Poll Results</h1>
            <hr>
            <p>Current standings for favorite Avatar character</p>
            <?php if(isset($_GET['voted']) && $_GET['voted'] == 1): ?>
                <div class="alert alert-success">Thank you for voting!</div>
            <?php endif; ?>
            <?php if(isset($most_recent_vote) && $most_recent_vote !== 'No votes yet'): ?>
                <p class="last-vote">Last vote: <?php echo date('F j, Y, g:i a', strtotime($most_recent_vote)); ?></p>
            <?php endif; ?>
        </div>
        
        <div class="results-container">
            <?php
            if(isset($vote_results) && !empty($vote_results)) {
                echo '<div class="total-votes">Total votes: ' . $total_votes . '</div>';
                
                foreach($vote_results as $row) {
                    $percentage = ($total_votes > 0) ? round(($row['votes'] / $total_votes) * 100) : 0;
                    
                    echo '<div class="result-item">';
                    echo '<div class="result-name">' . $row['character_name'] . '</div>';
                    echo '<div class="result-bar-container">';
                    echo '<div class="result-bar" style="width: ' . $percentage . '%"></div>';
                    echo '</div>';
                    echo '<div class="result-percentage">' . $percentage . '%</div>';
                    echo '<div class="result-votes">(' . $row['votes'] . ' votes)</div>';
                    echo '</div>';
                }
            } else {
                echo '<p>No voting data available.</p>';
            }
            ?>
        </div>
        
        <a href="poll.php" class="vote-link"><i class="bi bi-arrow-left-circle"></i> Back to Voting</a>
        <a href="index.html" class="home-link"><i class="bi bi-house-fill"></i> Back to Character Wiki</a>
    </div>
</body>
</html>