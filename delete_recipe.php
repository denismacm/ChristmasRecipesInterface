<?php 
    if (!isset($_POST['recipe_id']) || trim($_POST['recipe_id']) == '') {
        $error = "Invalid form submission.";
    } else {
        $recipe_id = trim($_POST['recipe_id']);

        require 'config/config.php';
        $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if ( $mysqli->connect_errno ) {
            echo $mysqli->connect_error;
            exit();
        }
        $mysqli->set_charset('utf8');

        $sql_delete = "DELETE FROM recipe WHERE recipe_id = $recipe_id;";
        $result = $mysqli->query($sql_delete);

        if (!$result) {
            echo $mysqli->error;
            $mysqli->close();
            exit();
        }

        $mysqli->close();
    }
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Delete Recipe | Christmas Recipes</title>
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
                <?php if (isset($error)) : ?>
                    <div class="title"><?php echo $error;?></div>
                <?php else : ?>
                    <div class="title">Deletion is successful!</div>
                <?php endif; ?>
            </div> <!-- #center-box -->
        </div> <!-- #container -->

        <div id="footer">
            Website created by Denis Mac.
        </div> <!-- #footer -->
    </body>
</html>