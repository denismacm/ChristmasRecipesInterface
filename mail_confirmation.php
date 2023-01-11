<?php 
    if (!isset($_POST['recipient']) || trim($_POST['recipient']) == '') {
        $error = "Invalid mail submission.";
    } else {
        $recipient = trim($_POST['recipient']);
        $subject = "Christmas Recipe: ";
        $message = "This following email is about a Christmas recipe.<hr>";
        if (isset($_POST['mail_image_url']) && trim($_POST['mail_image_url']) != '') {
            $mail_image_url = trim($_POST['mail_image_url']);
            $message = $message . "<img src='$mail_image_url' alt='Photo of Christmas Recipe'/><br>";
        }
        if (isset($_POST['mail_recipe_name']) && trim($_POST['mail_recipe_name']) != '') {
            $mail_recipe_name = trim($_POST['mail_recipe_name']);
            $subject = $subject . $mail_recipe_name;
            $message = $message . "The name of the food is called <strong>" . $mail_recipe_name . "</strong>. ";
        }
        if (isset($_POST['mail_author_name']) && trim($_POST['mail_author_name']) != '') {
            $mail_author_name = trim($_POST['mail_author_name']);
            $message = $message . "The recipe below is wrriten by an author named <strong>" . $mail_author_name . "</strong>. ";
        } 
        if (isset($_POST['mail_food_type_name']) && trim($_POST['mail_food_type_name']) != '') {
            $mail_food_type_name = trim($_POST['mail_food_type_name']);
            $message = $message . "The recipe is classified as a(n) <strong>" . $mail_food_type_name . "</strong>. ";
        } 
        if (isset($_POST['mail_description']) && trim($_POST['mail_description']) != '') {
            $mail_description = trim($_POST['mail_description']);
            $message = $message . $mail_description;
        }
        $message = $message . "<hr>";
        
        if (isset($_POST['mail_ingredients']) && trim($_POST['mail_ingredients']) != '') {
            $mail_ingredients = trim($_POST['mail_ingredients']);
            $ingredients_array = explode(' | ', $mail_ingredients);

            $message = $message . "Ingredients: <br>";
            $count = 1;
            foreach ($ingredients_array as $ingredient) {
                if (trim($ingredient) != "" && trim($ingredient) != "|") {
                    $message = $message . "$count) $ingredient <br>";
                    $count = $count + 1;
                }
            }
            $message = $message . "<br>";
        } 

        if (isset($_POST['mail_method']) && trim($_POST['mail_method']) != '') {
            $mail_method = trim($_POST['mail_method']);
            $methods_array = explode(' | ', $mail_method);
            $message = $message . "Methods: <br>";
            $count = 1;
            foreach ($methods_array as $method) {
                if (trim($method) != "" && trim($method) != "|") {
                    $message = $message . "$count) $method <br>";
                    $count = $count + 1;
                }
            }
            $message = $message . "<br>";
        }

        if (isset($_POST['mail_article_url']) && trim($_POST['mail_article_url']) != '') {
            $mail_article_url = trim($_POST['mail_article_url']);
            $message = $message . "A dedicated article link to this recipe is <a href='$mail_article_url' target='_blank'>here</a>.<br>";
        } 

        $message = $message . "<hr>";

        $message = $message . "Thank you for checking out the mail function!<br><br>";

        $message = $message . "Sincerely,<br>";
        $message = $message . "Denis Mac";

        $header = [
            "content-type" => "text/html",
            "from" => "dmac@usc.edu",
            "reply-to" => "dmac@usc.edu"
        ];

        // echo $message;
        // echo "<hr>";
        if ( !mail($recipient, $subject, $message, $header) ) {
            $error = "Mail did not go through.";
        }
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
                    <div class="title">Mail to <?php echo $recipient;?> is successful!</div>
                <?php endif; ?>
            </div> <!-- #center-box -->
        </div> <!-- #container -->

        <div id="footer">
            Website created by Denis Mac.
        </div> <!-- #footer -->
    </body>
</html>