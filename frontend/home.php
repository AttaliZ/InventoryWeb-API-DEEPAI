<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - KateBakery</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
        }

        .header {
            background-color: #ff69b4;
            padding: 20px;
            text-align: center;
            color: white;
            font-size: 24px;
        }

        .menu {
            display: flex;
            justify-content: center;
            background-color: white;
            border-bottom: 2px solid #ddd;
            padding: 10px;
        }

        .menu a {
            text-decoration: none;
            color: #333;
            font-weight: bold;
            padding: 10px 20px;
            transition: background-color 0.3s;
            border-radius: 5px;
        }

        .menu a:hover {
            background-color: #ffc0cb;
        }

        .container {
            text-align: center;
            padding: 40px 20px;
        }

        .btn {
            background-color: #ff69b4;
            color: white;
            padding: 15px 30px;
            font-size: 18px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
            text-decoration: none;
        }

        .btn:hover {
            background-color: #ff85c0;
        }

        .ai-section {
            margin-top: 40px;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }

        input[type="text"] {
            width: 80%;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-bottom: 10px;
        }

        .ai-image {
            margin-top: 20px;
            max-width: 100%;
            height: auto;
            border-radius: 10px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        }

        .download-btn {
            margin-top: 10px;
            padding: 10px;
            background: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            display: inline-block;
        }

        .download-btn:hover {
            background: #45a049;
        }
    </style>
</head>

<body>
    <div class="header">
        <?php echo "Welcome to KateBakery"; ?>
    </div>

    <div class="menu">
        <a href="home.php">Home</a>
        <a href="add_product.php">Add Cake</a>
        <a href="show_cakes.php">Show Cakes</a>
        <a href="edit_product.php">Edit Cake</a>
        <a href="delete_product.php">Delete</a>
        <a href="search_product.php">Search</a>
    </div>

    <div class="container">
        <h1>Welcome to KateBakery</h1>
        <p>Your one-stop shop for the most delicious cakes in town!</p>
        <a href="show_cakes.php" class="btn">View Our Cakes</a>
    </div>

    <div class="container ai-section">
    <h2>🎨 Design Your Own Cake with AI 🎂</h2>
    <p>Enter a cake description, and our AI will generate an image for you!</p>

    <input type="text" id="cake_description" placeholder="e.g., Chocolate cake with strawberries">
    <button class="btn" onclick="generateCake()">Generate Cake</button>

    <div id="cake_result"></div>
</div>

<script>
    function generateCake() {
        var description = document.getElementById("cake_description").value;
        if (description.trim() === "") {
            alert("Please enter a cake description.");
            return;
        }

        document.getElementById("cake_result").innerHTML = "<p>Generating cake... 🎂 Please wait.</p>";

        fetch("generate_cake.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: "description=" + encodeURIComponent(description)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById("cake_result").innerHTML = `
                    <img src="${data.image_url}" class="ai-image">
                    <p><strong>Your AI-generated cake!</strong></p>
                    <a href="${data.saved_path}" class="download-btn" download="AI_Cake.jpg">Download Image</a>
                    <button class="btn" onclick="addToDatabase('${data.saved_path}')">Add to Database</button>
                `;
            } else {
                document.getElementById("cake_result").innerHTML = `<p>Error: ${data.message}</p>`;
            }
        })
        .catch(error => {
            document.getElementById("cake_result").innerHTML = "<p>Error generating cake.</p>";
        });
    }

    function addToDatabase(imagePath) {
        fetch("add_cake.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: "image=" + encodeURIComponent(imagePath)
        })
        .then(response => response.json())
        .then(data => {
            alert(data.message);
        })
        .catch(error => {
            alert("Error adding cake to database.");
        });
    }
</script>
</body>

</html>
