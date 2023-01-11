<?php
    if (!isset($_POST['recipe_name']) || trim($_POST['recipe_name']) == '' ||
    !isset($_POST['author_id']) || trim($_POST['author_id']) == '' ||
    !isset($_POST['food_type_id']) || trim($_POST['food_type_id']) == '' ||
    !isset($_POST['ingredientsCount']) || trim($_POST['ingredientsCount']) == '' ||
    !isset($_POST['methodsCount']) || trim($_POST['methodsCount']) == '') {
        $error = "You must fill out all required parts of the form.";
    } else {
        $recipe_id = trim($_POST['recipe_id']);
        $author_id = trim($_POST['author_id']);
        $food_type_id = trim($_POST['food_type_id']);

        if ($author_id == 0 && (!isset($_POST['author_name']) || trim($_POST['author_name']) == '')) {
            $error = "You must specify a name for the author.";
        } else {
            require 'config/config.php';
            $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
            if ( $mysqli->connect_errno ) {
                echo $mysqli->connect_error;
                exit();
            }
            $mysqli->set_charset('utf8');

            $recipe_name = $mysqli->real_escape_string(trim($_POST['recipe_name']));

            if ($author_id == 0 && isset($_POST['author_name']) && trim($_POST['author_name']) != '') {
                $author_name = $mysqli->real_escape_string(trim($_POST['author_name']));
            } else {
                $author_name = null;
            }

            if (isset($_POST['recipe_url']) && trim($_POST['recipe_url']) != '') {
                $recipe_url = $mysqli->real_escape_string(trim($_POST['recipe_url']));
            } else {
                $recipe_url = null;
            }

            if (isset($_POST['image_url']) && trim($_POST['image_url']) != '') {
                $image_url = $mysqli->real_escape_string(trim($_POST['image_url']));
            } else {
                $image_url = null;
            }

            if (isset($_POST['description']) && trim($_POST['description']) != '') {
                $description = $mysqli->real_escape_string(trim($_POST['description']));
            } else {
                $description = null;
            }

            $ingredients_string = "";
            for ($i = 1; $i <= $_POST['ingredientsCount']; $i++) {
                $ingredientString = 'ingredient_' . $i;
                if (isset($_POST[$ingredientString]) && trim($_POST[$ingredientString]) != '') {
                    $ingredientString = trim($_POST[$ingredientString]);
                    $ingredients_string = $ingredients_string . $ingredientString . " | ";
                }
            }
            $ingredients_string = $mysqli->real_escape_string($ingredients_string);

            // echo $ingredients_string;
            // echo "<hr>";

            $methods_string = "";
            for ($i = 1; $i <= $_POST['methodsCount']; $i++) {
                $methodString = 'method_' . $i;
                if (isset($_POST[$methodString]) && trim($_POST[$methodString]) != '') {
                    $methodString = trim($_POST[$methodString]);
                    $methods_string = $methods_string . $methodString . " | ";
                }
            }
            $methods_string = $mysqli->real_escape_string($methods_string);

            // echo $methods_string;
            // echo "<hr>";

            // Create author in author table
            if ($author_name != null) {
                $sql_author = "INSERT INTO author(author_name) VALUES('$author_name');";
                // echo $sql_author;
                $results_author = $mysqli->query($sql_author);

                if (!$results_author) {
                    echo $mysqli->error;
                    $mysqli->close();
                    exit();
                }

                $author_id = $mysqli->insert_id;
            }

            // Update recipe
            $sql = "INSERT INTO recipe (recipe_name, url, image_url, description, author_id, ingredients, method, food_type_id)
            VALUES ('$recipe_name', '$recipe_url', '$image_url', '$description', $author_id, '$ingredients_string', '$methods_string', $food_type_id);";

            // echo $sql;
            // echo "<hr>";

            $results = $mysqli->query($sql);

            if (!$results) {
                echo $mysqli->error;
                $mysqli->close();
                exit();
            }

            $mysqli->close();
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Edit Recipe Confirmation | Christmas Recipes</title>
        <link rel="stylesheet" href="style.css">
    </head>
    <body>
        <div id="header">
            <h2>Christmas Recipes for <span id="strike">the Holiday Season</span> Any Season!</h2>
        </div> <!-- #header -->

        <div id="navbar">
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="search.php">Search for Recipes</a></li>
                <li><a href="add_recipe.php">Add a Recipe</a></li>
                <li><a href="contact_me.php">Contact Me</a></li>
            </ul>
        </div> <!-- #navbar -->

        <div id="container">
            <div id="center-box">
                <div class="title">
                    <?php if (isset($error)) : ?>
                        <?php echo $error;?>
                    <?php else : ?>
                        Add was successful!
                    <?php endif; ?>
                </div>
                <?php if (isset($error)) : ?>
                    <form action="add_recipe.php">
                        <input id="edit-recipe" type="submit" value="Go back to recipe add form">
                    </form>
                <?php else : ?>
                    <form action="search.php">
                        <input id="edit-recipe" type="submit" value="Go to search form">
                    </form>
                <?php endif; ?>
            </div> <!-- #center-box -->
        </div> <!-- #container -->

        <div id="footer">
            Website created by Denis Mac.
        </div> <!-- #footer -->
    </body>
</html>