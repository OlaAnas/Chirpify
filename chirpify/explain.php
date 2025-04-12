// This file will explain how the Chirpify website works step by step, including PHP and MySQL terms.

// 1. **Database Connection**:
//    - The `config.php` file establishes a connection to the MySQL database using the `mysqli` class.
//    - The database stores all the data for the website, such as users, posts, likes, and comments.
//    - Example: `$conn = new mysqli($host, $user, $pass, $dbname);`
//      - `$host`: The server where the database is hosted (e.g., "localhost").
//      - `$user`: The username to access the database (e.g., "root").
//      - `$pass`: The password for the database user (empty by default in XAMPP).
//      - `$dbname`: The name of the database (e.g., "chirpify").

// 2. **User Authentication**:
//    - Users can register (`register.php`) and log in (`login.php`) to the website.
//    - Passwords are hashed using `password_hash()` for security before being stored in the database.
//    - During login, `password_verify()` is used to compare the entered password with the hashed password in the database.
//    - Sessions (`session_start()`) are used to track logged-in users. For example:
//      - `$_SESSION["user_id"]`: Stores the ID of the logged-in user.
//      - `$_SESSION["is_admin"]`: Indicates if the user is an admin.

// 3. **Posting Content**:
//    - Users can create posts with text and optional images (`upload_post.php`).
//    - Uploaded images are validated for type (e.g., JPEG, PNG) and stored in the `uploads/posts/` directory.
//    - Posts are saved in the `posts` table in the database with fields like `user_id`, `content`, and `image_path`.

// 4. **Liking Posts**:
//    - Users can like or unlike posts (`like_post.php`).
//    - The `likes` table in the database tracks which users liked which posts.
//    - A `SELECT` query checks if a user has already liked a post. If yes, the like is removed (`DELETE`), otherwise, it is added (`INSERT`).

// 5. **Commenting on Posts**:
//    - Users can add comments to posts (`comment.php`).
//    - Comments can include text and optional images.
//    - Comments are stored in the `comments` table with fields like `post_id`, `user_id`, `comment_text`, and `image_path`.

// 6. **Profile Management**:
//    - Users can edit their profile (`edit_profile.php`) and upload a profile picture (`upload_profile_picture.php`).
//    - Profile pictures are stored in the `uploads/profiles/` directory.
//    - The `users` table stores user details like `username`, `email`, and `profile_picture`.

// 7. **Admin Features**:
//    - Admin users can manage other users (`admin_dashboard.php`).
//    - Admins can delete users and their associated data (e.g., posts, comments, likes).
//    - The `is_admin` field in the `users` table determines if a user is an admin.

// 8. **Dashboard and Home Page**:
//    - The `dashboard.php` file displays all posts, allowing users to like, comment, or delete their own posts.
//    - The `home.php` file shows posts with like counts and the list of users who liked each post.

// 9. **Security Features**:
//    - Input validation: User inputs (e.g., email, passwords, file uploads) are validated to prevent invalid data.
//    - Prepared statements: SQL queries use `prepare()` and `bind_param()` to prevent SQL injection attacks.
//    - CSRF protection: Admin actions include a CSRF token to prevent unauthorized requests.

// 10. **Logout**:
//    - The `logout.php` file destroys the session (`session_destroy()`) to log the user out and redirects them to the homepage.

// 11. **Error Handling**:
//    - Errors (e.g., database connection issues, invalid inputs) are handled using `die()` or custom error messages.
//    - Example: `if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }`

// 12. **Dark Mode**:
//    - A JavaScript function toggles a "dark-mode" class on the `<body>` element to switch between light and dark themes.
//    - Example: `document.body.classList.toggle('dark-mode');`

// 13. **File Structure**:
//    - `config.php`: Contains database connection details.
//    - `register.php`, `login.php`: Handle user registration and login.
//    - `dashboard.php`, `home.php`: Display posts and user interactions.
//    - `upload_post.php`, `comment.php`: Handle post and comment submissions.
//    - `admin_dashboard.php`: Admin panel for managing users.
//    - `main.css`: Stylesheet for the website's design.
//    - `uploads/`: Directory for storing uploaded images (posts, profiles, comments).

// **Programming Terms and Their Purpose**:

// 1. **PHP**: 
//    - A server-side scripting language used to build dynamic web applications.
//    - Example: `<?php ... ?>` is used to embed PHP code within HTML.

// 2. **MySQL**:
//    - A relational database management system used to store and manage data.
//    - Example: The `chirpify` database stores users, posts, likes, and comments.

// 3. **mysqli**:
//    - A PHP extension used to interact with MySQL databases.
//    - Example: `$conn = new mysqli($host, $user, $pass, $dbname);` creates a connection to the database.

// 4. **Prepared Statements**:
//    - A feature of `mysqli` to execute SQL queries securely and prevent SQL injection.
//    - Example: `$stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");` prepares a query with placeholders.

// 5. **bind_param()**:
//    - Binds variables to the placeholders in a prepared statement.
//    - Example: `$stmt->bind_param("s", $email);` binds the `$email` variable to the `?` placeholder.

// 6. **execute()**:
//    - Executes a prepared statement.
//    - Example: `$stmt->execute();` runs the SQL query.

// 7. **store_result()**:
//    - Stores the result of a query in memory for further processing.
//    - Example: `$stmt->store_result();` is used to check the number of rows returned by a query.

// 8. **fetch()**:
//    - Retrieves the next row of a result set.
//    - Example: `$stmt->fetch();` fetches the data from the executed query.

// 9. **session_start()**:
//    - Starts a new session or resumes an existing one.
//    - Example: `session_start();` is used to manage user authentication and store session data.

// 10. **$_SESSION**:
//     - A superglobal array used to store session variables.
//     - Example: `$_SESSION["user_id"]` stores the ID of the logged-in user.

// 11. **password_hash()**:
//     - Hashes a password securely before storing it in the database.
//     - Example: `$hashed_password = password_hash($password_raw, PASSWORD_DEFAULT);` hashes the raw password.

// 12. **password_verify()**:
//     - Verifies a password against a hashed value.
//     - Example: `password_verify($password, $hashed_password);` checks if the entered password matches the stored hash.

// 13. **header()**:
//     - Sends a raw HTTP header to the browser.
//     - Example: `header("Location: dashboard.php");` redirects the user to the dashboard page.

// 14. **die()**:
//     - Terminates the script and optionally outputs a message.
//     - Example: `die("Connection failed: " . $conn->connect_error);` stops execution if the database connection fails.

// 15. **isset()**:
//     - Checks if a variable is set and is not null.
//     - Example: `if (isset($_POST['post_id'])) { ... }` checks if the `post_id` is provided in the POST request.

// 16. **empty()**:
//     - Checks if a variable is empty.
//     - Example: `if (empty($comment)) { ... }` ensures that a comment is provided before processing.

// 17. **trim()**:
//     - Removes whitespace from the beginning and end of a string.
//     - Example: `$email = trim($_POST["email"]);` sanitizes the email input.

// 18. **filter_var()**:
//     - Validates and sanitizes data.
//     - Example: `filter_var($post_id, FILTER_VALIDATE_INT);` ensures that the `post_id` is an integer.

// 19. **uniqid()**:
//     - Generates a unique identifier based on the current time in microseconds.
//     - Example: `$new_file_name = uniqid() . '-' . basename($file_name);` creates a unique file name for uploads.

// 20. **move_uploaded_file()**:
//     - Moves an uploaded file to a new location.
//     - Example: `move_uploaded_file($file_tmp, $file_path);` saves the uploaded file to the server.

// 21. **mkdir()**:
//     - Creates a new directory.
//     - Example: `mkdir($upload_dir, 0755, true);` creates the `uploads/posts/` directory if it doesn't exist.

// 22. **file_exists()**:
//     - Checks if a file or directory exists.
//     - Example: `if (file_exists('uploads/profiles/' . $user_details['profile_picture'])) { ... }` ensures the file exists before displaying it.

// 23. **mail()**:
//     - Sends an email.
//     - Example: `mail($email, "Password Reset Request", "Click the link to reset your password: $reset_link");` sends a password reset email.

// 24. **implode()**:
//     - Joins array elements into a string.
//     - Example: `implode(', ', $liked_users);` converts an array of usernames into a comma-separated string.

// 25. **JavaScript**:
//     - A client-side scripting language used for interactivity.
//     - Example: `function toggleDarkMode() { ... }` toggles the dark mode theme using JavaScript.

// 26. **CSS**:
//     - A stylesheet language used to style HTML elements.
//     - Example: `body { background-color: #f4f7f6; }` sets the background color of the page.

// 27. **HTML**:
//     - A markup language used to structure web pages.
//     - Example: `<form method="post" action="upload_post.php">` creates a form for submitting posts.

// 28. **Superglobals**:
//     - Predefined variables in PHP that are always accessible.
//     - Examples: `$_POST`, `$_GET`, `$_SESSION`, `$_SERVER`.

// 29. **CSRF Token**:
//     - A security measure to prevent Cross-Site Request Forgery attacks.
//     - Example: `$csrf_token = bin2hex(random_bytes(32));` generates a random token for secure form submissions.

// 30. **bin2hex()**:
//     - Converts binary data into a hexadecimal representation.
//     - Example: `bin2hex(random_bytes(32));` generates a secure random string.

// 31. **random_bytes()**:
//     - Generates cryptographically secure random bytes.
//     - Example: `random_bytes(32);` creates a secure random value for tokens.

// 32. **HTTP_REFERER**:
//     - A server variable that contains the URL of the referring page.
//     - Example: `header("Location: " . $_SERVER["HTTP_REFERER"]);` redirects the user back to the previous page.

// This list provides a detailed explanation of the programming terms used in the Chirpify website.

// This concludes the explanation of how the Chirpify website works step by step.



