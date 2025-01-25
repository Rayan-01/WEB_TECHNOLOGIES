<?php

/* This is the main page of the library management system. It contains the list of all books, the form to add or remove a book, the form to edit a book, and the form to borrow a book. */ 
?>


<?php

$photos = [
    "Photos/P1.jpg",
    "Photos/P2.jpg",
    "Photos/P3.jpg"
];

// Get the current photo index from the URL or default to 0
$currentPhotoIndex = isset($_GET['index']) ? (int)$_GET['index'] : 0;

// Ensure the index stays within bounds
$currentPhotoIndex = ($currentPhotoIndex + count($photos)) % count($photos);

// Determine the next and previous photo indices
$prevIndex = ($currentPhotoIndex - 1 + count($photos)) % count($photos);
$nextIndex = ($currentPhotoIndex + 1) % count($photos);




//SQL query to select all books from the database
$conn = new mysqli("localhost", "root", "", "library_management");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$sql = "SELECT id, title, author, yearofpublication, genre FROM books";
$result = $conn->query($sql);

$books = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $books[] = $row;
    }
}
$conn->close();

$tokens = [];
if (file_exists('token.json')) {
    $json = file_get_contents('token.json');
    $tokens = json_decode($json, true);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="style.css">
    <title>Library Management</title>
    
<!-- Token Selection and Used Token-->
    <script>
    function selectToken(token) {
        document.getElementById('token').value = token;
        const tokenButton = document.getElementById('token-' + token);
        tokenButton.style.display = 'none';

        const usedTokenList = document.getElementById('used-token-list');
        const newUsedToken = document.createElement('li');
        newUsedToken.textContent = token;
        usedTokenList.appendChild(newUsedToken);

        let usedTokens = JSON.parse(localStorage.getItem('usedTokens')) || [];
        usedTokens.push(token);
        localStorage.setItem('usedTokens', JSON.stringify(usedTokens));
        
    }

    document.addEventListener('DOMContentLoaded', function() {
        let usedTokens = JSON.parse(localStorage.getItem('usedTokens')) || [];
        const usedTokenList = document.getElementById('used-token-list');
        usedTokens.forEach(function(token) {
            const newUsedToken = document.createElement('li');
            newUsedToken.textContent = token;
            usedTokenList.appendChild(newUsedToken);
        });
    });
</script>
</head>
<body>
    <main>
        <!-- Used Token section -->
        <aside class="box3">
            <h2>Token Used</h2> <hr>
            <ul class="token-list" id="used-token-list">
            </ul>
        </aside>
        <div>
           
            <section>
                
                <!-- Add -->
                <div class="box1">
                    <h2 style="text-align: center; border: 2px aliceblue solid; background-color:silver; color:black;">Add Book</h2>
                    <form action="add_book.php" method="post">
                        <input type="text" name="title" placeholder="Book Title" required>
                        <input type="text" name="author" placeholder="Author" required>
                        <input type="number" name="yearofpublication" placeholder="Year of Publication" required>
                        <input type="text" name="genre" placeholder="Genre" required>
                        <button type="submit" name="action" value="add" id="buttonAdd" style ="background-color: cornflowerblue; border: none; font-size: 15px;"><b>Add</b></button>
                    </form>
                </div>


        <div class="box1">
            <?php
                include('search_remove.php');
              ?>


                <h2 style="text-align: center; border: 2px aliceblue solid; background-color:Silver; color:black;">Book List</h2>
    
            <!-- Search bar to filter books -->
            <form method="POST">
                <input type="text" name="search" placeholder="Search Books" style="margin: 10px; padding: 5px; width: 50%;">
                <button type="submit" name="action" value="search" style="padding: 5px 10px; background-color: cornflowerblue; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 15px;">Search</button>
            </form>

            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Author</th>
                        <th>Year of Publication</th>
                        <th>Genre</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Display the books
                    displayBooks($searchTerm);
                    ?>
                </tbody>
            </table>
         </div>

                <!-- Edit Book Info -->
                <div class="box1">
                    <h2 style="text-align: center; border: 2px aliceblue solid; background-color:silver; color:black;">Edit Book Information</h2>
                    <form action="edit_book.php" method="post">
                        <input type="number" name="id" placeholder="Book ID" required>
                        <input type="text" name="title" placeholder="New Title">
                        <input type="text" name="author" placeholder="New Author">
                        <input type="number" name="yearofpublication" placeholder="New Year of Publication">
                        <input type="text" name="genre" placeholder="New Genre">
                        <button type="submit" id="buttonUpdate"><b>Update</b></button>
                    </form>
                </div>
            </section>
            <!-- About Library -->
            <section class="section2">
                <div class="box2">
                <img src="Photos/im3.jpg" alt="description">
                <p>The hill stood tall against the horizon, its gentle slopes a quiet reminder of nature's enduring strength.</p>
                
                </div>
                
                <div class="box2">
                    
                <img src="Photos/im2.jpg" alt="description">
                <p>The sea is a timeless expanse, its waves whispering secrets of the deep and its horizon promising endless adventure.</p>
                </div>

                <div class="box2">
                <img src="Photos/im1.jpg" alt="description">
                <p>The sky is a vast canvas, constantly shifting in color and mood, from the soft hues of dawn to the deep blues of night.</p>
                </div>
            </section>
            <!-- Borrow Book -->
            <section class="section2">
                <div class="box22a">
                    <form action="process.php" method="post">
                        <b style="color: white;">Student Name</b> 
                        <br><input type="text" placeholder="Student Full Name" name="studentname" id="studentname" required><br>
                        <b style="color: white;">Student ID</b>
                        <br><input type="text" placeholder="Student ID" name="studentid" id="studentID" required><br>
                        <b style="color: white;">Student Email</b>
                        <br><input type="email" placeholder="Student Email" name="email" id="email" required><br>
                        <label for="booktitle"><b style="color: white;">Choose A Book Title: </b></label><br>
                        <select name="booktitle" id="booktitle" required>
                            <option value="Select a Book" disabled selected>Select a Book</option>
                            <option value="A Practical Handbook of Software Construction">A Practical Handbook of Software Construction</option>
                            <option value="Your Journey to Mastery">Your Journey to Mastery</option>
                            <option value="Elements of Reusable Object-Oriented Software">Elements of Reusable Object-Oriented Software</option>
                        </select><br>
                        <b style="color: white;">Borrow date</b>
                        <br><input type="date" name="borrowdate" id="borrowdate" required><br>
                        <b style="color: white;">Return date</b>
                        <br><input type="date" name="returndate" id="returndate" required><br>
                        <b style="color: white;">Token</b>
                        <br><input type=number placeholder="Choose from Availabe Tokens" name="token" id="token" style="background-color: white;"><br>
                        <b style="color: white;">Fees</b>
                        <br><input type=number placeholder="Fees" name="fees" id="fees" required><br> <br><br>
                        <button type="submit" name="submit" id="button"><b>Borrow</b></button>
                    </form>
                </div>

                <!-- Available Token Picking -->
                <?php
                if (file_exists('token.json')) {
                    $tokens_json = file_get_contents('token.json');
                    $tokens = json_decode($tokens_json, true);
                    if ($tokens === null) {
                        echo "Error decoding JSON.";
                    }
                } else {
                    echo "token.json file not found.";
                }
                ?>

                <div class="box22b">
                    <h3 style="text-align: center;color:white;">Available Tokens</h3>
                    <ul>
                        <?php if (isset($tokens) && is_array($tokens)): ?>
                            <?php foreach ($tokens as $token): ?>
                                <?php if (isset($token['token'])): ?>
                                    <button id="token-<?php echo $token['token']; ?>" style="background-color:silver; color:black; padding:10px; margin:10px; width:75%;" onclick="selectToken('<?php echo $token['token']; ?>')">
                                        <strong><?php echo $token['token']; ?></strong>
                                    </button><br>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p>No tokens available or an error occurred while loading the tokens.</p>
                        <?php endif; ?>
                    </ul>
                </div>
            </section>
        </div>
        <!-- Library Details -->
        <div class="box3">

                <h2 style="text-align: center;">Library Details</h2> 
                <div id="gallery" class="gallery">
                    <div class="photo">
                        <img src="<?php echo $photos[$currentPhotoIndex]; ?>" alt="Photo">
                    </div>
                </div>

                <div class="buttons">
                    <!-- Previous Button -->
                    <a href="?index=<?php echo $prevIndex; ?>#gallery" class="button1" id="prev-btn"></a>

                    <!-- Next Button -->
                
                    <a href="?index=<?php echo $nextIndex; ?>#gallery" class="button1" id="next-btn"></a>
                    
                </div>
            <div>
                    <hr>

                    <Label><b>About</b></Label>
                    <p>The AIUB Library, established in 1994, supports the academic and research needs of faculty, students, and staff. It has grown significantly, offering a rich collection of over 43,318 books, 1,72,000 e-books, 68,000 e-journals, and resources in various fields like Business, Science, Technology, and Social Sciences. The library operates an open system for AIUB students, allowing book and CD borrowing (excluding textbooks) for seven days using their student ID cards. With a seating capacity for 500+, it uses the "AIUB Library System," a software developed in-house, providing modern facilities for efficient library access and management.</p>

                    <hr>

                <div class="social-icons">
                <!-- Facebook Icon -->
                <a href="https://www.facebook.com/aiub.edu" target="_blank">
                    <i class="fab fa-facebook"></i>
                </a>
                
                <!-- LinkedIn Icon -->
                <a href="https://www.linkedin.com/school/aiubedu/" target="_blank">
                    <i class="fab fa-linkedin"></i>
                </a> </div>
        </div>

    </main>
</body>
</html>