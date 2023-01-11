<?php
    if (!isset($_GET['recipe_id']) || trim($_GET['recipe_id']) == '') {
        $error = "Error in URL retrieving recipe.";
    } else {
        require 'config/config.php';
        $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if ( $mysqli->connect_errno ) {
            echo $mysqli->connect_error;
            exit();
        }
        $mysqli->set_charset('utf8');

        $recipe_id = trim($_GET['recipe_id']);

        $sql_recipe_details = "SELECT recipe.recipe_id AS recipe_id, recipe.recipe_name AS recipe_name, recipe.url AS url, recipe.image_url AS image_url, recipe.description AS description, author.author_name AS author_name, recipe.ingredients AS ingredients, recipe.method AS method, food_type.food_type_name AS food_type_name
                                FROM recipe
                                    LEFT JOIN author
                                        ON recipe.author_id =  author.author_id
                                    LEFT JOIN food_type
                                        ON recipe.food_type_id = food_type.food_type_id
                                    WHERE recipe.recipe_id = $recipe_id;";

        $results_recipe_details = $mysqli->query($sql_recipe_details);

        if (!$results_recipe_details) {
            echo $mysqli->error;
			$mysqli->close();
			exit();
        }

        $row = $results_recipe_details->fetch_assoc();

        // Convert ingredients
        $ingredients_array = explode(' | ', $row['ingredients']);
        // var_dump($ingredients_array);
        // foreach ($ingredients_array as $ingredient) {
        //     if (trim($ingredient) != "" && trim($ingredient) != "|") {
        //         echo "$ingredient <br>";
        //     }
        // }

        // Convert methods
        $methods_array = explode(' | ', $row['method']);
        // var_dump($methods_array);
        // foreach ($methods_array as $method) {
        //     if (trim($method) != "" && trim($method) != "|") {
        //         echo "$method <br>";
        //     }
        // }

        $mysqli->close();
    }
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Recipe Details | Christmas Recipes</title>
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

        <div id="container<?php if (!isset($error)) echo '-long';?>">
            <div id="center-box">
                <div class="title">
                    <?php if (isset($error)) : ?>
                        <?php echo $error;?>
                    <?php else : ?>
                        <?php echo $row['recipe_name'];?>
                    <?php endif; ?>
                </div>
                <?php if (!isset($error)) : ?>
                    <br>
                    <table>
                        <tr>
                            <th>Image</th>
                            <!-- <td><img class="details-img" src="<?php echo $row['image_url'];?>" alt="Picture of <?php echo $row['recipe_name'];?>"> -->
                            <td>
                            <?php if ($row['image_url'] == null || trim($row['image_url']) == '') : ?>
                                No image available.
                            <?php else : ?>
                                <img class="details-img" src="<?php echo $row['image_url'];?>" alt="Picture of <?php echo $row['recipe_name'];?>">
                            <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <th>Author</th>
                            <td><?php echo $row['author_name'];?></td>
                        </tr>
                        <tr>
                            <th>Food Type</th>
                            <td><?php echo $row['food_type_name'];?></td>
                        </tr>
                        <tr>
                            <th>Article URL</th>
                            <td>
                            <?php if ($row['url'] == null || trim($row['url']) == '') : ?>
                                No article provided.
                            <?php else :?>
                                <a href="<?php echo $row['url']?>" target="_blank">Link</a>
                            <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <th>Description</th>
                            <td><?php echo $row['description'];?></td>
                        </tr>   
                        <tr>
                            <th>Ingredients</th>
                            <td>
                                <?php
                                    $count = 1;
                                    foreach ($ingredients_array as $ingredient) {
                                        if (trim($ingredient) != "" && trim($ingredient) != "|") {
                                            echo "$count) $ingredient <br>";
                                            $count = $count + 1;
                                        }
                                    }
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <th>Methods</th>
                            <td>
                                <?php
                                    $count = 1;
                                    foreach ($methods_array as $method) {
                                        if (trim($method) != "" && trim($method) != "|") {
                                            echo "$count) $method <br>";
                                            $count = $count + 1;
                                        }
                                    }
                                ?>
                            </td>
                        </tr>
                    </table>
                    <div id="center-flex-box">
                        <form id="mail-form" action="mail_confirmation.php" class="triple-btn" method="POST">
                            <input id="recipient" type="hidden" name="recipient" value="">
                            <input id="mail_recipe_name" type="hidden" name="mail_recipe_name" value="<?php echo $row['recipe_name'];?>">
                            <input id="mail_author_name" type="hidden" name="mail_author_name" value="<?php echo $row['author_name'];?>">
                            <input id="mail_food_type_name" type="hidden" name="mail_food_type_name" value="<?php echo $row['food_type_name'];?>">
                            <input id="mail_image_url" type="hidden" name="mail_image_url" value="<?php echo $row['image_url'];?>">
                            <input id="mail_article_url" type="hidden" name="mail_article_url" value="<?php echo $row['url'];?>">
                            <input id="mail_description" type="hidden" name="mail_description" value="<?php echo $row['description'];?>">
                            <input id="mail_ingredients" type="hidden" name="mail_ingredients" value="<?php echo $row['ingredients'];?>">
                            <input id="mail_method" type="hidden" name="mail_method" value="<?php echo $row['method']; ?>">
                            <input id="mail-recipe" type="submit" value="Mail this recipe">
                        </form>
                        <form class="triple-btn" action="edit_recipe.php" method="GET">
                            <input type="hidden" name="recipe_id" value=<?php echo $row['recipe_id']; ?>>
                            <input id="edit-recipe" type="submit" value="Edit this recipe">
                        </form>
                        <form class="triple-btn" action="delete_recipe.php" method="POST">
                            <input type="hidden" name="recipe_id" value=<?php echo $row['recipe_id']; ?>>
                            <input class="add-button" type="submit" value="Delete this recipe" onclick="return confirm('Are you sure you want to delete this recipe?')">
                        </form>
                    </div> <!-- #center-flex-box-->
                <?php endif; ?>
            </div> <!-- #center-box -->
        </div> <!-- #container -->

        <div id="footer">
            Website created by Denis Mac.
        </div> <!-- #footer -->
    <script>
        document.querySelector("#mail-form").onsubmit = () => {
            let emailAddress = prompt("Please enter your email address:", "ttrojan@usc.edu");
            document.querySelector("#recipient").value = emailAddress;
        }
    </script>
    </body>
    
</html>